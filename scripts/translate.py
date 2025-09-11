#!/usr/bin/env python3
"""
Argos Translate Script for Laravel Post Translation
Translates post content from Korean to other languages using Argos Translate
"""

import argparse
import json
import sys
import logging
import os
import time
from pathlib import Path

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

try:
    import argostranslate.package
    import argostranslate.translate
except ImportError:
    logger.error("argostranslate is not installed. Install it with: pip install argostranslate")
    sys.exit(1)

class PostTranslator:
    def __init__(self):
        self.supported_languages = {
            'eng': 'en',
            'chn': 'zh',
            'hin': 'hi',
            'arb': 'ar'
        }
        self.source_language = 'ko'  # Korean
        self.translation_cache = {}  # Cache translation functions
        self.initialize_translation_packages()
    
    def initialize_translation_packages(self):
        """Download and install required translation packages"""
        logger.info("Updating package index...")
        argostranslate.package.update_package_index()
        
        available_packages = argostranslate.package.get_available_packages()
        
        # Install Korean to English first (required for pivot)
        ko_en_package = self.find_package(available_packages, self.source_language, 'en')
        if ko_en_package and not self.is_package_installed(ko_en_package):
            logger.info(f"Installing translation package: {self.source_language} -> en")
            try:
                download_path = ko_en_package.download()
                argostranslate.package.install_from_path(download_path)
                logger.info(f"Successfully installed package for Korean -> English")
            except Exception as e:
                logger.error(f"Failed to install Korean -> English package: {e}")
        
        # Install English to target languages for pivot translation
        for target_lang_code, argos_code in self.supported_languages.items():
            if target_lang_code == 'eng':
                continue  # Already handled above
                
            # Try English to target language for pivot translation
            en_target_package = self.find_package(available_packages, 'en', argos_code)
            if en_target_package and not self.is_package_installed(en_target_package):
                logger.info(f"Installing pivot translation package: en -> {argos_code}")
                try:
                    download_path = en_target_package.download()
                    argostranslate.package.install_from_path(download_path)
                    logger.info(f"Successfully installed package for English -> {target_lang_code}")
                except Exception as e:
                    logger.error(f"Failed to install English -> {target_lang_code} package: {e}")
        
        # Check for direct Korean to target packages (rarely available but worth checking)
        for target_lang_code, argos_code in self.supported_languages.items():
            if target_lang_code == 'eng':
                continue
                
            package = self.find_package(available_packages, self.source_language, argos_code)
            if package and not self.is_package_installed(package):
                logger.info(f"Installing direct translation package: {self.source_language} -> {argos_code}")
                try:
                    download_path = package.download()
                    argostranslate.package.install_from_path(download_path)
                    logger.info(f"Successfully installed direct package for {target_lang_code}")
                except Exception as e:
                    logger.error(f"Failed to install direct package for {target_lang_code}: {e}")
    
    def find_package(self, packages, from_code, to_code):
        """Find a specific translation package"""
        for package in packages:
            if package.from_code == from_code and package.to_code == to_code:
                return package
        return None
    
    def is_package_installed(self, package):
        """Check if a package is already installed"""
        installed_packages = argostranslate.package.get_installed_packages()
        for installed in installed_packages:
            if (installed.from_code == package.from_code and 
                installed.to_code == package.to_code):
                return True
        return False
    
    def get_translation_function(self, target_language):
        """Get translation function for target language (with caching)"""
        cache_key = f"{self.source_language}->{target_language}"
        
        # Return cached function if available
        if cache_key in self.translation_cache:
            return self.translation_cache[cache_key]
        
        if target_language not in self.supported_languages:
            raise ValueError(f"Unsupported target language: {target_language}")
        
        argos_code = self.supported_languages[target_language]
        
        installed_languages = argostranslate.translate.get_installed_languages()
        
        from_lang = None
        to_lang = None
        
        for lang in installed_languages:
            if lang.code == self.source_language:
                from_lang = lang
            elif lang.code == argos_code:
                to_lang = lang
        
        if not from_lang:
            raise RuntimeError(f"Source language {self.source_language} not found")
        
        if not to_lang:
            raise RuntimeError(f"Target language {argos_code} not found")
        
        translation_func = from_lang.get_translation(to_lang)
        
        # Cache the function for future use
        self.translation_cache[cache_key] = translation_func
        
        return translation_func
    
    def translate_text(self, text, target_language):
        """Translate text to target language"""
        if not text or not text.strip():
            return text
        
        # Skip very short texts to improve performance
        if len(text.strip()) < 3:
            return text
        
        try:
            # For long texts, split into sentences for better performance
            if len(text) > 200:
                return self.translate_long_text(text, target_language)
            
            # Try direct translation first
            try:
                translation_func = self.get_translation_function(target_language)
                translated = translation_func.translate(text)
                logger.debug(f"Direct translation to {target_language}: {text[:30]}... -> {translated[:30]}...")
                return translated
            except RuntimeError as e:
                if "not found" in str(e):
                    # Fall back to pivot translation through English
                    logger.info(f"Direct translation not available for {target_language}, using pivot translation through English")
                    return self.pivot_translate_text(text, target_language)
                else:
                    raise e
        except Exception as e:
            logger.error(f"Translation failed for {target_language}: {e}")
            return text  # Return original text if translation fails
    
    def translate_long_text(self, text, target_language):
        """Translate long text by splitting into smaller chunks"""
        import re
        
        # For very long texts, split into smaller chunks
        if len(text) > 800:
            return self.translate_chunks(text, target_language)
        
        # Split text into sentences (Korean sentence endings)
        sentences = re.split(r'[.!?。！？]\s*', text)
        translated_sentences = []
        
        for i, sentence in enumerate(sentences):
            sentence = sentence.strip()
            if len(sentence) < 3:  # Skip very short segments
                if sentence:  # Only add non-empty segments
                    translated_sentences.append(sentence)
                continue
            
            logger.info(f"Translating sentence {i+1}/{len(sentences)}: {sentence[:50]}...")
            
            try:
                # Try direct translation first
                try:
                    translation_func = self.get_translation_function(target_language)
                    translated = translation_func.translate(sentence)
                    translated_sentences.append(translated)
                    logger.debug(f"Sentence translated: {sentence[:30]}... -> {translated[:30]}...")
                except RuntimeError as e:
                    if "not found" in str(e):
                        # Fall back to pivot translation
                        translated = self.pivot_translate_text(sentence, target_language)
                        translated_sentences.append(translated)
                        logger.debug(f"Pivot translated: {sentence[:30]}... -> {translated[:30]}...")
                    else:
                        raise e
            except Exception as e:
                logger.warning(f"Failed to translate sentence: {sentence[:30]}... Error: {e}")
                translated_sentences.append(sentence)  # Keep original if translation fails
        
        # Reconstruct the text
        result = '. '.join(filter(None, translated_sentences))
        if result and not result.endswith('.'):
            result += '.'
        
        return result
    
    def translate_chunks(self, text, target_language, chunk_size=150):
        """Translate very long text by splitting into fixed-size chunks"""
        chunks = []
        words = text.split()
        current_chunk = []
        current_length = 0
        
        for word in words:
            if current_length + len(word) + 1 > chunk_size and current_chunk:
                chunks.append(' '.join(current_chunk))
                current_chunk = [word]
                current_length = len(word)
            else:
                current_chunk.append(word)
                current_length += len(word) + 1
        
        if current_chunk:
            chunks.append(' '.join(current_chunk))
        
        logger.info(f"Splitting long text into {len(chunks)} chunks of ~{chunk_size} characters each")
        
        translated_chunks = []
        for i, chunk in enumerate(chunks):
            logger.info(f"Translating chunk {i+1}/{len(chunks)}: {chunk[:50]}...")
            
            try:
                # Try direct translation first
                try:
                    translation_func = self.get_translation_function(target_language)
                    translated = translation_func.translate(chunk)
                    translated_chunks.append(translated)
                except RuntimeError as e:
                    if "not found" in str(e):
                        # Fall back to pivot translation
                        translated = self.pivot_translate_text(chunk, target_language)
                        translated_chunks.append(translated)
                    else:
                        raise e
            except Exception as e:
                logger.warning(f"Failed to translate chunk {i+1}: {e}")
                translated_chunks.append(chunk)  # Keep original if translation fails
        
        return ' '.join(translated_chunks)
    
    def pivot_translate_text(self, text, target_language):
        """Translate text using English as pivot language (Korean -> English -> Target)"""
        try:
            # Step 1: Korean to English
            ko_to_en_func = self.get_translation_function('eng')
            english_text = ko_to_en_func.translate(text)
            logger.debug(f"Pivot step 1 (ko->en): {text[:30]}... -> {english_text[:30]}...")
            
            # Step 2: English to target language
            if target_language == 'eng':
                return english_text
            
            argos_code = self.supported_languages[target_language]
            installed_languages = argostranslate.translate.get_installed_languages()
            
            from_lang = None
            to_lang = None
            
            for lang in installed_languages:
                if lang.code == 'en':
                    from_lang = lang
                elif lang.code == argos_code:
                    to_lang = lang
            
            if not from_lang or not to_lang:
                raise RuntimeError(f"Pivot translation not possible: en or {argos_code} not found")
            
            en_to_target_func = from_lang.get_translation(to_lang)
            final_translation = en_to_target_func.translate(english_text)
            logger.debug(f"Pivot step 2 (en->{target_language}): {english_text[:30]}... -> {final_translation[:30]}...")
            
            return final_translation
        except Exception as e:
            logger.error(f"Pivot translation failed for {target_language}: {e}")
            raise e
    
    def translate_post(self, post_data, target_language):
        """Translate a post's content to target language"""
        translated_post = post_data.copy()
        
        # Fields to translate
        fields_to_translate = ['title', 'summary', 'content']
        
        for i, field in enumerate(fields_to_translate):
            if field in post_data and post_data[field]:
                logger.info(f"Translating {field} to {target_language} ({i+1}/{len(fields_to_translate)})...")
                original_text = post_data[field]
                text_length = len(original_text)
                logger.info(f"Text length: {text_length} characters")
                
                start_time = time.time()
                translated_text = self.translate_text(original_text, target_language)
                end_time = time.time()
                
                translated_post[field] = translated_text
                logger.info(f"Completed {field} translation in {end_time - start_time:.2f} seconds")
        
        # Update language field
        translated_post['language'] = target_language
        
        return translated_post

def main():
    parser = argparse.ArgumentParser(description='Translate Laravel posts using Argos Translate')
    parser.add_argument('--input', required=True, help='Input JSON file with post data')
    parser.add_argument('--output', required=True, help='Output JSON file for translated data')
    parser.add_argument('--target-language', required=True, 
                       choices=['eng', 'chn', 'hin', 'arb'],
                       help='Target language for translation')
    parser.add_argument('--verbose', '-v', action='store_true', help='Enable verbose logging')
    
    args = parser.parse_args()
    
    if args.verbose:
        logging.getLogger().setLevel(logging.DEBUG)
    
    # Check if input file exists
    if not os.path.exists(args.input):
        logger.error(f"Input file not found: {args.input}")
        sys.exit(1)
    
    try:
        # Read input data
        with open(args.input, 'r', encoding='utf-8') as f:
            post_data = json.load(f)
        
        logger.info(f"Loaded post data from {args.input}")
        
        # Initialize translator
        translator = PostTranslator()
        
        # Translate post
        translated_post = translator.translate_post(post_data, args.target_language)
        
        # Ensure output directory exists
        output_dir = os.path.dirname(args.output)
        if output_dir:
            os.makedirs(output_dir, exist_ok=True)
        
        # Write translated data
        with open(args.output, 'w', encoding='utf-8') as f:
            json.dump(translated_post, f, ensure_ascii=False, indent=2)
        
        logger.info(f"Translation completed. Output saved to {args.output}")
        
        # Output success status for Laravel
        print(json.dumps({
            'status': 'success',
            'message': f'Post translated to {args.target_language}',
            'output_file': args.output
        }))
        
    except Exception as e:
        logger.error(f"Translation failed: {e}")
        print(json.dumps({
            'status': 'error',
            'message': str(e)
        }))
        sys.exit(1)

if __name__ == '__main__':
    main()
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Preview</title>
    @vite(['resources/css/app.css'])
    <style>
        /* 사이즈 클래스 정의 */
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        .text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
        .text-5xl { font-size: 3rem; line-height: 1; }
        .text-6xl { font-size: 3.75rem; line-height: 1; }
        .text-7xl { font-size: 4.5rem; line-height: 1; }
        
        @media (min-width: 768px) {
            .md\:text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
            .md\:text-5xl { font-size: 3rem; line-height: 1; }
            .md\:text-6xl { font-size: 3.75rem; line-height: 1; }
        }
        
        @media (min-width: 1024px) {
            .lg\:text-6xl { font-size: 3.75rem; line-height: 1; }
            .lg\:text-7xl { font-size: 4.5rem; line-height: 1; }
        }
    </style>
</head>
<body class="m-0 p-0" style="overflow: hidden;">
    <div id="hero-container" class="relative h-[500px] bg-gray-100 overflow-hidden">
        <!-- 배경 -->
        <div class="absolute inset-0">
            <!-- 배경 이미지 -->
            <img id="background-image" 
                 src="" 
                 alt="Hero Background"
                 class="w-full h-full object-cover hidden">
            
            <!-- 배경 비디오 -->
            <video id="background-video" 
                   autoplay loop muted playsinline 
                   class="w-full h-full object-cover hidden">
                <source src="" type="video/mp4">
            </video>
            
            <!-- 오버레이 -->
            <div id="overlay" class="absolute inset-0"></div>
        </div>
        
        <!-- 콘텐츠 -->
        <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-20">
            <div id="content-container" class="flex items-center h-full justify-start">
                <div id="text-container" class="w-full md:w-2/3 lg:w-1/2 text-left">
                    <!-- 부제목 (위) -->
                    <p id="subtitle-above" class="uppercase tracking-wider mb-2 text-sm text-gray-500">여기에 부제목이 표시됩니다</p>
                    
                    <!-- 제목 -->
                    <h1 id="title" class="font-bold mb-4 text-4xl md:text-5xl lg:text-6xl text-gray-900">Hero 제목을 입력하세요</h1>
                    
                    <!-- 부제목 (아래) -->
                    <p id="subtitle-below" class="uppercase tracking-wider mb-2 text-sm text-gray-500" style="display: none;">여기에 부제목이 표시됩니다</p>
                    
                    <!-- 설명 -->
                    <p id="description" class="mb-8 leading-relaxed text-lg text-gray-600">여기에 설명 텍스트가 표시됩니다. 슬라이드에 대한 상세한 설명을 입력할 수 있습니다.</p>
                    
                    <!-- 버튼 -->
                    <a id="button" 
                       href="#" 
                       class="inline-flex items-center px-8 py-3 rounded-full transition-all duration-300 hover:opacity-80 border-2 bg-blue-500 text-white border-blue-500">버튼 텍스트</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 메시지 리스너
        window.addEventListener('message', function(event) {
            const data = event.data;
            if (data.type !== 'hero-preview-update') return;
            
            updatePreview(data.data);
        });

        // 파일 입력 변경 감지를 위한 함수
        function handleFilePreview(file, type) {
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                if (type === 'image') {
                    const bgImage = document.getElementById('background-image');
                    const bgVideo = document.getElementById('background-video');
                    
                    bgImage.src = e.target.result;
                    bgImage.classList.remove('hidden');
                    bgVideo.classList.add('hidden');
                } else if (type === 'video') {
                    const bgImage = document.getElementById('background-image');
                    const bgVideo = document.getElementById('background-video');
                    const videoSource = bgVideo.querySelector('source');
                    
                    videoSource.src = e.target.result;
                    bgVideo.load();
                    bgVideo.classList.remove('hidden');
                    bgImage.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        }

        function updatePreview(data) {
            // 제목
            const titleEl = document.getElementById('title');
            if (data.title !== undefined) {
                titleEl.textContent = data.title || 'Hero 제목을 입력하세요';
                titleEl.style.color = data.titleColor || '#1F2937';
                
                // 사이즈 매핑
                const titleSizeMap = {
                    'text-3xl': 'text-3xl md:text-4xl',
                    'text-4xl': 'text-4xl md:text-5xl',
                    'text-5xl': 'text-4xl md:text-5xl lg:text-6xl',
                    'text-6xl': 'text-5xl md:text-6xl lg:text-7xl'
                };
                const titleSize = titleSizeMap[data.titleSize] || 'text-4xl md:text-5xl lg:text-6xl';
                titleEl.className = `font-bold mb-4 ${titleSize}`;
            }
            
            // 부제목
            const subtitleAboveEl = document.getElementById('subtitle-above');
            const subtitleBelowEl = document.getElementById('subtitle-below');
            const subtitlePosition = data.subtitlePosition || 'above';
            
            if (data.subtitle !== undefined) {
                const subtitleText = data.subtitle || '여기에 부제목이 표시됩니다';
                const subtitleColor = data.subtitleColor || '#6B7280';
                const subtitleClass = `uppercase tracking-wider mb-2 ${data.subtitleSize || 'text-sm'}`;
                
                if (subtitlePosition === 'below') {
                    // 제목 아래에 표시
                    subtitleAboveEl.style.display = 'none';
                    subtitleBelowEl.textContent = subtitleText;
                    subtitleBelowEl.style.color = subtitleColor;
                    subtitleBelowEl.className = subtitleClass;
                    subtitleBelowEl.style.display = 'block';
                } else {
                    // 제목 위에 표시 (기본값)
                    subtitleBelowEl.style.display = 'none';
                    subtitleAboveEl.textContent = subtitleText;
                    subtitleAboveEl.style.color = subtitleColor;
                    subtitleAboveEl.className = subtitleClass;
                    subtitleAboveEl.style.display = 'block';
                }
            }
            
            // 설명
            const descriptionEl = document.getElementById('description');
            if (data.description !== undefined) {
                descriptionEl.textContent = data.description || '여기에 설명 텍스트가 표시됩니다. 슬라이드에 대한 상세한 설명을 입력할 수 있습니다.';
                descriptionEl.style.color = data.descriptionColor || '#4B5563';
                descriptionEl.className = `mb-8 leading-relaxed ${data.descriptionSize || 'text-lg'}`;
            }
            
            // 버튼
            const buttonEl = document.getElementById('button');
            if (data.buttonText !== undefined) {
                buttonEl.textContent = data.buttonText || '버튼 텍스트';
                if (data.buttonStyle === 'outline') {
                    buttonEl.style.color = data.buttonTextColor || '#FFFFFF';
                    buttonEl.style.backgroundColor = 'transparent';
                    buttonEl.style.borderColor = data.buttonTextColor || '#FFFFFF';
                } else {
                    buttonEl.style.color = data.buttonTextColor || '#FFFFFF';
                    buttonEl.style.backgroundColor = data.buttonBgColor || '#3B82F6';
                    buttonEl.style.borderColor = data.buttonBgColor || '#3B82F6';
                }
            }
            
            // 오버레이
            const overlayEl = document.getElementById('overlay');
            if (data.overlayEnabled !== false) {
                const overlayOpacity = data.overlayOpacity || 60;
                const opacity1 = Math.round(overlayOpacity * 2.55 * 0.8).toString(16).padStart(2, '0');
                const opacity2 = Math.round(overlayOpacity * 2.55 * 0.5).toString(16).padStart(2, '0');
                overlayEl.style.background = `linear-gradient(to right, ${data.overlayColor || '#000000'}${opacity1}, ${data.overlayColor || '#000000'}${opacity2}, transparent)`;
                overlayEl.style.display = 'block';
            } else {
                overlayEl.style.display = 'none';
            }
            
            // 정렬
            const contentContainer = document.getElementById('content-container');
            const textContainer = document.getElementById('text-container');
            
            const alignmentClasses = {
                'left': 'justify-start',
                'center': 'justify-center',
                'right': 'justify-end'
            };
            
            const alignmentClass = alignmentClasses[data.contentAlignment || 'left'];
            contentContainer.className = `flex items-center h-full ${alignmentClass}`;
            
            if (data.contentAlignment === 'center') {
                textContainer.classList.add('text-center');
                textContainer.classList.remove('text-left');
            } else {
                textContainer.classList.remove('text-center');
                textContainer.classList.add('text-left');
            }
            
            // 배경 타입 처리
            let hasBackground = false;
            if (data.backgroundType) {
                const bgImage = document.getElementById('background-image');
                const bgVideo = document.getElementById('background-video');
                
                if (data.backgroundType === 'video') {
                    bgImage.classList.add('hidden');
                    // 비디오 URL이 있으면 표시
                    if (data.backgroundVideoUrl) {
                        const videoSource = bgVideo.querySelector('source');
                        videoSource.src = data.backgroundVideoUrl;
                        bgVideo.load();
                        bgVideo.classList.remove('hidden');
                        hasBackground = true;
                    }
                } else {
                    bgVideo.classList.add('hidden');
                    // 이미지 URL이 있으면 표시
                    if (data.backgroundImageUrl) {
                        bgImage.src = data.backgroundImageUrl;
                        bgImage.classList.remove('hidden');
                        hasBackground = true;
                    }
                }
            }
            
            // 파일 객체가 전달된 경우 처리
            if (data.backgroundImageFile) {
                handleFilePreview(data.backgroundImageFile, 'image');
                hasBackground = true;
            }
            if (data.backgroundVideoFile) {
                handleFilePreview(data.backgroundVideoFile, 'video');
                hasBackground = true;
            }
            
            // 배경 유무에 따라 기본 텍스트 색상 조정
            if (!hasBackground && !data.titleColor) {
                titleEl.style.color = '#1F2937'; // gray-900
            }
            if (!hasBackground && !data.subtitleColor) {
                subtitleEl.style.color = '#6B7280'; // gray-500
            }
            if (!hasBackground && !data.descriptionColor) {
                descriptionEl.style.color = '#4B5563'; // gray-600
            }
        }
        
        // 초기 로드 시 기본 텍스트 표시
        document.addEventListener('DOMContentLoaded', function() {
            // 기본 상태로 초기화
            updatePreview({});
        });
        
        // 초기 메시지 전송 요청
        window.parent.postMessage({ type: 'hero-preview-ready' }, '*');
    </script>
</body>
</html>

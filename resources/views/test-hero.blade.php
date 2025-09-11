<!DOCTYPE html>
<html>
<head>
    <title>Hero Translation Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section { margin: 30px 0; }
        .raw-data { background: #f5f5f5; padding: 10px; margin: 10px 0; }
        pre { white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>Hero Translation Debug</h1>
    
    <div class="section">
        <h2>Current Locale Information</h2>
        <p><strong>Session Locale:</strong> {{ session('locale', 'not set') }}</p>
        <p><strong>App Locale:</strong> {{ app()->getLocale() }}</p>
    </div>
    
    <div class="section">
        <h2>Raw Database Values</h2>
        <div class="raw-data">
            <p><strong>ID:</strong> {{ $hero->id }}</p>
            <p><strong>title:</strong> {{ $hero->title ?? '(null)' }}</p>
            <p><strong>subtitle:</strong> {{ $hero->subtitle ?? '(null)' }}</p>
            <p><strong>description:</strong> {{ $hero->description ?? '(null)' }}</p>
            <p><strong>button_text:</strong> {{ $hero->button_text ?? '(null)' }}</p>
        </div>
        
        <div class="raw-data">
            <p><strong>title_translations:</strong></p>
            <pre>{{ json_encode($hero->title_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            
            <p><strong>subtitle_translations:</strong></p>
            <pre>{{ json_encode($hero->subtitle_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            
            <p><strong>description_translations:</strong></p>
            <pre>{{ json_encode($hero->description_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            
            <p><strong>button_text_translations:</strong></p>
            <pre>{{ json_encode($hero->button_text_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
    
    <div class="section">
        <h2>Translation Method Results</h2>
        <table>
            <tr>
                <th>Language</th>
                <th>getTitle()</th>
                <th>getSubtitle()</th>
                <th>getDescription()</th>
                <th>getButtonText()</th>
                <th>hasTranslation()</th>
            </tr>
            @foreach($languages as $lang)
            <tr>
                <td>{{ $lang }}</td>
                <td>{{ $hero->getTitle($lang) ?? '(null)' }}</td>
                <td>{{ $hero->getSubtitle($lang) ?? '(null)' }}</td>
                <td>{{ $hero->getDescription($lang) ?? '(null)' }}</td>
                <td>{{ $hero->getButtonText($lang) ?? '(null)' }}</td>
                <td>{{ $hero->hasTranslation($lang) ? 'Yes' : 'No' }}</td>
            </tr>
            @endforeach
        </table>
    </div>
    
    <div class="section">
        <h2>Default Method Results (Current Locale)</h2>
        <p><strong>getTitle():</strong> {{ $hero->getTitle() ?? '(null)' }}</p>
        <p><strong>getSubtitle():</strong> {{ $hero->getSubtitle() ?? '(null)' }}</p>
        <p><strong>getDescription():</strong> {{ $hero->getDescription() ?? '(null)' }}</p>
        <p><strong>getButtonText():</strong> {{ $hero->getButtonText() ?? '(null)' }}</p>
    </div>
    
    <div class="section">
        <h2>Available Languages</h2>
        <p>{{ $hero->getAvailableLanguages()->join(', ') ?: 'None' }}</p>
    </div>
</body>
</html>
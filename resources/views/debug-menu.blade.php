<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>메뉴 데이터 디버그</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
        }
        pre {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
        .menu-item {
            margin: 10px 0;
            padding: 10px;
            background: #e8f4f8;
            border-left: 4px solid #0066cc;
        }
        .group {
            margin: 10px 0 10px 20px;
            padding: 10px;
            background: #f0f8ff;
            border-left: 4px solid #4a90e2;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .success {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>메뉴 데이터 구조 디버그</h1>
        
        @php
            $headerMenu = get_option('header_menu', []);
        @endphp
        
        <h2>1. Raw Data from Database (JSON)</h2>
        <pre>{{ json_encode($headerMenu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        
        <h2>2. 메뉴 구조 분석</h2>
        @foreach($headerMenu as $index => $menu)
            <div class="menu-item">
                <h3>메뉴 #{{ $index + 1 }}: {{ $menu['label'] ?? 'NO LABEL' }}</h3>
                <p><strong>ID:</strong> {{ $menu['id'] ?? 'NO ID' }}</p>
                <p><strong>Type:</strong> {{ $menu['type'] ?? 'NO TYPE' }}</p>
                <p><strong>URL:</strong> {{ $menu['url'] ?? 'NO URL' }}</p>
                <p><strong>Active:</strong> {{ isset($menu['active']) ? ($menu['active'] ? 'Yes' : 'No') : 'NOT SET' }}</p>
                
                @if($menu['type'] === 'mega' && isset($menu['groups']))
                    <h4>메가 메뉴 그룹 (총 {{ count($menu['groups']) }}개)</h4>
                    @foreach($menu['groups'] as $groupIndex => $group)
                        <div class="group">
                            <h5>그룹 #{{ $groupIndex + 1 }}</h5>
                            <p><strong>그룹 라벨:</strong> 
                                @if(isset($group['label']))
                                    <span class="success">"{{ $group['label'] }}" (label field)</span>
                                @elseif(isset($group['group_label']))
                                    <span class="success">"{{ $group['group_label'] }}" (group_label field)</span>
                                @else
                                    <span class="error">NOT SET</span>
                                @endif
                            </p>
                            <p><strong>아이템 수:</strong> {{ isset($group['items']) ? count($group['items']) : 0 }}</p>
                            
                            @if(isset($group['items']))
                                <h6>아이템 목록:</h6>
                                <ul>
                                    @foreach($group['items'] as $item)
                                        <li>
                                            {{ $item['label'] ?? 'NO LABEL' }} 
                                            ({{ $item['url'] ?? 'NO URL' }})
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                @endif
                
                @if($menu['type'] === 'dropdown' && isset($menu['children']))
                    <h4>드롭다운 아이템 (총 {{ count($menu['children']) }}개)</h4>
                    <ul>
                        @foreach($menu['children'] as $child)
                            <li>
                                {{ $child['label'] ?? 'NO LABEL' }} 
                                ({{ $child['url'] ?? 'NO URL' }})
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
        
        <h2>3. 데이터 검증</h2>
        @php
            $issues = [];
            foreach($headerMenu as $index => $menu) {
                if (!isset($menu['id'])) {
                    $issues[] = "메뉴 #{$index}: ID가 없습니다";
                }
                if (!isset($menu['label'])) {
                    $issues[] = "메뉴 #{$index}: 라벨이 없습니다";
                }
                if (!isset($menu['type'])) {
                    $issues[] = "메뉴 #{$index}: 타입이 없습니다";
                }
                
                if ($menu['type'] === 'mega' && isset($menu['groups'])) {
                    foreach($menu['groups'] as $gIndex => $group) {
                        if (!isset($group['label']) && !isset($group['group_label'])) {
                            $issues[] = "메뉴 #{$index} > 그룹 #{$gIndex}: 그룹 라벨이 없습니다";
                        }
                    }
                }
            }
        @endphp
        
        @if(count($issues) > 0)
            <div class="error">
                <h4>발견된 문제점:</h4>
                <ul>
                    @foreach($issues as $issue)
                        <li>{{ $issue }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="success">
                <p>데이터 구조에 문제가 없습니다!</p>
            </div>
        @endif
        
        <h2>4. navigation.blade.php에서 사용되는 필드 매핑</h2>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>데이터베이스 필드</th>
                <th>Blade 템플릿 매핑</th>
                <th>설명</th>
            </tr>
            <tr>
                <td>$group['label'] 또는 $group['group_label']</td>
                <td>$column['title']</td>
                <td>메가 메뉴 그룹 제목</td>
            </tr>
            <tr>
                <td>$group['items']</td>
                <td>$column['items']</td>
                <td>그룹 내 아이템 목록</td>
            </tr>
            <tr>
                <td>$item['label']</td>
                <td>$subItem['title']</td>
                <td>개별 메뉴 아이템 제목</td>
            </tr>
            <tr>
                <td>$item['url']</td>
                <td>$subItem['url']</td>
                <td>메뉴 아이템 URL</td>
            </tr>
        </table>
    </div>
</body>
</html>
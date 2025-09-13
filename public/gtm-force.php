<?php
// GTM Preview Force Connection for Main Page
header('Content-Type: text/html; charset=utf-8');

// Set GTM preview cookies
setcookie('gtm_preview', 'GTM-N8GJF2QW', time() + 3600, '/', '.ai-med.co.kr', true, false);
setcookie('gtm_debug', 'x', time() + 3600, '/', '.ai-med.co.kr', true, false);
setcookie('gtm_cookies_win', 'x', time() + 3600, '/', '.ai-med.co.kr', true, false);

// Check if we should redirect to main page
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : false;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GTM Force Connection</title>
    
    <!-- GTM without any conditions -->
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'event': 'gtm.init',
            'gtm.start': new Date().getTime()
        });
    </script>
    
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-N8GJF2QW');</script>
    
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .status {
            padding: 20px;
            background: #f0f9ff;
            border-radius: 10px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .success {
            color: #22c55e;
            font-weight: bold;
        }
        .code {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- GTM noscript -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N8GJF2QW"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    
    <div class="container">
        <h1>🚀 GTM Force Connection</h1>
        
        <div class="status">
            <p class="success">✅ GTM Preview 쿠키가 설정되었습니다!</p>
            <div class="code">
                gtm_preview = GTM-N8GJF2QW<br>
                gtm_debug = x<br>
                gtm_cookies_win = x
            </div>
        </div>
        
        <p>이제 GTM Preview 모드로 연결할 수 있습니다.</p>
        
        <div>
            <a href="/" class="btn">메인 페이지로 이동</a>
            <a href="/gtm-debug.html" class="btn">디버그 페이지로 이동</a>
        </div>
        
        <div style="margin-top: 30px; padding: 20px; background: #fef3c7; border-radius: 10px;">
            <h3>📋 사용 방법:</h3>
            <ol style="text-align: left;">
                <li>이 페이지를 먼저 방문 (완료)</li>
                <li>GTM에서 "미리보기" 클릭</li>
                <li>URL에 <strong>https://ai-med.co.kr/gtm-force.php</strong> 입력</li>
                <li>연결 성공 후 메인 페이지로 이동</li>
            </ol>
        </div>
    </div>
    
    <script>
        // Check GTM status
        window.addEventListener('load', function() {
            if (typeof google_tag_manager !== 'undefined') {
                console.log('✅ GTM Loaded Successfully');
                console.log('Container:', Object.keys(google_tag_manager));
                
                // Send test event
                window.dataLayer.push({
                    'event': 'gtm_force_connected',
                    'timestamp': new Date().toISOString()
                });
            }
            
            <?php if ($redirect): ?>
            // Auto redirect after 3 seconds
            setTimeout(function() {
                window.location.href = '/';
            }, 3000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
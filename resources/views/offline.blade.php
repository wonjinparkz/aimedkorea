<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>오프라인 - AimedKorea</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
        }
        
        .offline-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        
        .icon-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            position: relative;
        }
        
        .wifi-icon {
            width: 100%;
            height: 100%;
            stroke: #9ca3af;
            stroke-width: 2;
            fill: none;
        }
        
        .cross-line {
            position: absolute;
            width: 100%;
            height: 3px;
            background: #ef4444;
            top: 50%;
            left: 0;
            transform: rotate(-45deg);
            transform-origin: center;
        }
        
        h1 {
            color: #1f2937;
            font-size: 28px;
            margin-bottom: 16px;
            font-weight: 700;
        }
        
        p {
            color: #6b7280;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .button-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .button {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }
        
        .button-primary {
            background-color: #3b82f6;
            color: white;
        }
        
        .button-primary:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }
        
        .button-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .button-secondary:hover {
            background-color: #e5e7eb;
        }
        
        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #fef3c7;
            color: #92400e;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #f59e0b;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.5;
                transform: scale(1.2);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @media (max-width: 480px) {
            .offline-container {
                padding: 30px 20px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            p {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="icon-container">
            <svg class="wifi-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z"/>
            </svg>
            <div class="cross-line"></div>
        </div>
        
        <h1>인터넷 연결 끊김</h1>
        
        <p>
            현재 오프라인 상태입니다.<br>
            인터넷 연결을 확인한 후 다시 시도해주세요.
        </p>
        
        <div class="button-group">
            <button onclick="window.location.reload()" class="button button-primary">
                다시 시도
            </button>
            <button onclick="goBack()" class="button button-secondary">
                이전 페이지
            </button>
        </div>
        
        <div class="status-indicator">
            <span class="status-dot"></span>
            연결 상태 확인 중...
        </div>
    </div>
    
    <script>
        // 온라인 상태 감지
        window.addEventListener('online', () => {
            console.log('인터넷 연결이 복구되었습니다.');
            // 자동으로 새로고침 (선택사항)
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        });
        
        // 이전 페이지로 돌아가기
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '/';
            }
        }
        
        // 주기적으로 연결 상태 확인
        setInterval(() => {
            if (navigator.onLine) {
                window.location.reload();
            }
        }, 5000);
    </script>
</body>
</html>
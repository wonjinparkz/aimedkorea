{{-- Google Analytics for Search Tracking --}}
@if(config('app.env') === 'production')
<script async src="https://www.googletagmanager.com/gtag/js?id=YOUR_GA_ID"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'YOUR_GA_ID');
</script>
@endif

{{-- Search Event Logging --}}
<script>
    // 검색 이벤트 로깅 함수
    window.logSearchEvent = function(eventType, data) {
        const logData = {
            timestamp: new Date().toISOString(),
            event: eventType,
            ...data,
            userAgent: navigator.userAgent,
            platform: navigator.platform,
            url: window.location.href
        };
        
        // 콘솔 로그
        console.log('[SearchAnalytics]', logData);
        
        // 서버로 로그 전송 (선택적)
        if (window.location.hostname !== 'localhost') {
            fetch('/api/log-search-event', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(logData)
            }).catch(err => console.error('Failed to log search event:', err));
        }
        
        // GA 이벤트 전송
        if (typeof gtag !== 'undefined') {
            gtag('event', eventType, {
                'event_category': 'search',
                'event_label': data.query || '',
                'value': data.results_count || 0
            });
        }
    };
    
    // 키보드 단축키 감지 로깅
    document.addEventListener('keydown', function(e) {
        const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
        const isCtrlOrCmd = isMac ? e.metaKey : e.ctrlKey;
        
        if (isCtrlOrCmd && e.key === '/') {
            window.logSearchEvent('search_shortcut_triggered', {
                shortcut: isMac ? 'Cmd+/' : 'Ctrl+/',
                source: 'keyboard'
            });
        }
    });
</script>
// Service Worker for AimedKorea PWA
const CACHE_NAME = 'aimedkorea-v1';
const urlsToCache = [
    '/',
    '/offline'
];

// Install event - cache assets
self.addEventListener('install', event => {
    console.log('[Service Worker] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('[Service Worker] Caching app shell');
                return cache.addAll(urlsToCache);
            })
            .then(() => {
                console.log('[Service Worker] Install completed');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('[Service Worker] Cache failed:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('[Service Worker] Activating...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME && cacheName.startsWith('aimedkorea-')) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            console.log('[Service Worker] Claiming clients');
            return self.clients.claim();
        })
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Skip cross-origin requests
    if (!event.request.url.startsWith(self.location.origin)) {
        return;
    }

    // Skip admin panel requests
    if (event.request.url.includes('/admin') || event.request.url.includes('/livewire')) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Cache hit - return response
                if (response) {
                    return response;
                }

                // Clone the request
                const fetchRequest = event.request.clone();

                return fetch(fetchRequest).then(response => {
                    // Check if valid response
                    if (!response || response.status !== 200 || response.type !== 'basic') {
                        return response;
                    }

                    // Clone the response
                    const responseToCache = response.clone();

                    // Cache the response for future use
                    caches.open(CACHE_NAME)
                        .then(cache => {
                            // Only cache successful responses
                            if (event.request.url.includes('/api/') || 
                                event.request.url.includes('.json') ||
                                event.request.url.includes('/surveys/')) {
                                // Don't cache API responses or dynamic content
                                return;
                            }
                            cache.put(event.request, responseToCache);
                        });

                    return response;
                }).catch(() => {
                    // Network request failed, try to get from cache
                    if (event.request.destination === 'document') {
                        // Return offline page for navigation requests
                        return caches.match('/offline');
                    }
                    
                    // For other requests, return a fallback if available
                    if (event.request.destination === 'image') {
                        return caches.match('/images/offline-placeholder.png');
                    }
                });
            })
    );
});

// Background sync for survey responses
self.addEventListener('sync', event => {
    console.log('[Service Worker] Sync event:', event.tag);
    
    if (event.tag === 'sync-survey-responses') {
        event.waitUntil(syncSurveyResponses());
    }
});

// Push notification handling
self.addEventListener('push', event => {
    console.log('[Service Worker] Push event received');
    
    const options = {
        body: event.data ? event.data.text() : '새로운 알림이 있습니다.',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/badge-72x72.png',
        vibrate: [200, 100, 200],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: '자세히 보기',
                icon: '/images/icons/checkmark.png'
            },
            {
                action: 'close',
                title: '닫기',
                icon: '/images/icons/xmark.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('AimedKorea', options)
    );
});

// Notification click handling
self.addEventListener('notificationclick', event => {
    console.log('[Service Worker] Notification click:', event.action);
    
    event.notification.close();

    if (event.action === 'explore') {
        // Open the app and navigate to specific page
        event.waitUntil(
            clients.openWindow('/notifications')
        );
    } else {
        // Just open the app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Helper function to sync survey responses
async function syncSurveyResponses() {
    try {
        // Open IndexedDB
        const db = await openDB();
        const tx = db.transaction('pending_responses', 'readonly');
        const store = tx.objectStore('pending_responses');
        const responses = await store.getAll();

        console.log(`[Service Worker] Syncing ${responses.length} survey responses`);

        for (const response of responses) {
            try {
                const fetchResponse = await fetch('/api/surveys/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': response.csrfToken
                    },
                    body: JSON.stringify(response.data)
                });

                if (fetchResponse.ok) {
                    // Remove from IndexedDB after successful sync
                    const deleteTx = db.transaction('pending_responses', 'readwrite');
                    await deleteTx.objectStore('pending_responses').delete(response.id);
                    console.log(`[Service Worker] Synced response ${response.id}`);
                }
            } catch (error) {
                console.error('[Service Worker] Failed to sync response:', error);
            }
        }
    } catch (error) {
        console.error('[Service Worker] Sync failed:', error);
    }
}

// Helper function to open IndexedDB
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('aimedkorea-offline', 1);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            
            if (!db.objectStoreNames.contains('pending_responses')) {
                db.createObjectStore('pending_responses', { 
                    keyPath: 'id', 
                    autoIncrement: true 
                });
            }
        };
    });
}

// Message handling for skip waiting
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        console.log('[Service Worker] Received skip waiting message');
        self.skipWaiting();
    }
});
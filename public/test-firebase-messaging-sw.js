importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

console.log('[Test Service Worker] Initializing...');

firebase.initializeApp({
    apiKey: "AIzaSyB1QMSJ7lUYdtOJuY8bscHYHNOL3j0j_SU",
    authDomain: "lens-soma.firebaseapp.com",
    projectId: "lens-soma",
    storageBucket: "lens-soma.firebasestorage.app",
    messagingSenderId: "559992014976",
    appId: "1:559992014976:web:c9affddb516e94018fbe65",
    
});

console.log('[Test Service Worker] Firebase initialized');

const messaging = firebase.messaging();
console.log('[Test Service Worker] Messaging initialized');

messaging.setBackgroundMessageHandler(function(payload) {
    console.log('[Test Service Worker] Received background message:', payload);

    const notificationOptions = {
        body: payload.notification.body,
        vibrate: [100, 50, 100],
        data: payload.data,
        actions: [
            {
                action: 'open',
                title: 'فتح الصفحة'
            }
        ],
        requireInteraction: true,
        dir: 'rtl',
        lang: 'ar'
    };

    console.log('[Test Service Worker] Showing notification with options:', notificationOptions);

    return self.registration.showNotification(
        payload.notification.title,
        notificationOptions
    );
});

self.addEventListener('notificationclick', function(event) {
    console.log('[Test Service Worker] Notification click received.');

    event.notification.close();

    if (event.action === 'open') {
        console.log('[Test Service Worker] Opening window');
        clients.openWindow('/test');
    }
});

importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

self.addEventListener('message', function(event) {
    if (event.data && event.data.type === 'FIREBASE_CONFIG') {
        firebase.initializeApp(event.data.config);
        const messaging = firebase.messaging();

        messaging.setBackgroundMessageHandler(function(payload) {
            const notificationTitle = payload.notification.title;
            const notificationOptions = {
                body: payload.notification.body,
                vibrate: [100, 50, 100],
                data: payload.data,
                actions: [
                    {
                        action: 'open_order',
                        title: 'عرض الطلب'
                    }
                ],
                requireInteraction: true,
                dir: 'rtl',
                lang: 'ar',
                tag: Date.now().toString()
            };

            return self.registration.showNotification(notificationTitle, notificationOptions);
        });
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const notificationData = event.notification.data || {};
    const link = notificationData.link;
    const uuid = notificationData.uuid;
    const targetUrl = link || '/admin/dashboard';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        })
        .then(function(clientList) {
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

self.addEventListener('push', function(event) {
    if (event.data) {
        const payload = event.data.json();

        event.waitUntil(
            self.registration.showNotification(payload.notification.title, {
                body: payload.notification.body,
                vibrate: [100, 50, 100],
                data: payload.data,
                actions: [
                    {
                        action: 'open_order',
                        title: 'عرض الطلب'
                    }
                ],
                requireInteraction: true,
                dir: 'rtl',
                lang: 'ar',
                tag: Date.now().toString()
            })
        );
    }
});

self.addEventListener('install', function(event) {
    event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', function(event) {
    event.waitUntil(self.clients.claim());
});

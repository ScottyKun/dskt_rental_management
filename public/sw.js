const CACHE_NAME = 'dskt-rental-v1';

self.addEventListener('install', (event) => {
    console.log('[SW] Installation');
    console.log('[SW] Installation VERSION 1');
    // On attend que l'utilisateur demande explicitement
    // l'activation de la nouvelle version.
});

self.addEventListener('activate', (event) => {
    console.log('[SW] Activation');

    event.waitUntil(
        self.clients.claim()
    );
});

self.addEventListener('message', (event) => {
    if (event.data?.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

self.addEventListener('push', (event) => {
    console.log('[SW] Push reçu');

    let data = {
        title: 'DSKT Rental',
        body: 'Vous avez une nouvelle notification.',
        url: '/dashboard',
    };

    if (event.data) {
        try {
            data = event.data.json();
        } catch (error) {
            console.error('[SW] Impossible de lire le payload push', error);
        }
    }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: '/pwaIcons/icon-192.png',
            badge: '/pwaIcons/icon-192.png',
            data: {
                url: data.url ?? '/dashboard',
            },
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();

    const url = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true,
        }).then(windowClients => {

            for (const client of windowClients) {
                if (client.url.includes(self.location.origin)) {
                    client.focus();
                    client.navigate(url);
                    return;
                }
            }

            return clients.openWindow(url);
        })
    );
});

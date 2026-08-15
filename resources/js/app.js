import './bootstrap';

if ('serviceWorker' in navigator) {
    window.addEventListener('load', async () => {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js');

            console.log(
                '[PWA] Service Worker enregistré :',
                registration.scope
            );

            // Vérifie périodiquement si une nouvelle version existe.
            setInterval(() => {
                registration.update();
            }, 60 * 60 * 1000);

            // Vérification lorsqu'on revient sur l'application.
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    registration.update();
                }
            });

            // Une nouvelle version est en attente d'activation.
            if (registration.waiting) {
                showUpdateAvailable(registration);
            }

            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;

                if (!newWorker) {
                    return;
                }

                newWorker.addEventListener('statechange', () => {
                    if (
                        newWorker.state === 'installed' &&
                        navigator.serviceWorker.controller
                    ) {
                        showUpdateAvailable(registration);
                    }
                });
            });

        } catch (error) {
            console.error(
                '[PWA] Échec de l’enregistrement du Service Worker :',
                error
            );
        }
    });
}


function showUpdateAvailable(registration) {
    // Évite de créer plusieurs notifications.
    if (document.getElementById('pwa-update-notification')) {
        return;
    }

    const notification = document.createElement('div');

    notification.id = 'pwa-update-notification';

    notification.className =
        'fixed bottom-5 left-5 right-5 sm:left-auto sm:right-5 ' +
        'sm:w-96 bg-white border border-gray-200 rounded-xl shadow-xl ' +
        'p-4 z-[9999]';

    notification.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="text-blue-600 text-xl">
                <i class="fa-solid fa-arrows-rotate"></i>
            </div>

            <div class="flex-1">
                <p class="font-semibold text-gray-800">
                    Nouvelle version disponible
                </p>

                <p class="text-sm text-gray-500 mt-1">
                    Une nouvelle version de DSKT Rental est disponible.
                </p>

                <div class="flex gap-2 mt-3">
                    <button
                        id="pwa-update-button"
                        class="bg-blue-600 hover:bg-blue-700 text-white
                               px-3 py-2 rounded-lg text-sm">
                        Mettre à jour
                    </button>

                    <button
                        id="pwa-update-later"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700
                               px-3 py-2 rounded-lg text-sm">
                        Plus tard
                    </button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(notification);

    document
        .getElementById('pwa-update-later')
        ?.addEventListener('click', () => {
            notification.remove();
        });

    document
        .getElementById('pwa-update-button')
        ?.addEventListener('click', () => {

            const waitingWorker = registration.waiting;

            if (!waitingWorker) {
                window.location.reload();
                return;
            }

            waitingWorker.postMessage({
                type: 'SKIP_WAITING'
            });
        });

    // Quand le nouveau SW prend le contrôle,
    // on recharge l'application.
    navigator.serviceWorker.addEventListener(
        'controllerchange',
        () => {
            window.location.reload();
        },
        { once: true }
    );
}

//webpush
async function subscribeToPush() {
    if (!('serviceWorker' in navigator)) {
        console.warn('[Push] Service Worker non supporté');
        return;
    }

    if (!('PushManager' in window)) {
        console.warn('[Push] Web Push non supporté');
        return;
    }

    if (!('Notification' in window)) {
        console.warn('[Push] Notifications non supportées');
        return;
    }

    // Permission déjà refusée.
    if (Notification.permission === 'denied') {
        console.warn('[Push] Notifications refusées par utilisateur');
        return;
    }

    try {
        const registration = await navigator.serviceWorker.ready;

        let subscription = await registration.pushManager.getSubscription();

        if (!subscription) {

            // Permission pas encore déterminée.
            if (Notification.permission === 'default') {
                const permission = await Notification.requestPermission();

                if (permission !== 'granted') {
                    console.warn('[Push] Permission non accordée');
                    return;
                }
            }

            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(
                    import.meta.env.VITE_VAPID_PUBLIC_KEY
                ),
            });

            console.log('[Push] Nouvel abonnement créé');
        }

        await savePushSubscription(subscription);

    } catch (error) {
        console.error('[Push] Erreur abonnement :', error);
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);

    const base64 = (
        base64String +
        padding
    )
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);

    return Uint8Array.from(
        [...rawData].map(char => char.charCodeAt(0))
    );
}

async function savePushSubscription(subscription) {
    const response = await window.axios.post(
        '/push/subscribe',
        {
            endpoint: subscription.endpoint,

            keys: {
                p256dh: arrayBufferToBase64(
                    subscription.getKey('p256dh')
                ),

                auth: arrayBufferToBase64(
                    subscription.getKey('auth')
                ),
            },

            contentEncoding:
                subscription.options?.applicationServerKey
                    ? 'aes128gcm'
                    : null,
        }
    );

    console.log('[Push] Abonnement enregistré côté serveur', response.data);
}

function arrayBufferToBase64(buffer) {
    if (!buffer) {
        return null;
    }

    return btoa(
        String.fromCharCode(...new Uint8Array(buffer))
    );
}

window.enablePushIfNeeded = async function () {
    if (!('Notification' in window)) {
        return;
    }

    if (Notification.permission === 'denied') {
        console.warn('[Push] Notifications refusées');
        return;
    }

    const registration = await navigator.serviceWorker.ready;

    const subscription =
        await registration.pushManager.getSubscription();

    if (subscription) {
        console.log('[Push] Abonnement déjà existant');
        return;
    }

    await subscribeToPush();
};
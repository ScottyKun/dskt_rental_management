import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',

    key: import.meta.env.VITE_REVERB_APP_KEY,

    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT),

    wssPort: Number(import.meta.env.VITE_REVERB_PORT),

    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',

    enabledTransports: ['ws', 'wss'],

    authEndpoint: '/broadcasting/auth',
});
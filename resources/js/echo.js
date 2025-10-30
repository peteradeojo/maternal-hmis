import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const hostname = window.location.hostname;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: hostname, // import.meta.env.VITE_REVERB_HOST,
    wsPort: 8080, // import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: 443, // import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: ['localhost', '127.0.0.1', '100.114.76.111', 'portal', 'portal.lan', '192.168.0.171'].includes(hostname) == false,
    enabledTransports: ['ws', 'wss'],
});

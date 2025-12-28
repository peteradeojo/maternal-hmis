import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

const hostname = window.location.hostname;
let reverbHostname = hostname;

switch (hostname) {
    case 'portal.maternalchildhosp.com':
        reverbHostname = 'reverb.maternalchildhosp.com';
        break;
    case 'portal.lan':
        reverbHostname = 'reverb.lan';
        break;
    default:
        reverbHostname = hostname;
        break;
}

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: reverbHostname, // import.meta.env.VITE_REVERB_HOST,
    wsPort: 8080, // import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: 443, // import.meta.env.VITE_REVERB_PORT ?? 443,
    // forceTLS: ['localhost', '127.0.0.1', '100.114.76.111', 'portal', 'portal.lan', '192.168.0.171'].includes(hostname) == false,
    forceTLS: window.location.protocol === 'https:',
    enabledTransports: ['ws', 'wss'],
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        }
    }
});

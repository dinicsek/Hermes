/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
import axios from 'axios';

import Pusher from 'pusher-js';
import Echo from "laravel-echo";

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

window.Pusher = Pusher;

if (import.meta.env.VITE_PUSHER_PORT) {
    let echoConfig = {
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
        wsPort: import.meta.env.VITE_PUSHER_PORT,
        wssPort: import.meta.env.VITE_PUSHER_PORT,
        forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https'
    };

    window.Echo = new Echo(echoConfig);
} else {
    let pusher = new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
        wsHost: import.meta.env.VITE_PUSHER_HOST,
        httpHost: import.meta.env.VITE_PUSHER_HOST,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
        httpPath: '/realtime',
        wsPath: '/realtime',
    })

    window.Echo = new Echo({
        broadcaster: 'pusher',
        client: pusher,
    });
}

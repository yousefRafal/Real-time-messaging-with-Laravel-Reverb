import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

/**
 * Laravel Echo Configuration
 * 
 * Initialize Echo for real-time broadcasting with Reverb
 */
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

// // Connection event handlers
// window.Echo.connector.socket.on('connect', () => {
//     console.log('✅ Connected to Reverb server');
// });

// window.Echo.connector.socket.on('disconnect', () => {
//     console.log('❌ Disconnected from Reverb server');
// });

// window.Echo.connector.socket.on('error', (error) => {
//     console.error('🔥 Reverb connection error:', error);
// });

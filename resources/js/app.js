import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;


// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     wsHost: import.meta.env.VITE_REVERB_HOST,
//     wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
//     wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'http',
//     enabledTransports: ['ws', 'wss'],
//     disableStats: true,
//     timeout: 5000,
//     reconnectAfterMs: 1000,
//     maxAttempts: Infinity,
//     maxAttemptsAfterMs: 1000,
//     disableHeartbeat: true,
//     heartbeatIntervalMs: 1000,  
//     heartbeatTimeoutMs: 5000,
//     heartbeatIntervalAfterCloseMs: 1000,
// });

// if (window.Echo ) {
//     window.Echo.connector.socket?.on('connect', () => {
//         console.log("✅ Connected to Reverb server")
//     });
//     window.Echo.connector.socket?.on('disconnect', () => {
//         console.log("❌ Disconnected from Reverb server")
//     });
//     window.Echo.connector.socket?.on('error', x => {
//         console.error("🔥 Reverb connection error:", x)
//     });
//     window.Echo.connector.socket?.on('close', () => {
//         console.log("❌ Closed connection to Reverb server")
//     });
// } else {
//     console.error("❌ Failed to connect to Reverb server")
// }





// import Echo from 'laravel-echo';
// import Pusher from 'pusher-js';


// // window.Pusher = Pusher;
const options = {
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY
}
var pusher = new Pusher("6fd0240d3f0724aded6f", {
  cluster: "eu", // Replace "APP_CLUSTER" with your cluster shortcode (e.g., "mt1", "eu", "us2")
});


window.Echo = new Echo({
    ...options,
    client: new Pusher("6fd0240d3f0724aded6f", {
        cluster: "eu", // Replace "APP_CLUSTER" with your cluster shortcode (e.g., "mt1", "eu", "us2")
    })
});

// if (window.Echo ) {
//     window.Echo.connector.socket?.on('connect', () => {
//         console.log("✅ Connected to Reverb server")
//     });
//     window.Echo.connector.socket?.on('disconnect', () => {
//         console.log("❌ Disconnected from Reverb server")
//     });
//     window.Echo.connector.socket?.on('error', x => {
//         console.error("🔥 Reverb connection error:", x)
//     });
//     window.Echo.connector.socket?.on('close', () => {
//         console.log("❌ Closed connection to Reverb server")
//     });
// } else {
//     console.error("❌ Failed to connect to Reverb server")
// }


window.Echo.channel('chat.general')
    .listen('ChatMessage', ({ message }) => {
        console.log(" New message received:", message);
        event(new ChatMessage(message));
    });


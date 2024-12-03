import Echo from "laravel-echo";
import Pusher from "pusher-js";

// Set up Laravel Echo with Pusher
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'd86cdeb92a26c70836eb',  // Replace with your Pusher app key
    cluster: 'ap1',  // Replace with your app cluster
    forceTLS: true,
});


// Listening to a private channel
window.Echo.private(`chat.${receiverId}`) // receiverId should be the logged-in user's receiver ID
    .listen('MessageSent', (event) => {
        console.log('Message received:', event);
        // Handle the incoming message, e.g., display it in the chat
    });

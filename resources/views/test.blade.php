<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Event Trigger</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/7.2.0/pusher.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <h1>Trigger Message Event</h1>
    <button id="sendMessage">Send Message</button>

    <script>
        console.log(window.Echo);
        // Initialize Pusher with your Pusher key and cluster
        const pusher = new Pusher('d86cdeb92a26c70836eb', {
            cluster: 'ap1', // Your Pusher cluster
            forceTLS: true
        });

        // Initialize Echo with Pusher
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: 'd86cdeb92a26c70836eb', // Pusher app key
            cluster: 'ap1', // Pusher cluster
            forceTLS: true
        , });

        document.getElementById('sendMessage').addEventListener('click', function() {
            // Sample chat data
            const chat = {
                sender_id: 3, // Replace with actual sender ID
                receiver_id: 4, // Replace with actual receiver ID
                message: 'Hello from frontend!' // Message to send
            };

            // Trigger the event directly through Pusher
            pusher.trigger('chat.' + chat.receiver_id, 'MessageSent', {
                message: chat.message
                , sender_id: chat.sender_id
                , receiver_id: chat.receiver_id
            });

            console.log('Message triggered from frontend');
        });

        // Listening for the MessageSent event broadcasted on the channel
        window.Echo.private('chat.4') // Replace with actual receiver ID
            .listen('MessageSent', (event) => {
                console.log('Message received:', event);
                // Handle the incoming message (e.g., display it in the chat)
            });

    </script>
</body>
</html>

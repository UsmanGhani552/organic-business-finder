<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Socket.IO Chat</title>
    <script src="https://cdn.socket.io/4.6.1/socket.io.min.js"></script>
</head>
<body>
    <h1>Socket.IO Chat</h1>

    <div id="chatBox">
        <!-- Chat messages will be appended here -->
    </div>

    <form id="chatForm">
        <input type="text" id="messageInput" placeholder="Type your message here..." required>
        <button type="submit">Send</button>
    </form>

    <script>
        // Connect to the Socket.IO server
        const socket = io('https://organic-business-finder.koderspedia.net:3000');

        const sender_id = 3; // Set sender's ID dynamically (e.g., from your Laravel session)
        const receiver_id = 4; // Set receiver's ID dynamically (e.g., the recipient of the message)

        // Listen for incoming messages
        socket.on('message', (data) => {
            const chatBox = document.getElementById('chatBox');
            
            // Format the chat message to show both sender and receiver
            const newMessage = `<p><strong>Sender (ID: ${data.sender_id})</strong> to <strong>Receiver (ID: ${data.receiver_id})</strong>: ${data.message}</p>`;
            
            chatBox.innerHTML += newMessage; // Display message in the chat box
        });

        // Handle form submission
        document.getElementById('chatForm').addEventListener('submit', (event) => {
            event.preventDefault();
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value;

            // Send the message to the server
            socket.emit('message', {
                sender_id: sender_id,
                receiver_id: receiver_id,
                message: message
            });

            // Optionally, add the message to your chat box immediately
            const chatBox = document.getElementById('chatBox');
            const myMessage = `<p><strong>You (Sender ID: ${sender_id})</strong> to <strong>Receiver (ID: ${receiver_id})</strong>: ${message}</p>`;
            chatBox.innerHTML += myMessage;

            // Clear the input field
            messageInput.value = '';
        });
    </script>
</body>
</html>

import express from 'express';
import http from 'http';
import { Server } from 'socket.io';
import axios from 'axios';
require('dotenv').config();

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: "*", // Allow all origins for testing
        methods: ["GET", "POST"],
        allowedHeaders: ["my-custom-header"],
        credentials: true,
    }
});

// When a client connects
io.on('connection', (socket) => {
    console.log('a user connected');
  
    // Listen for messages from clients
    socket.on('message', async (data) => {
        console.log('Message from client:', data);
        const baseUrl = process.env.BASE_URL;
        // Send the message data to the Laravel backend for database insertion
        try {
            const response = await axios.post(`${baseUrl}/api/chats/send`, {
                sender_id: data.sender_id,
                receiver_id: data.receiver_id,
                message: data.message
            });
            console.log('Message saved:', response.data);
        } catch (error) {
            console.error('Error saving message:', error);
        }

        // Emit the message to all connected clients
        io.emit('message', data); // Use io.emit to broadcast to all connected clients
    });

    // When the client disconnects
    socket.on('disconnect', () => {
        console.log('user disconnected');
    });
});

// Start the server
server.listen(3000, () => {
    console.log('Socket.IO server is running on http://localhost:3000');
});

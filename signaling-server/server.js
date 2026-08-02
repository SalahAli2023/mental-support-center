/**
 * WebRTC Signaling Server
 * 
 * This server handles WebRTC signaling for video therapy sessions.
 * It uses Socket.IO for real-time communication between clients.
 * 
 * Events:
 * - joinRoom: Client joins a session room
 * - offer: WebRTC offer from peer
 * - answer: WebRTC answer from peer
 * - iceCandidate: ICE candidate exchange
 * - leaveRoom: Client leaves the room
 */

require('dotenv').config();
const { Server } = require('socket.io');
const http = require('http');
const cors = require('cors');
const url = require('url');

const PORT = process.env.SIGNALING_PORT || 3001;
const CORS_ORIGIN = process.env.CORS_ORIGIN || 'http://localhost:5173';

// Create HTTP server
const server = http.createServer((req, res) => {
      // Enable CORS
      res.setHeader('Access-Control-Allow-Origin', CORS_ORIGIN);
      res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
      res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');

      if (req.method === 'OPTIONS') {
            res.writeHead(200);
            res.end();
            return;
      }

      const parsedUrl = url.parse(req.url, true);

      // Endpoint لإرسال event إنهاء الجلسة
      if (parsedUrl.pathname === '/api/session/end' && req.method === 'POST') {
            let body = '';
            req.on('data', chunk => {
                  body += chunk.toString();
            });

            req.on('end', () => {
                  try {
                        const data = JSON.parse(body);
                        const { roomId } = data;

                        if (!roomId) {
                              res.writeHead(400, { 'Content-Type': 'application/json' });
                              res.end(JSON.stringify({ error: 'Room ID is required' }));
                              return;
                        }

                        // إرسال event إنهاء الجلسة لجميع المشاركين في الغرفة
                        io.to(roomId).emit('sessionEnded', {
                              roomId,
                              message: 'تم إنهاء الجلسة من قبل المشرف',
                              endedAt: new Date().toISOString()
                        });

                        console.log(`Session ended event sent to room ${roomId}`);

                        res.writeHead(200, { 'Content-Type': 'application/json' });
                        res.end(JSON.stringify({ success: true, message: 'Session ended event sent' }));
                  } catch (error) {
                        console.error('Error handling session end:', error);
                        res.writeHead(500, { 'Content-Type': 'application/json' });
                        res.end(JSON.stringify({ error: 'Internal server error' }));
                  }
            });
            return;
      }

      // Default response
      res.writeHead(404, { 'Content-Type': 'application/json' });
      res.end(JSON.stringify({ error: 'Not found' }));
});

// Initialize Socket.IO server
const io = new Server(server, {
      cors: {
            origin: CORS_ORIGIN,
            methods: ['GET', 'POST'],
            credentials: true
      },
      transports: ['websocket', 'polling']
});

// Store active rooms and their participants
const rooms = new Map();

/**
 * Get or create room data structure
 */
function getRoom(roomId) {
      if (!rooms.has(roomId)) {
            rooms.set(roomId, {
                  participants: new Set(),
                  createdAt: new Date()
            });
      }
      return rooms.get(roomId);
}

/**
 * Remove empty rooms
 */
function cleanupRoom(roomId) {
      const room = rooms.get(roomId);
      if (room && room.participants.size === 0) {
            rooms.delete(roomId);
            console.log(`Room ${roomId} cleaned up`);
      }
}

io.on('connection', (socket) => {
      console.log(`Client connected: ${socket.id}`);

      /**
       * Join a session room
       * Expected payload: { roomId, userId, userRole }
       */
      socket.on('joinRoom', async (data) => {
            try {
                  const { roomId, userId, userRole } = data;

                  if (!roomId) {
                        socket.emit('error', { message: 'Room ID is required' });
                        return;
                  }

                  // Join the socket room
                  socket.join(roomId);

                  const room = getRoom(roomId);
                  room.participants.add(socket.id);

                  // Store user info in socket data
                  socket.data.roomId = roomId;
                  socket.data.userId = userId;
                  socket.data.userRole = userRole;

                  console.log(`User ${userId} (${userRole}) joined room ${roomId}`);

                  // Notify others in the room
                  socket.to(roomId).emit('userJoined', {
                        userId,
                        userRole,
                        socketId: socket.id
                  });

                  // Send confirmation to the joining user
                  socket.emit('joinedRoom', {
                        roomId,
                        participants: Array.from(room.participants).length
                  });

                  // If there are other participants, notify about existing peers
                  const otherParticipants = Array.from(room.participants).filter(id => id !== socket.id);
                  if (otherParticipants.length > 0) {
                        console.log(`Notifying new user about ${otherParticipants.length} existing participants`);
                        socket.emit('existingParticipants', {
                              count: otherParticipants.length,
                              socketIds: otherParticipants
                        });

                        // Also notify existing participants about the new user
                        otherParticipants.forEach(participantId => {
                              io.to(participantId).emit('userJoined', {
                                    userId,
                                    userRole,
                                    socketId: socket.id
                              });
                        });
                  }

            } catch (error) {
                  console.error('Error in joinRoom:', error);
                  socket.emit('error', { message: 'Failed to join room', error: error.message });
            }
      });

      /**
       * Handle WebRTC offer
       * Expected payload: { roomId, offer, targetSocketId? }
       */
      socket.on('offer', (data) => {
            try {
                  const { roomId, offer, targetSocketId } = data;

                  if (!roomId || !offer) {
                        socket.emit('error', { message: 'Room ID and offer are required' });
                        return;
                  }

                  // If targetSocketId is specified, send to that specific socket
                  // Otherwise, broadcast to all others in the room
                  if (targetSocketId) {
                        io.to(targetSocketId).emit('offer', {
                              offer,
                              fromSocketId: socket.id,
                              fromUserId: socket.data.userId
                        });
                  } else {
                        socket.to(roomId).emit('offer', {
                              offer,
                              fromSocketId: socket.id,
                              fromUserId: socket.data.userId
                        });
                  }

                  console.log(`Offer sent from ${socket.id} in room ${roomId}`);
            } catch (error) {
                  console.error('Error in offer:', error);
                  socket.emit('error', { message: 'Failed to send offer', error: error.message });
            }
      });

      /**
       * Handle WebRTC answer
       * Expected payload: { roomId, answer, targetSocketId }
       */
      socket.on('answer', (data) => {
            try {
                  const { roomId, answer, targetSocketId } = data;

                  if (!roomId || !answer || !targetSocketId) {
                        socket.emit('error', { message: 'Room ID, answer, and target socket ID are required' });
                        return;
                  }

                  io.to(targetSocketId).emit('answer', {
                        answer,
                        fromSocketId: socket.id,
                        fromUserId: socket.data.userId
                  });

                  console.log(`Answer sent from ${socket.id} to ${targetSocketId} in room ${roomId}`);
            } catch (error) {
                  console.error('Error in answer:', error);
                  socket.emit('error', { message: 'Failed to send answer', error: error.message });
            }
      });

      /**
       * Handle ICE candidate exchange
       * Expected payload: { roomId, candidate, targetSocketId? }
       */
      socket.on('iceCandidate', (data) => {
            try {
                  const { roomId, candidate, targetSocketId } = data;

                  if (!roomId || !candidate) {
                        socket.emit('error', { message: 'Room ID and candidate are required' });
                        return;
                  }

                  // If targetSocketId is specified, send to that specific socket
                  // Otherwise, broadcast to all others in the room
                  if (targetSocketId) {
                        io.to(targetSocketId).emit('iceCandidate', {
                              candidate,
                              fromSocketId: socket.id,
                              fromUserId: socket.data.userId
                        });
                  } else {
                        socket.to(roomId).emit('iceCandidate', {
                              candidate,
                              fromSocketId: socket.id,
                              fromUserId: socket.data.userId
                        });
                  }

                  console.log(`ICE candidate sent from ${socket.id} in room ${roomId}`);
            } catch (error) {
                  console.error('Error in iceCandidate:', error);
                  socket.emit('error', { message: 'Failed to send ICE candidate', error: error.message });
            }
      });

      /**
       * Handle chat messages
       * Expected payload: { roomId, message: { id, text, sender, time } }
       */
      socket.on('chatMessage', (data = {}) => {
            try {
                  const { roomId, message } = data;

                  if (!roomId || !message || !message.text) {
                        socket.emit('error', { message: 'Room ID and message are required' });
                        return;
                  }

                  // تحديد نوع المرسل من البيانات المرسلة أو من socket.data
                  let senderRole = message.sender || message.senderRole;
                  if (!senderRole) {
                        // محاولة تحديد من socket.data.userRole
                        const userRole = socket.data.userRole || '';
                        if (userRole.toLowerCase() === 'therapist' || userRole.toLowerCase() === 'therapist') {
                              senderRole = 'therapist';
                        } else {
                              senderRole = 'patient';
                        }
                  }

                  const enrichedMessage = {
                        ...message,
                        id: message.id || `${socket.id}-${Date.now()}`,
                        sender: senderRole,
                        senderRole: senderRole,
                        senderSocketId: socket.id,
                        senderId: message.senderId || socket.data.userId || 0,
                        text: message.text.trim(),
                        time: message.time || new Date().toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' })
                  };

                  // إرسال الرسالة لجميع المشاركين الآخرين في الغرفة (ليس المرسل نفسه)
                  // لأن المرسل أضاف الرسالة محلياً بالفعل
                  socket.to(roomId).emit('chatMessage', enrichedMessage);

                  console.log(`💬 Chat message relayed in room ${roomId} from ${socket.id} (${senderRole}) to other participants`);
            } catch (error) {
                  console.error('Error in chatMessage:', error);
                  socket.emit('error', { message: 'Failed to send chat message', error: error.message });
            }
      });

      /**
       * Handle leaving a room
       */
      socket.on('leaveRoom', (data) => {
            try {
                  const { roomId } = data || { roomId: socket.data.roomId };

                  if (roomId) {
                        const room = getRoom(roomId);
                        room.participants.delete(socket.id);

                        socket.leave(roomId);
                        socket.to(roomId).emit('userLeft', {
                              userId: socket.data.userId,
                              socketId: socket.id
                        });

                        cleanupRoom(roomId);
                        console.log(`User ${socket.data.userId} left room ${roomId}`);
                  }
            } catch (error) {
                  console.error('Error in leaveRoom:', error);
            }
      });

      /**
       * Handle disconnection
       */
      socket.on('disconnect', () => {
            try {
                  const roomId = socket.data.roomId;

                  if (roomId) {
                        const room = getRoom(roomId);
                        room.participants.delete(socket.id);

                        socket.to(roomId).emit('userLeft', {
                              userId: socket.data.userId,
                              socketId: socket.id
                        });

                        cleanupRoom(roomId);
                        console.log(`User ${socket.data.userId} disconnected from room ${roomId}`);
                  } else {
                        console.log(`Client ${socket.id} disconnected`);
                  }
            } catch (error) {
                  console.error('Error in disconnect:', error);
            }
      });

      /**
       * Handle errors
       */
      socket.on('error', (error) => {
            console.error(`Socket error for ${socket.id}:`, error);
      });
});

// Start server
server.listen(PORT, () => {
      console.log(`🚀 WebRTC Signaling Server running on port ${PORT}`);
      console.log(`📡 CORS enabled for: ${CORS_ORIGIN}`);
      console.log(`🔗 Ready to handle WebRTC signaling events`);
});

// Graceful shutdown
process.on('SIGTERM', () => {
      console.log('SIGTERM received, shutting down gracefully');
      server.close(() => {
            console.log('Server closed');
            process.exit(0);
      });
});

process.on('SIGINT', () => {
      console.log('SIGINT received, shutting down gracefully');
      server.close(() => {
            console.log('Server closed');
            process.exit(0);
      });
});



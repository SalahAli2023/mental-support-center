# WebRTC Signaling Server

This is a Node.js Socket.IO server that handles WebRTC signaling for video therapy sessions.

## Features

- Real-time WebRTC signaling (offer, answer, ICE candidates)
- Room-based session management
- Automatic cleanup of empty rooms
- CORS support for cross-origin requests
- Error handling and logging

## Installation

```bash
npm install
```

## Configuration

Copy `.env.example` to `.env` and configure:

```bash
SIGNALING_PORT=3001
CORS_ORIGIN=http://localhost:5173
```

## Running

Development mode (with auto-reload):
```bash
npm run dev
```

Production mode:
```bash
npm start
```

## Events

### Client → Server

- `joinRoom`: Join a session room
  ```javascript
  {
    roomId: string,
    userId: number,
    userRole: 'client' | 'therapist' | 'admin'
  }
  ```

- `offer`: Send WebRTC offer
  ```javascript
  {
    roomId: string,
    offer: RTCSessionDescriptionInit,
    targetSocketId?: string
  }
  ```

- `answer`: Send WebRTC answer
  ```javascript
  {
    roomId: string,
    answer: RTCSessionDescriptionInit,
    targetSocketId: string
  }
  ```

- `iceCandidate`: Send ICE candidate
  ```javascript
  {
    roomId: string,
    candidate: RTCIceCandidateInit,
    targetSocketId?: string
  }
  ```

- `leaveRoom`: Leave the session room
  ```javascript
  {
    roomId: string
  }
  ```

### Server → Client

- `joinedRoom`: Confirmation of joining
- `userJoined`: Notification when another user joins
- `userLeft`: Notification when a user leaves
- `offer`: Received WebRTC offer
- `answer`: Received WebRTC answer
- `iceCandidate`: Received ICE candidate
- `error`: Error message
- `existingParticipants`: Notification of existing participants

## Integration with Laravel

The server can be integrated with Laravel for authentication and session validation. This can be done via HTTP requests to Laravel API endpoints.



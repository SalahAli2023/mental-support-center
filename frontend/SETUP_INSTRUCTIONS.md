# Quick Setup Instructions

## 🚀 Quick Start

### 1. Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Backend: http://localhost:8000

### 2. Frontend (Vue)

```bash
npm install
npm run dev
```

Frontend: http://localhost:5173

### 3. Signaling Server (Node.js)

```bash
cd signaling-server
npm install
cp .env.example .env
npm start
```

Signaling: http://localhost:3001

## 📝 Required Environment Variables

### Backend (.env)
```env
APP_URL=http://localhost:8000
SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

### Frontend (.env)
```env
VITE_API_URL=http://localhost:8000/api
VITE_SIGNALING_URL=http://localhost:3001
```

### Signaling Server (.env)
```env
SIGNALING_PORT=3001
CORS_ORIGIN=http://localhost:5173
```

## ✅ All Systems Implemented

- ✅ Appointment booking system with status flow
- ✅ Therapist API integration with Vue
- ✅ Video session system with access control
- ✅ WebRTC signaling server (Socket.IO)
- ✅ Real-time video communication

See `IMPLEMENTATION_COMPLETE.md` for detailed documentation.



# Implementation Complete - Psychological Support System

This document outlines all the implemented systems and how to run them.

## ✅ Completed Systems

### 1. Booking System (Appointments)

**Status:** ✅ Complete

**Features:**
- Appointment model with proper relationships
- Status flow: `pending` → `confirmed` → `completed` / `cancelled`
- Role-based access control (Client, Therapist, Admin)
- Full CRUD operations via API
- Appointment validation and status transition rules

**Files Created/Modified:**
- `backend/database/migrations/2025_01_21_000001_update_appointments_status_enum.php` - Added pending/confirmed status
- `backend/app/Http/Controllers/Api/AppointmentController.php` - Enhanced with status flow validation
- `backend/app/Http/Resources/AppointmentResource.php` - Already existed, verified working
- `src/stores/appointments.ts` - Updated to use real API

**API Endpoints:**
- `GET /api/appointments` - List appointments (role-filtered)
- `POST /api/appointments` - Create appointment (status: pending)
- `GET /api/appointments/{id}` - Get appointment details
- `PUT /api/appointments/{id}` - Update appointment (with status validation)
- `DELETE /api/appointments/{id}` - Delete appointment

### 2. Therapist API + Vue Integration

**Status:** ✅ Complete

**Features:**
- Therapist API fully functional
- Vue components connected to real API
- Admin dashboard can create/update/delete therapists
- Public-facing page fetches real therapist data

**Files Created/Modified:**
- `src/components/frontend/Specialists/TherapistList.vue` - Now fetches from API
- `src/components/frontend/Specialists/TherapistCard.vue` - Updated to handle API data structure
- `src/stores/therapists.ts` - Already existed, verified working

**API Endpoints:**
- `GET /api/therapists` - List therapists (public)
- `GET /api/therapists/{id}` - Get therapist details
- `POST /api/therapists` - Create therapist (admin)
- `PUT /api/therapists/{id}` - Update therapist (admin)
- `DELETE /api/therapists/{id}` - Delete therapist (admin)

### 3. Video Session System

**Status:** ✅ Complete

**Features:**
- Session model with secure room_id generation
- Session creation from appointments (admin only)
- Access control: Only appointment's patient or therapist can access
- Session status management (scheduled → active → ended)
- Integration with appointments

**Files Created:**
- `backend/database/migrations/2025_01_21_000002_create_sessions_table.php`
- `backend/app/Models/Session.php`
- `backend/app/Http/Controllers/Api/SessionController.php`
- `backend/app/Http/Resources/SessionResource.php`

**API Endpoints:**
- `GET /api/sessions` - List sessions (role-filtered)
- `POST /api/sessions` - Create session from appointment (admin)
- `GET /api/sessions/{id}` - Get session details
- `GET /api/sessions/room/{roomId}` - Get session by room_id (for video access)
- `PUT /api/sessions/{id}` - Update session
- `POST /api/sessions/{id}/start` - Start session
- `POST /api/sessions/{id}/end` - End session

**Route:**
- `/session/{roomId}` - Video session page (requires authentication)

### 4. Real-Time WebRTC Signaling (Node.js + Socket.IO)

**Status:** ✅ Complete

**Features:**
- Node.js Socket.IO server for WebRTC signaling
- Room-based session management
- WebRTC offer/answer/ICE candidate exchange
- Automatic cleanup of empty rooms
- CORS support

**Files Created:**
- `signaling-server/package.json`
- `signaling-server/server.js`
- `signaling-server/README.md`
- `signaling-server/.env.example`

**Socket.IO Events:**
- `joinRoom` - Join a session room
- `offer` - Send WebRTC offer
- `answer` - Send WebRTC answer
- `iceCandidate` - Exchange ICE candidates
- `leaveRoom` - Leave the room

**Vue Component:**
- `src/components/frontend/Session/VideoSession.vue` - Fully integrated with WebRTC and Socket.IO

## 🚀 Running the System

### Prerequisites

1. **Laravel Backend:**
   - PHP 8.1+
   - Composer
   - MySQL/SQLite
   - Node.js (for frontend)

2. **Vue Frontend:**
   - Node.js 18+
   - npm or yarn

3. **Signaling Server:**
   - Node.js 18+

### Step 1: Laravel Backend Setup

```bash
cd Psychological_Support/backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# (Optional) Seed database
php artisan db:seed

# Start Laravel server
php artisan serve
```

**Backend runs on:** `http://localhost:8000`

### Step 2: Vue Frontend Setup

```bash
cd Psychological_Support

# Install dependencies
npm install

# Start development server
npm run dev
```

**Frontend runs on:** `http://localhost:5173`

### Step 3: Signaling Server Setup

```bash
cd Psychological_Support/signaling-server

# Install dependencies
npm install

# Copy environment file
cp .env.example .env

# Edit .env if needed (defaults are usually fine)
# SIGNALING_PORT=3001
# CORS_ORIGIN=http://localhost:5173

# Start server
npm start
# Or for development with auto-reload:
npm run dev
```

**Signaling server runs on:** `http://localhost:3001`

## 📋 Environment Variables

### Laravel Backend (.env)

```env
APP_NAME="Psychological Support"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Or MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=psychological_support
# DB_USERNAME=root
# DB_PASSWORD=

# Sanctum (for API authentication)
SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

### Vue Frontend (.env)

```env
VITE_API_URL=http://localhost:8000/api
VITE_SIGNALING_URL=http://localhost:3001
```

### Signaling Server (.env)

```env
SIGNALING_PORT=3001
CORS_ORIGIN=http://localhost:5173
LARAVEL_API_URL=http://localhost:8000/api
```

## 🔐 Authentication Flow

1. **User Registration/Login:**
   - Frontend: `/register` or login modal
   - API: `POST /api/frontend/register` or `POST /api/frontend/login`
   - Returns: JWT token (Laravel Sanctum)

2. **API Requests:**
   - Token stored in localStorage or Pinia store
   - Sent as `Authorization: Bearer {token}` header

3. **Session Access:**
   - User must be authenticated
   - Must be the appointment's patient or therapist
   - Access checked in `SessionController::getByRoomId()`

## 📊 Database Schema

### Appointments Table
- `id` - Primary key
- `client_id` - Foreign key to users
- `therapist_id` - Foreign key to therapists
- `starts_at` - Appointment start time
- `ends_at` - Appointment end time
- `status` - Enum: pending, confirmed, completed, cancelled
- `notes` - Optional notes
- `cancellation_reason` - Optional cancellation reason

### Sessions Table
- `id` - Primary key
- `appointment_id` - Foreign key to appointments
- `room_id` - Unique secure identifier (32 chars)
- `start_time` - Session start time
- `end_time` - Session end time
- `status` - Enum: scheduled, active, ended, cancelled
- `notes` - Optional notes

## 🔄 Workflow

### Creating a Video Session

1. **Patient creates appointment:**
   ```
   POST /api/appointments
   {
     "therapist_id": 1,
     "starts_at": "2025-01-22 10:00:00",
     "notes": "First session"
   }
   ```
   Status: `pending`

2. **Admin confirms appointment:**
   ```
   PUT /api/appointments/{id}
   {
     "status": "confirmed"
   }
   ```

3. **Admin creates session when appointment time arrives:**
   ```
   POST /api/sessions
   {
     "appointment_id": 1
   }
   ```
   Returns: Session with `room_id`

4. **Patient/Therapist accesses video session:**
   - Navigate to: `/session/{roomId}`
   - Component fetches session data
   - Connects to Socket.IO server
   - Establishes WebRTC connection

## 🧪 Testing

### Test Appointment Creation

```bash
# As authenticated client
curl -X POST http://localhost:8000/api/appointments \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "therapist_id": 1,
    "starts_at": "2025-01-22 10:00:00"
  }'
```

### Test Session Creation

```bash
# As admin
curl -X POST http://localhost:8000/api/sessions \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "appointment_id": 1
  }'
```

## 📝 Notes

1. **Status Flow Validation:**
   - Appointments: `pending` → `confirmed` → `completed`/`cancelled`
   - Sessions: `scheduled` → `active` → `ended`/`cancelled`
   - Invalid transitions are rejected with 422 status

2. **Access Control:**
   - Clients see only their appointments
   - Therapists see only their appointments
   - Admins see all appointments
   - Session access requires being the appointment's patient or therapist

3. **WebRTC:**
   - Uses STUN servers (Google's public STUN)
   - For production, consider TURN servers for NAT traversal
   - Signaling handled via Socket.IO

4. **Security:**
   - Room IDs are randomly generated (32 chars)
   - Access verified on every request
   - Tokens validated via Laravel Sanctum

## 🐛 Troubleshooting

### Backend Issues

- **Migration errors:** Run `php artisan migrate:fresh` (WARNING: deletes data)
- **API 401 errors:** Check token in localStorage and Sanctum config
- **CORS errors:** Check `config/cors.php` and `SANCTUM_STATEFUL_DOMAINS`

### Frontend Issues

- **API not connecting:** Check `VITE_API_URL` in `.env`
- **Socket.IO not connecting:** Check `VITE_SIGNALING_URL` and signaling server status
- **WebRTC not working:** Check browser permissions for camera/microphone

### Signaling Server Issues

- **Port already in use:** Change `SIGNALING_PORT` in `.env`
- **CORS errors:** Update `CORS_ORIGIN` in `.env` to match frontend URL

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [Socket.IO Documentation](https://socket.io/docs/v4/)
- [WebRTC Documentation](https://webrtc.org/getting-started/overview)

## ✨ Next Steps (Optional Enhancements)

1. Add TURN servers for better NAT traversal
2. Implement recording functionality
3. Add screen sharing
4. Implement chat persistence
5. Add session recording/playback
6. Implement notifications (email/SMS) for session creation
7. Add calendar integration
8. Implement payment processing for appointments



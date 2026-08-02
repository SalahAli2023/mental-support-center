# API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {token}
```

## Language Support

All endpoints support multi-language via:
- Query parameter: `?locale=ar` or `?locale=en`
- Header: `Accept-Language: ar` or `Accept-Language: en`

Default: `en`

## Endpoints

### Authentication

#### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password",
  "phone": "+967712345678",
  "role": "Client"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password"
}
```

Response:
```json
{
  "message": "Login successful",
  "user": { ... },
  "token": "1|..."
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Get User
```http
GET /api/user
Authorization: Bearer {token}
```

### Articles

#### List Articles
```http
GET /api/articles?locale=ar&category_id=1&search=keyword&per_page=15
```

#### Get Article
```http
GET /api/articles/{id}?locale=ar
```

#### Create Article (Auth Required)
```http
POST /api/articles
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "title_ar": "عنوان المقال",
  "title_en": "Article Title",
  "excerpt_ar": "ملخص",
  "excerpt_en": "Excerpt",
  "content_ar": "المحتوى",
  "content_en": "Content",
  "category_id": 1,
  "image": file,
  "is_published": true
}
```

#### Update Article (Auth Required)
```http
PUT /api/articles/{id}
Authorization: Bearer {token}
```

#### Delete Article (Auth Required)
```http
DELETE /api/articles/{id}
Authorization: Bearer {token}
```

#### Get Categories
```http
GET /api/articles/categories/list?locale=ar
```

### Events

#### List Events
```http
GET /api/events?locale=ar&type=workshops&search=keyword
```

#### Get Event
```http
GET /api/events/{id}?locale=ar
```

#### Create Event (Auth Required)
```http
POST /api/events
Authorization: Bearer {token}
```

#### Update Event (Auth Required)
```http
PUT /api/events/{id}
Authorization: Bearer {token}
```

#### Delete Event (Auth Required)
```http
DELETE /api/events/{id}
Authorization: Bearer {token}
```

### Therapists

#### List Therapists
```http
GET /api/therapists?locale=ar&specialization=anxiety&gender=female&search=keyword
```

#### Get Therapist
```http
GET /api/therapists/{id}?locale=ar
```

#### Get Specializations
```http
GET /api/therapists/specializations/list?locale=ar
```

#### Create Therapist (Auth Required)
```http
POST /api/therapists
Authorization: Bearer {token}
```

#### Update Therapist (Auth Required)
```http
PUT /api/therapists/{id}
Authorization: Bearer {token}
```

#### Delete Therapist (Auth Required)
```http
DELETE /api/therapists/{id}
Authorization: Bearer {token}
```

### Library

#### List Library Items
```http
GET /api/library?locale=ar&category_id=1&type=book&search=keyword
```

#### Get Library Item
```http
GET /api/library/{id}?locale=ar
```

#### Get Categories
```http
GET /api/library/categories/list?locale=ar
```

#### Create Library Item (Auth Required)
```http
POST /api/library
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "title_ar": "عنوان",
  "title_en": "Title",
  "type": "book",
  "category_id": 1,
  "cover_image": file,
  "file": file
}
```

#### Update Library Item (Auth Required)
```http
PUT /api/library/{id}
Authorization: Bearer {token}
```

#### Delete Library Item (Auth Required)
```http
DELETE /api/library/{id}
Authorization: Bearer {token}
```

### Measures

#### List Measures
```http
GET /api/measures?locale=ar&category=women
```

#### Get Measure
```http
GET /api/measures/{id}?locale=ar
```

#### Get Measure Questions
```http
GET /api/measures/{id}/questions?locale=ar
```

#### Submit Measure Response
```http
POST /api/measures/{id}/submit
Authorization: Bearer {token}
Content-Type: application/json

{
  "answers": [0, 1, 2, 1, 0, 2, 1]
}
```

#### Create Measure (Auth Required)
```http
POST /api/measures
Authorization: Bearer {token}
```

#### Update Measure (Auth Required)
```http
PUT /api/measures/{id}
Authorization: Bearer {token}
```

#### Delete Measure (Auth Required)
```http
DELETE /api/measures/{id}
Authorization: Bearer {token}
```

### Appointments

#### List Appointments (Auth Required)
```http
GET /api/appointments?status=scheduled
Authorization: Bearer {token}
```

#### Get Appointment (Auth Required)
```http
GET /api/appointments/{id}
Authorization: Bearer {token}
```

#### Create Appointment (Auth Required)
```http
POST /api/appointments
Authorization: Bearer {token}
Content-Type: application/json

{
  "therapist_id": 1,
  "starts_at": "2024-12-01 10:00:00",
  "ends_at": "2024-12-01 11:00:00",
  "notes": "Initial consultation"
}
```

#### Update Appointment (Auth Required)
```http
PUT /api/appointments/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "completed",
  "notes": "Session completed successfully"
}
```

#### Delete Appointment (Auth Required)
```http
DELETE /api/appointments/{id}
Authorization: Bearer {token}
```

### Programs

#### List Programs (Auth Required)
```http
GET /api/programs?locale=ar&search=keyword
Authorization: Bearer {token}
```

#### Get Program (Auth Required)
```http
GET /api/programs/{id}?locale=ar
Authorization: Bearer {token}
```

#### Create Program (Auth Required)
```http
POST /api/programs
Authorization: Bearer {token}
Content-Type: application/json

{
  "title_ar": "برنامج",
  "title_en": "Program",
  "description_ar": "الوصف",
  "description_en": "Description",
  "sessions": [
    {
      "title_ar": "جلسة 1",
      "title_en": "Session 1",
      "content_ar": "المحتوى",
      "content_en": "Content"
    }
  ]
}
```

#### Update Program (Auth Required)
```http
PUT /api/programs/{id}
Authorization: Bearer {token}
```

#### Delete Program (Auth Required)
```http
DELETE /api/programs/{id}
Authorization: Bearer {token}
```

### Legal Resources

#### List Legal Resources
```http
GET /api/legal-resources?locale=ar&law_type=constitution&category_id=1
```

#### Get Legal Resource
```http
GET /api/legal-resources/{id}?locale=ar
```

#### Create Legal Resource (Auth Required)
```http
POST /api/legal-resources
Authorization: Bearer {token}
```

#### Update Legal Resource (Auth Required)
```http
PUT /api/legal-resources/{id}
Authorization: Bearer {token}
```

#### Delete Legal Resource (Auth Required)
```http
DELETE /api/legal-resources/{id}
Authorization: Bearer {token}
```

### Dashboard

#### Get Dashboard Stats (Auth Required - Admin Only)
```http
GET /api/dashboard/stats
Authorization: Bearer {token}
```

Response:
```json
{
  "total_users": 100,
  "total_clients": 80,
  "total_therapists": 15,
  "total_articles": 50,
  "published_articles": 45,
  "total_events": 20,
  "upcoming_appointments": 10,
  "total_appointments": 200,
  "new_users_this_month": 5
}
```

## Response Format

All responses follow this format:

### Success Response
```json
{
  "data": { ... },
  "message": "Success message"
}
```

### Paginated Response
```json
{
  "data": [ ... ],
  "current_page": 1,
  "per_page": 15,
  "total": 100,
  "last_page": 7
}
```

### Error Response
```json
{
  "message": "Error message",
  "errors": {
    "field": ["Error message"]
  }
}
```

## Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

## File Uploads

For file uploads, use `multipart/form-data`:
- Images: `image`, `cover_image`, `media`
- Files: `file` (for library items)

Files are stored in `storage/app/public` and accessible via `/storage/{path}`.

## Rate Limiting

API requests are rate-limited. Default limits:
- 60 requests per minute for authenticated users
- 30 requests per minute for unauthenticated users



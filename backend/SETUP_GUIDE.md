# Backend Setup Guide

## Quick Start

1. **Install Dependencies**
   ```bash
   cd backend
   composer install
   ```

2. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure Database**
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=psychological_support
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Seed Database**
   ```bash
   php artisan db:seed
   ```

6. **Create Storage Link**
   ```bash
   php artisan storage:link
   ```

7. **Start Server**
   ```bash
   php artisan serve
   ```

## Default Credentials

After running seeders, you can login with:

- **Admin**: admin@example.com / password
- **Therapist**: therapist@example.com / password
- **Client**: client@example.com / password

## API Base URL

```
http://localhost:8000/api
```

## Testing API

### Register User
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### Get Articles (Public)
```bash
curl http://localhost:8000/api/articles
```

### Get Articles (Authenticated)
```bash
curl http://localhost:8000/api/articles \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Frontend Integration

In your Vue.js frontend, configure the API base URL:

```javascript
// src/config/api.js
export const API_BASE_URL = 'http://localhost:8000/api'

// Example API call
import axios from 'axios'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Add token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})
```

## CORS Configuration

Update `config/sanctum.php` to allow your frontend domain:

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 
    'localhost,localhost:3000,localhost:5173,127.0.0.1:5173'
)),
```

And in `.env`:
```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:5173
FRONTEND_URL=http://localhost:5173
```

## File Storage

Files are stored in `storage/app/public`. Make sure to:
1. Create the storage link: `php artisan storage:link`
2. Set proper permissions on `storage` and `bootstrap/cache` directories

## Troubleshooting

### Migration Errors
If you get foreign key constraint errors, make sure to run migrations in order or reset the database:
```bash
php artisan migrate:fresh --seed
```

### Token Issues
Clear cache and config:
```bash
php artisan config:clear
php artisan cache:clear
```

### Storage Issues
Make sure storage link exists:
```bash
php artisan storage:link
```

## Next Steps

1. Configure your database connection
2. Run migrations and seeders
3. Test API endpoints
4. Integrate with frontend
5. Configure file storage
6. Set up production environment variables



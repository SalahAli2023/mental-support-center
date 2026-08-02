# Psychological Support Platform - Backend API

Laravel 12 API backend for the Psychological Support Platform.

## Features

- **Authentication**: Laravel Sanctum for API token authentication
- **Multi-language Support**: Arabic and English content support
- **RESTful API**: Clean REST API endpoints for all resources
- **Role-based Access**: Admin, Therapist, and Client roles
- **Comprehensive Models**: Articles, Events, Therapists, Library, Measures, Appointments, Programs, Legal Resources

## Requirements

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM (for frontend)

## Installation

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure Database**
   Edit `.env` file and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=psychological_support
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Seed Database (Optional)**
   ```bash
   php artisan db:seed
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   ```

   API will be available at: `http://localhost:8000/api`

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires auth)
- `GET /api/user` - Get authenticated user (requires auth)

### Articles
- `GET /api/articles` - List articles
- `GET /api/articles/{id}` - Get article details
- `POST /api/articles` - Create article (requires auth)
- `PUT /api/articles/{id}` - Update article (requires auth)
- `DELETE /api/articles/{id}` - Delete article (requires auth)
- `GET /api/articles/categories/list` - Get categories

### Events
- `GET /api/events` - List events
- `GET /api/events/{id}` - Get event details
- `POST /api/events` - Create event (requires auth)
- `PUT /api/events/{id}` - Update event (requires auth)
- `DELETE /api/events/{id}` - Delete event (requires auth)

### Therapists
- `GET /api/therapists` - List therapists
- `GET /api/therapists/{id}` - Get therapist details
- `POST /api/therapists` - Create therapist (requires auth)
- `PUT /api/therapists/{id}` - Update therapist (requires auth)
- `DELETE /api/therapists/{id}` - Delete therapist (requires auth)
- `GET /api/therapists/specializations/list` - Get specializations

### Library
- `GET /api/library` - List library items
- `GET /api/library/{id}` - Get library item details
- `POST /api/library` - Create library item (requires auth)
- `PUT /api/library/{id}` - Update library item (requires auth)
- `DELETE /api/library/{id}` - Delete library item (requires auth)
- `GET /api/library/categories/list` - Get categories

### Measures
- `GET /api/measures` - List measures
- `GET /api/measures/{id}` - Get measure details
- `GET /api/measures/{id}/questions` - Get measure questions
- `POST /api/measures/{id}/submit` - Submit measure response
- `POST /api/measures` - Create measure (requires auth)
- `PUT /api/measures/{id}` - Update measure (requires auth)
- `DELETE /api/measures/{id}` - Delete measure (requires auth)

### Appointments
- `GET /api/appointments` - List appointments (requires auth)
- `GET /api/appointments/{id}` - Get appointment details (requires auth)
- `POST /api/appointments` - Create appointment (requires auth)
- `PUT /api/appointments/{id}` - Update appointment (requires auth)
- `DELETE /api/appointments/{id}` - Delete appointment (requires auth)

### Programs
- `GET /api/programs` - List programs (requires auth)
- `GET /api/programs/{id}` - Get program details (requires auth)
- `POST /api/programs` - Create program (requires auth)
- `PUT /api/programs/{id}` - Update program (requires auth)
- `DELETE /api/programs/{id}` - Delete program (requires auth)

### Legal Resources
- `GET /api/legal-resources` - List legal resources
- `GET /api/legal-resources/{id}` - Get legal resource details
- `POST /api/legal-resources` - Create legal resource (requires auth)
- `PUT /api/legal-resources/{id}` - Update legal resource (requires auth)
- `DELETE /api/legal-resources/{id}` - Delete legal resource (requires auth)

### Dashboard
- `GET /api/dashboard/stats` - Get dashboard statistics (requires auth)

## Authentication

The API uses Laravel Sanctum for token-based authentication. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

## CORS Configuration

CORS is configured to allow requests from the frontend. Update `config/sanctum.php` and `.env` file to configure allowed origins.

## Database Structure

### Main Tables
- `users` - User accounts (Admin, Therapist, Client)
- `articles` - Mental health articles
- `article_categories` - Article categories
- `events` - Workshops, evenings, events
- `therapists` - Therapist profiles
- `specializations` - Therapist specializations
- `library_items` - Library resources (books, research, guides)
- `library_categories` - Library categories
- `measures` - Psychological assessment measures
- `measure_questions` - Measure questions
- `measure_responses` - User responses to measures
- `appointments` - Therapy appointments
- `programs` - Treatment programs
- `program_sessions` - Program sessions
- `legal_resources` - Yemeni laws and legal resources
- `legal_resource_categories` - Legal resource categories

## File Storage

Uploaded files (images, documents) are stored in `storage/app/public`. Create a symbolic link:

```bash
php artisan storage:link
```

## Testing

Run tests with:
```bash
php artisan test
```

## License

Proprietary - Psychological Support Platform

# Postman Collection Setup Guide

## 📥 Importing the Collection

1. **Open Postman**
   - Launch Postman application

2. **Import Collection**
   - Click on "Import" button (top left)
   - Select "File" tab
   - Choose `postman_collection.json` from the `backend/` folder
   - Click "Import"

3. **Set up Environment Variables**
   - After importing, you'll see the collection "Psychological Support Platform API"
   - The collection uses environment variables:
     - `base_url`: `http://localhost:8000` (default)
     - `auth_token`: Will be automatically set after login
     - `user_id`: Will be automatically set after login

## 🔧 Environment Setup

### Option 1: Use Collection Variables (Recommended)
The collection already includes variables. You can modify them:
1. Right-click on the collection
2. Select "Edit"
3. Go to "Variables" tab
4. Update `base_url` if your server runs on a different port

### Option 2: Create Postman Environment
1. Click on "Environments" (left sidebar)
2. Click "+" to create new environment
3. Add variables:
   - `base_url`: `http://localhost:8000`
   - `auth_token`: (leave empty, will be set automatically)
   - `user_id`: (leave empty, will be set automatically)
4. Save and select the environment

## 🚀 Using the Collection

### 1. Start Your Laravel Server
```bash
cd backend
php artisan serve
```

### 2. Test Authentication

#### Login Flow:
1. Open "Authentication" folder
2. Use "Login" request (uses admin@example.com by default)
3. After successful login, the `auth_token` will be automatically saved
4. All subsequent requests will use this token

#### Default Test Users:
- **Admin**: `admin@example.com` / `password`
- **Therapist**: `therapist@example.com` / `password`
- **Client**: `client@example.com` / `password`

### 3. Test API Endpoints

#### Public Endpoints (No Authentication Required):
- Articles (List, Get)
- Events (List, Get)
- Therapists (List, Get, Specializations)
- Library (List, Get, Categories)
- Measures (List, Get, Questions)
- Legal Resources (List, Get)

#### Protected Endpoints (Authentication Required):
- All CRUD operations for Articles, Events, Therapists, Library, Measures
- Appointments (all operations)
- Programs (all operations)
- Dashboard Stats

### 4. Testing Multi-language Support

Most endpoints support locale parameter:
- Add `?locale=ar` for Arabic content
- Add `?locale=en` for English content (default)
- Or use `Accept-Language` header: `ar` or `en`

Example:
```
GET /api/articles?locale=ar
```

## 📝 Request Examples

### Creating an Article
1. Go to "Articles" → "Create Article"
2. Update the request body with your data
3. Make sure you're logged in (token is set)
4. Send request

### Creating a Library Item with File Upload
1. Go to "Library" → "Create Library Item"
2. The request is set to `form-data` mode
3. Fill in text fields
4. For file uploads, click on "cover_image" or "file" fields
5. Select "File" type and choose your file
6. Send request

### Submitting a Measure Response
1. First, get a measure: "Measures" → "Get Measure"
2. Get measure questions: "Measures" → "Get Measure Questions"
3. Submit response: "Measures" → "Submit Measure Response"
4. Include answers array in the request body

## 🔑 Authentication Flow

The collection includes automatic token handling:
1. When you login using "Login" request
2. The response script automatically extracts the token
3. Saves it to `auth_token` variable
4. All protected requests use `Bearer {{auth_token}}` header

To manually set token:
1. Copy token from login response
2. Update `auth_token` variable in collection/environment
3. Or manually add `Authorization: Bearer <token>` header

## 🌐 Base URL Configuration

If your backend runs on a different URL:
1. Update `base_url` variable:
   - Development: `http://localhost:8000`
   - Production: `https://your-domain.com`
2. All requests will use this base URL automatically

## 📊 Testing Scenarios

### Complete Flow Test:
1. **Register** a new user
2. **Login** with credentials
3. **Get Current User** to verify authentication
4. **Create Article** as authenticated user
5. **List Articles** to see your article
6. **Update Article** to modify it
7. **Delete Article** to remove it

### Multi-language Test:
1. **List Articles** with `?locale=ar`
2. Verify Arabic content in response
3. **List Articles** with `?locale=en`
4. Verify English content in response

### File Upload Test:
1. **Create Library Item** with file upload
2. Verify file is uploaded
3. Check file URL in response
4. Access file via URL

## 🐛 Troubleshooting

### Token Not Working
- Make sure you've logged in first
- Check if token is set in variables
- Verify token hasn't expired
- Try logging in again

### CORS Errors
- Make sure Sanctum is configured correctly
- Check `config/sanctum.php` for stateful domains
- Verify frontend URL is in allowed domains

### 401 Unauthorized
- Check if endpoint requires authentication
- Verify token is included in Authorization header
- Make sure token is valid

### 404 Not Found
- Verify base_url is correct
- Check if route exists in `routes/api.php`
- Make sure server is running

### 422 Validation Error
- Check request body format
- Verify all required fields are provided
- Check validation rules in controller

## 📚 Additional Resources

- **API Documentation**: See `API_DOCUMENTATION.md`
- **Setup Guide**: See `SETUP_GUIDE.md`
- **Laravel Docs**: https://laravel.com/docs
- **Sanctum Docs**: https://laravel.com/docs/sanctum

## 🎯 Quick Start Checklist

- [ ] Import `postman_collection.json`
- [ ] Set `base_url` variable
- [ ] Start Laravel server (`php artisan serve`)
- [ ] Run migrations (`php artisan migrate`)
- [ ] Seed database (`php artisan db:seed`)
- [ ] Test login with default credentials
- [ ] Verify token is set automatically
- [ ] Test a protected endpoint
- [ ] Test multi-language support
- [ ] Test file upload (if needed)

## 💡 Tips

1. **Use Collections**: Organize requests in folders
2. **Use Variables**: Make requests reusable
3. **Save Examples**: Save response examples for documentation
4. **Use Tests**: Add automated tests to requests
5. **Use Pre-request Scripts**: Set up dynamic values
6. **Export Collection**: Share with team members

## 🔄 Updating the Collection

If API endpoints change:
1. Update requests in Postman
2. Export updated collection
3. Replace `postman_collection.json`
4. Share with team

---

Happy Testing! 🚀



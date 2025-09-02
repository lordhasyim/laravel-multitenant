# Laravel Multi-Tenant REST API

A comprehensive Laravel 12 REST API application with multi-tenancy, JWT authentication, and role-based access control (RBAC).

## Tech Stack

- **Laravel:** 12.x
- **PHP:** 8.4.1
- **Database:** MySQL 8.0.41
- **Multi-Tenancy:** [stancl/tenancy](https://github.com/archtechx/tenancy)
- **Authentication:** JWT via [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth)
- **Authorization:** [spatie/laravel-permission](https://github.com/spatie/laravel-permission)

## Features

- ✅ Multi-tenant architecture with separate databases per tenant
- ✅ JWT-based authentication
- ✅ Role-based access control (RBAC)
- ✅ Master permission management
- ✅ Tenant-specific roles and permissions
- ✅ Comprehensive API endpoints

## Installation

### Prerequisites

- PHP 8.4.1 or higher
- Composer
- MySQL 8.0.41 or higher
- Node.js (for frontend assets, if needed)

### Setup Steps

1. **Clone the repository**
```bash
git clone <repository-url>
cd laravel-multitenant-api
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure your `.env` file**
```env
APP_NAME="MultiTenant API"
APP_ENV=local
APP_KEY=base64:your-app-key
APP_DEBUG=true
APP_URL=http://localhost

# Central Database Configuration
DB_CONNECTION=central
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=multitenant_central
DB_USERNAME=root
DB_PASSWORD=your_password

# JWT Configuration
JWT_SECRET=your-jwt-secret-key
JWT_TTL=60
```

5. **Create databases**
```sql
-- Create central database
CREATE DATABASE multitenant_central;

-- Tenant databases will be created automatically
```

6. **Run migrations**
```bash
# Central database migrations
php artisan migrate --database=central

# Generate JWT secret
php artisan jwt:secret
```

7. **Seed master data**
```bash
# Seed master permissions (central database)
php artisan db:seed --class=MasterPermissionSeeder
```

## Multi-Tenancy Setup

### Creating Tenants

Tenants are created with their own database and configuration:

```php
// Create a tenant programmatically
$tenant = Tenant::create(['id' => '550e8400-e29b-41d4-a716-446655440000']);
$tenant->domains()->create(['domain' => 'domain.com']);
```

## Running Setup Via Command
```bash
# Central database
php artisan migrate --database=central

# Seed master permissions
php artisan db:seed --class=MasterPermissionSeeder
```
## Create tenants using the command
```bash
# Auto-generate slug from name
php artisan tenant:create "Alamsegar" "admin@alamsegar.com"
# Creates: slug=alamsegar, db=tenant_alamsegar

# With custom slug
php artisan tenant:create "Kretek Jaya" "admin@kretekjaya.com" --slug="kretekjaya"  
# Creates: slug=kretekjaya, db=tenant_kretekjaya

# With spaces and special characters (auto-slugified)
php artisan tenant:create "PT. Multi Corp Indonesia" "admin@multicorp.co.id"
# Creates: slug=pt_multi_corp_indonesia, db=tenant_pt_multi_corp_indonesia
```
## Example using command
```bash
$ php artisan tenant:create "Alamsegar" "admin@alamsegar.com"
Tenant created successfully!
+--------------+--------------------------------------+
| Field        | Value                                |
+--------------+--------------------------------------+
| ID           | 25e6397d-5851-4443-94db-f6b8dc010814 |
| Name         | Alamsegar                            |
| Slug         | alamsegar                            |
| Email        | admin@alamsegar.com                  |
| Database     | tenant_alamsegar                     |
| Storage Path | tenants/alamsegar                    |
+--------------+--------------------------------------+

 Do you want to run tenant migrations? (yes/no) [yes]:
 > yes

Tenant: 25e6397d-5851-4443-94db-f6b8dc010814

   INFO  Nothing to migrate.  

 Do you want to seed tenant data? (yes/no) [yes]:
 > yes

Tenant: 25e6397d-5851-4443-94db-f6b8dc010814

   INFO  Seeding database.  


```
#### With custom slug

```bash
$ php artisan tenant:create "Kretek Jaya" "admin@kretekjaya.com" --slug="kretekjaya"  
Tenant created successfully!
+--------------+--------------------------------------+
| Field        | Value                                |
+--------------+--------------------------------------+
| ID           | 3c6b30e2-c068-477c-bbe7-277e4a22ddaa |
| Name         | Kretek Jaya                          |
| Slug         | kretekjaya                           |
| Email        | admin@kretekjaya.com                 |
| Database     | tenant_kretekjaya                    |
| Storage Path | tenants/kretekjaya                   |
+--------------+--------------------------------------+

 Do you want to run tenant migrations? (yes/no) [yes]:
 > yes

Tenant: 3c6b30e2-c068-477c-bbe7-277e4a22ddaa

   INFO  Nothing to migrate.  

 Do you want to seed tenant data? (yes/no) [yes]:
 > yes

Tenant: 3c6b30e2-c068-477c-bbe7-277e4a22ddaa

   INFO  Seeding database.  


```

#### With spaces and special characters (auto-slugified)
- This will Creates: slug=pt_multi_corp_indonesia, db=tenant_pt_multi_corp_indonesia
```bash
$ php artisan tenant:create "PT. Multi Corp Indonesia" "admin@multicorp.co.id"
Tenant created successfully!
+--------------+--------------------------------------+
| Field        | Value                                |
+--------------+--------------------------------------+
| ID           | b3cc623b-fea6-454d-a5fe-33c2f9556168 |
| Name         | PT. Multi Corp Indonesia             |
| Slug         | pt_multi_corp_indonesia              |
| Email        | admin@multicorp.co.id                |
| Database     | tenant_pt_multi_corp_indonesia       |
| Storage Path | tenants/pt_multi_corp_indonesia      |
+--------------+--------------------------------------+

 Do you want to run tenant migrations? (yes/no) [yes]:
 > yes

Tenant: b3cc623b-fea6-454d-a5fe-33c2f9556168

   INFO  Nothing to migrate.  

 Do you want to seed tenant data? (yes/no) [yes]:
 > yes

Tenant: b3cc623b-fea6-454d-a5fe-33c2f9556168

   INFO  Seeding database.
```


### Tenant Database Setup

For each tenant, run:

```bash
# Migrate tenant database
php artisan tenants:migrate

# Seed tenant permissions and roles
php artisan tenants:seed --class=TenantPermissionSeeder
php artisan tenants:seed --class=TenantRoleSeeder

# Or run all tenant seeders
php artisan tenants:seed
```

## Testing the Setup

### Tenant Applications
- **Tenant 1**: `http://multitenant.test:8000`
- **Tenant 2**: `http://multitenant.test:8000`
- **Expected**: "This is your multi-tenant application. The id of the current tenant is {tenant_id}"

### API Testing with Postman/Insomnia

1. Set base URL to tenant domain (e.g., `http://domain.com`)
2. Register a user or login to get JWT token
3. Add `Authorization: Bearer {token}` header to protected routes
4. Add X-Tenant-Id : tenant_id in header
    - **X-Tenant-Id**: `1`
5. Test CRUD operations based on user permissions


## Permission System

### Master Permissions (Central Database)

Master permissions are defined centrally and synced to tenant databases:

- **Products:** `create-product`, `view-product`, `edit-product`, `delete-product`
- **Users:** `create-user`, `view-user`, `edit-user`, `delete-user`

## Example for testing the functional of the apps
### Tenant-Specific Roles

Each tenant can have custom roles based on their business needs:

**Tenant 1 (Alamsegar):**
- `manager` - Full product permissions
- `operator` - View-only permissions
- `admin` - All permissions

**Tenant 2 (Kretekjaya):**
- `supervisor` - Full product permissions  
- `staff` - View-only permissions
- `admin` - All permissions

## API Endpoints

### Authentication Endpoints

All authentication endpoints work within tenant context.
### API Usage Example

#### Register User
```http
POST /api/register
X-Tenant-Id: 550e8400-e29b-41d4-a716-446655440000
Content-Type: application/json

{
    "name": "John Doe",  
    "email": "john@alamsegar.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-08-29T10:00:00Z",
        "updated_at": "2025-08-29T10:00:00Z"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

#### Login
```http
POST /api/login
X-Tenant-Id: 550e8400-e29b-41d4-a716-446655440000
Content-Type: application/json
Host: domain.com

{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    }
}
```

#### Get User Profile
```http
GET /api/me
X-Tenant-Id: 550e8400-e29b-41d4-a716-446655440000
Authorization: Bearer {token}
Host: domain.com
```

**Response:**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "roles": ["manager"],
        "permissions": ["create-product", "view-product", "edit-product", "delete-product"]
    },
    "tenant": "550e8400-e29b-41d4-a716-446655440000"
}
```

#### Logout
```http
POST /api/logout
X-Tenant-Id: 550e8400-e29b-41d4-a716-446655440000
Authorization: Bearer {token}
Host: domain.com
```

**Response:**
```json
{
    "message": "Successfully logged out"
}
```

## Architecture Overview
- Tenant Database is using schema tenant_{tenant_name} ex: tenant_starcompany
### Database Structure

```
Central Database (multitenant_central):
├── tenants
├── domains  
├── master_permissions
└── migrations

Tenant Database (tenant_1, tenant_2, etc.):
├── users
├── permissions
├── roles
├── model_has_permissions
├── model_has_roles
├── role_has_permissions
└── your_business_tables (products, orders, etc.)
```

### Slug will be used in
- Database naming: tenant_alamsegar (clean and readable)
- File storage: storage/tenants/alamsegar/uploads/
- Folder organization: public/tenants/alamsegar/assets/

### Configuration Files

#### Tenancy Configuration (`config/tenancy.php`)
- Central database connection: `central`
- Tenant database prefix: `tenant_`
- Bootstrappers: Database, Cache, Filesystem, Queue

#### Permission Configuration (`config/permission.php`)
- Default guard: `api` (for JWT)
- Cache expiration: 24 hours
- Teams feature: Disabled

#### Database Configuration (`config/database.php`)
- Central connection for multi-tenancy management
- MySQL connections for tenant databases

## Development Workflow

### Adding New Permissions

1. **Add to MasterPermissionSeeder:**
```php
['name' => 'new-permission', 'category' => 'category', 'description' => 'Description']
```

2. **Reseed master permissions:**
```bash
php artisan db:seed --class=MasterPermissionSeeder
```

3. **Sync to all tenants:**
```bash
php artisan tenants:seed --class=TenantPermissionSeeder
```

### Adding New Tenants

1. **Create tenant and domain:**
```php
$tenant = Tenant::create(['id' => 'new-tenant']);
$tenant->domains()->create(['domain' => 'new-tenant.yourdomain.com']);
```

2. **Setup tenant database:**
```bash
php artisan tenants:migrate --tenants=new-tenant
php artisan tenants:seed --tenants=new-tenant
```

## Security Considerations

- JWT tokens expire after 60 minutes (configurable)
- All API endpoints require authentication except login/register
- Role-based access control enforced at API level
- Tenant isolation at database level
- Password hashing using Laravel's default hasher

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

### API Testing with Postman/Insomnia

1. Set base URL to tenant domain (e.g., `http://domain.com`)
2. Register a user or login to get JWT token
3. Add `Authorization: Bearer {token}` header to protected routes
4. Test CRUD operations based on user permissions

## Common Issues & Troubleshooting

### Permission Guard Mismatch
**Error:** `There is no permission named 'permission-name' for guard 'web'`

**Solution:** Ensure all permissions and roles use `api` guard:
```php
// In seeders
Role::create(['name' => 'role-name', 'guard_name' => 'api']);
Permission::create(['name' => 'permission-name', 'guard_name' => 'api']);
```

### Database Connection Issues
**Error:** `Database connection [central] not configured`

**Solution:** Add central connection to `config/database.php`:
```php
'central' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'database' => env('DB_DATABASE', 'multitenant_central'),
    // ... other config
],
```

### JWT Token Issues
**Error:** `Token has expired`

**Solution:** Configure JWT TTL in `.env`:
```env
JWT_TTL=60  # Token valid for 60 minutes
```

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support and questions, please open an issue in the GitHub repository or contact the development team.

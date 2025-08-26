# Laravel Multi-Tenant API

A Laravel 12 multi-tenant REST API application with domain-based tenant identification, custom database per tenant, and JWT authentication.

## Tech Stack

- **Laravel**: 12.25.0
- **PHP**: 8.4.1
- **MySQL**: 8.0.41
- **Multi-tenancy**: [ArchTech Tenancy v3.9.1](https://github.com/archtechx/tenancy)
- **Authentication**: JWT (tymondesigns/jwt-auth)
- **Permissions**: Spatie Laravel Permission

## Architecture Overview

- **Landlord Database**: `laravel_multitenant_landlord` (central app, tenants, domains)
- **Tenant Databases**: `tenant_{tenant_id}` (isolated per tenant)
- **Domain Identification**: `tenant.multitenant-api.test` format
- **Permission System**: Master permissions in landlord DB, tenant-specific roles

## Installation

### Prerequisites

Ensure you have these installed on your system:

```bash
# Verify versions
php --version          # Should be >= 8.4.1
composer --version     # Latest version
mysql --version        # Should be >= 8.0.41

# Required PHP extensions
php -m | grep -E "(pdo|mysql|mbstring|xml|ctype|json|tokenizer|openssl|curl)"
```

### Step 1: Clone Repository

```bash
git clone <repository-url>
cd laravel-multitenant-api
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Environment Configuration

Edit `.env` file:

```env
APP_NAME="Multi-Tenant API"
APP_URL=http://multitenant-api.test
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_multitenant_landlord
DB_USERNAME=root
DB_PASSWORD=root
```

### Step 4: Database Setup

```bash
# Create landlord database
mysql -u root -p
```

In MySQL:
```sql
CREATE DATABASE laravel_multitenant_landlord;
EXIT;
```

### Step 5: Run Migrations

```bash
# Run landlord database migrations
php artisan migrate
```

### Step 6: Local Domain Setup

Add these lines to `/etc/hosts`:

```bash
sudo nano /etc/hosts
```

Add:
```
127.0.0.1   multitenant-api.test
127.0.0.1   alamsegar.multitenant-api.test
127.0.0.1   kretekjaya.multitenant-api.test
127.0.0.1   starcompany.multitenant-api.test
```

### Step 7: Create Sample Tenants

```bash
# Create tenants using custom command
php artisan tenant:create alamsegar "Alam Segar Company" "admin@alamsegar.com" "alamsegar.multitenant-api.test"

php artisan tenant:create kretekjaya "Kretek Jaya Company" "admin@kretekjaya.com" "kretekjaya.multitenant-api.test"
```

### Step 8: Start Development Server

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Testing the Setup

### Central Application (Landlord)
- **URL**: `http://multitenant-api.test:8000`
- **Test Route**: `http://multitenant-api.test:8000/central`
- **Expected**: "This is the CENTRAL (landlord) application!"

### Tenant Applications
- **Tenant 1**: `http://alamsegar.multitenant-api.test:8000`
- **Tenant 2**: `http://kretekjaya.multitenant-api.test:8000`
- **Expected**: "This is your multi-tenant application. The id of the current tenant is {tenant_id}"

## Database Structure

### Landlord Database (`laravel_multitenant_landlord`)

**Tenants Table:**
```sql
- id (varchar) - Tenant identifier (e.g., "alamsegar")
- name (varchar) - Company name
- email (varchar) - Admin email
- domain (varchar) - Tenant domain
- db_name (varchar) - Tenant database name
- db_host (varchar) - Database host
- db_port (varchar) - Database port
- db_username (varchar) - Database username
- db_password (varchar) - Database password
- created_at, updated_at, data (json)
```

**Domains Table:**
```sql
- id (int) - Auto increment
- domain (varchar) - Domain name
- tenant_id (varchar) - Foreign key to tenants
- created_at, updated_at
```

### Tenant Databases
Each tenant has its own database named `tenant_{tenant_id}`:
- `tenant_alamsegar`
- `tenant_kretekjaya`
- etc.

## Artisan Commands

### Tenant Management

```bash
# Create a new tenant
php artisan tenant:create {id} {name} {email} {domain}

# Example
php artisan tenant:create mycompany "My Company Ltd" "admin@mycompany.com" "mycompany.multitenant-api.test"

# Run migrations on all tenant databases
php artisan tenants:migrate

# Run seeders on all tenant databases
php artisan tenants:seed
```

### Standard Laravel Commands

```bash
# Clear application caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Check migration status
php artisan migrate:status

# List all routes
php artisan route:list
```

## Project Structure

```
app/
├── Console/Commands/
│   └── CreateTenant.php       # Custom tenant creation command
├── Models/
│   ├── Tenant.php            # Tenant model with custom fields
│   └── Domain.php            # Domain model
config/
├── tenancy.php               # Tenancy configuration
database/
├── migrations/
│   ├── tenant/               # Tenant-specific migrations
│   ├── *_create_tenants_table.php
│   ├── *_create_domains_table.php
│   └── *_add_fields_to_tenants_table.php
routes/
├── web.php                   # Central app routes
├── tenant.php               # Tenant app routes
```

## Troubleshooting

### Common Issues

**1. "Call to undefined method domains()"**
- Ensure models are properly configured in `config/tenancy.php`
- Check that custom models extend the correct base classes

**2. "404 Not Found" on tenant domains**
- Verify `/etc/hosts` entries are correct
- Check `central_domains` configuration in `config/tenancy.php`
- Ensure tenant and domain records exist in database

**3. Database connection errors**
- Verify MySQL credentials in `.env`
- Ensure landlord database exists
- Check MySQL service is running

**4. Tenant database not created**
- Check tenant creation completed successfully
- Verify database naming matches configuration
- Run `SHOW DATABASES LIKE 'tenant%';` in MySQL

### Logs and Debugging

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Enable query logging (add to AppServiceProvider)
DB::enableQueryLog();
dd(DB::getQueryLog());
```

## Development Workflow

1. **Add new features**: Work on central app first, then tenant-specific features
2. **Database changes**: Create migrations for both landlord and tenant databases
3. **Testing**: Test on multiple tenants to ensure isolation
4. **Domain management**: Add new domains to `/etc/hosts` for local testing

## Production Considerations

- Use proper domain/subdomain setup with SSL certificates
- Implement proper database credentials per tenant
- Set up automated tenant provisioning
- Configure proper caching strategies
- Implement database backup strategies per tenant

## Next Steps

- [ ] Implement JWT authentication
- [ ] Add permission system (master permissions + tenant roles)
- [ ] Create API endpoints for tenant management
- [ ] Add user management per tenant
- [ ] Implement proper error handling and logging

---

**Note**: This is a development setup. For production deployment, additional security, performance, and reliability configurations are required.
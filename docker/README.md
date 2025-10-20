# Docker Setup for BKK Esemkasari

## Local Development Setup

This Docker setup provides a simple development environment with:
- **app**: Laravel application (PHP 8.2-FPM)
- **nginx**: Web server
- **mysql**: MySQL 8.0 database
- **vite**: Vite development server for HMR

## Quick Start

1. **Clone and setup the project:**
   ```bash
   git clone <repository-url>
   cd bkk-esemkasari
   ```

2. **Copy environment file:**
   ```bash
   cp .env.docker .env
   ```

3. **Generate application key:**
   ```bash
   # You can do this after containers are running
   docker-compose exec app php artisan key:generate
   ```

4. **Build and start containers:**
   ```bash
   docker-compose up -d --build
   ```

5. **Install dependencies and setup database:**
   ```bash
   # Install dependencies (if not already done in build)
   docker-compose exec app composer install
   docker-compose exec app npm install

   # Run migrations
   docker-compose exec app php artisan migrate

   # Create storage link
   docker-compose exec app php artisan storage:link
   ```

## Access Points

- **Application**: http://localhost:8000
- **Vite Dev Server**: http://localhost:5173
- **MySQL**: localhost:3306
  - Database: `bkk_esemkasari`
  - Username: `root`
  - Password: `root`

## Useful Commands

```bash
# View logs
docker-compose logs -f

# Enter app container
docker-compose exec app bash

# Enter mysql container
docker-compose exec mysql mysql -u root -p

# Run artisan commands
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller SomeController

# Install packages
docker-compose exec app composer require package-name
docker-compose exec app npm install package-name

# Stop containers
docker-compose down

# Stop and remove volumes (careful - this removes database data)
docker-compose down -v
```

## Development Workflow

1. **Frontend Development**: The Vite container provides hot module replacement. Edit files in `resources/js/` and see changes instantly.

2. **Backend Development**: Edit PHP files and they'll be reflected immediately due to volume mounting.

3. **Database**: MySQL data persists in Docker volumes. Use migrations for schema changes.

## Notes

- The setup mounts the entire project directory for development convenience
- File uploads will be stored in `storage/app/public/`
- Logs are available via `docker-compose logs`
- For production deployment, you'll need additional security and optimization configurations

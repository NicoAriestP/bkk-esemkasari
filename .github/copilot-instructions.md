
# BKK Esemkasari – AI Coding Agent Guide

## Project Overview
BKK Esemkasari is a job fair management system for SMKN Purwosari Bojonegoro, built with Laravel 12, Inertia.js, Vue 3, TypeScript, and PrimeVue v4 with Tailwind CSS v4.

**Core Features:**
- Tracer study questionnaires (multi-answer types)
- Announcements with VirtualScroller lazy loading
- Job vacancy screening and application workflow
- Multi-guard authentication (admin/student/partner)
- File upload system with auto-organization by model type

## Architecture & Data Flow
- **Multi-Guard Auth:**
  - Admin (`auth:web`): Full CRUD operations
  - Student (`auth:student`): View announcements, complete tracer study, apply to vacancies
  - Partner (`auth:partner`): Manage vacancies, screen applications
  - Route separation: `routes/web.php` uses middleware groups, `routes/auth.php` handles all guards
  - Frontend guard detection: `usePage().props.auth.activeGuard`
- **Action-Controller Pattern:**
  - Business logic in `app/Actions/{Entity}/{Entity}Action.php` (e.g., `StudentAction::save()`)
  - Controllers delegate to Actions, never handle business logic directly
  - Actions return models, Controllers handle HTTP responses and redirects
  - Example: `StudentController@store` calls `StudentAction::save()` for all validation and business logic
- **Observer Pattern:**
  - All models use observers in `app/Observers/` extending `BaseObserver`
  - `BaseObserver` auto-authenticates user ID 1 in console for seeders/migrations
  - Observers handle created_by/updated_by via `Blameable` trait
  - Registered in `app/Providers/AppServiceProvider.php`
- **Data Hierarchy:**
  - Year → StudentClass → Students (nested educational structure)
  - Partners → Vacancies → VacancyApplications (job workflow)
  - Students → TracerStudy Answers (one-to-one per answer type: activity/working/university/entrepreneur)

## Essential Developer Workflows

### Local Development (Non-Docker)
```bash
composer run dev    # Laravel server + queue + Vite HMR (color-coded)
composer run dev:ssr  # With Inertia SSR + Laravel Pail logs
```

### Docker Development with HTTPS
```bash
# First-time setup
./docker/generate-ssl.sh              # Generate self-signed SSL certificate
echo "127.0.0.1 bkk-esemkasari.dev" | sudo tee -a /etc/hosts
docker compose up -d --build          # Start containers
docker exec -it bkk-esemkasari-app php artisan migrate
docker exec -it bkk-esemkasari-app php artisan storage:link

# Daily workflow
docker compose up -d                  # Start Docker (if not running)
npm run dev                           # Start Vite with HTTPS support
# Access: https://bkk-esemkasari.dev
```

**Important Docker Notes:**
- App runs at `https://bkk-esemkasari.dev` (not localhost:8000)
- Nginx serves Laravel on port 443 (HTTPS) with auto HTTP→HTTPS redirect
- Vite auto-detects SSL certificates and runs with HTTPS if available
- MySQL accessible at `localhost:3902` (mapped from container's 3306)
- Frontend runs on HOST, not in container (npm run dev outside Docker)
- Fix permissions if needed: `docker exec -it bkk-esemkasari-app chown -R www-data:www-data /var/www/app/storage`

### Database & Migrations
```bash
# Inside container
docker exec -it bkk-esemkasari-app php artisan migrate
docker exec -it bkk-esemkasari-app php artisan db:seed

# Or via bash
docker exec -it bkk-esemkasari-app bash
php artisan migrate:fresh --seed
```

### Frontend
```bash
npm run dev           # Vite HMR (auto-detects HTTPS if certs exist)
npm run build         # Production build
npm run build:ssr     # SSR build
npm run lint          # ESLint check + fix
npm run format        # Prettier format
```

### Testing
```bash
composer run test     # Pest PHP tests
```

## Critical Patterns & Conventions

### Vue Components
- **Always use `<script setup>` syntax** - no Options API
- **Page components**: `resources/js/pages/{Entity}/` with proper `AppLayout` or `layout: null`
- **Auth guard detection**: `usePage().props.auth.activeGuard` returns 'web'|'student'|'partner'
- **Breadcrumbs**: Required `BreadcrumbItem[]` prop for all pages
- **Import PrimeVue components individually**: `import Button from 'primevue/button'` (never bulk import)
- **Date formatting**: dayjs with `'dayjs/locale/id'` and `relativeTime` plugin
- **Route navigation**: `router.get(route('name', params), options)` for Inertia

### Styling Philosophy
- **Tailwind CSS v4**: Uses `@import 'tailwindcss';` with inline theme in `resources/css/app.css`
- **Utility-first approach**: Always prioritize Tailwind classes over custom CSS
- **Mobile-responsive**: All layouts must work on mobile (`sm:`, `md:`, `lg:` breakpoints)
- **Professional UI patterns**: 
  - Card layouts with rounded-xl, shadow-sm, border
  - Icon-enhanced columns with color-coded badges
  - Hover states with transition-colors duration-200
  - Empty states with descriptive icons and text
- **PrimeVue Theme**: Lara preset with `darkModeSelector: false` in `app.ts`

### File Upload System
- **Use `HasFeaturedFile` trait** on models (Announcement, Vacancy, Student)
- **Methods**: 
  - `updateFile(UploadedFile $file)` - Stores file, auto-deletes previous
  - `updateCvFile(UploadedFile $file)` - For CV files
  - `deleteFile()` - Removes file from storage
  - `deleteCvFile()` - Removes CV file
- **File organization**: Auto-organized in `storage/app/public/{table_name}/files/`
- **Access via attributes**: `$model->file_url` (appended automatically)
- **Display filename**: Extract with `file_url.split('/').pop()` in Vue

## Specialized Implementation Patterns

### DataTable Lists
```vue
<DataTable
    :value="items"
    paginator
    :rows="10"
    :rowsPerPageOptions="[5, 10, 20, 50]"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} data"
>
```
- Responsive pagination with CSS order properties for mobile
- Professional empty states with icons and descriptive text
- Icon-enhanced columns for better visual hierarchy

### VirtualScroller (Announcements/Vacancies)
- **Announcements**: Uses PrimeVue VirtualScroller with `onLazyLoad` event
- **Vacancies**: Custom Intersection Observer pattern with threshold 0.1
- **Backend**: Supports `?lazy_load=true&first={offset}` parameters
- **Skeleton components**: Show during loading states
- **Search**: Use `preserveState: true, preserveScroll: false, replace: true` in router options
- **Loader trigger**: `<div ref="loaderTrigger">` at bottom for infinite scroll

### TinyMCE Integration
- **API key**: Access via `usePage().props.tinymce.api_key`
- **Consistent toolbar**: `'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | removeformat'`
- **Content storage**: HTML stored directly in database
- **Display styling**: Use `.prose` classes with custom styles for v-html content
- **Image handling**: Uploaded to storage, served via public disk

## Multi-Guard Authentication Flow
- **Route Guards**: `middleware('auth:web,student,partner')` for shared routes
- **Controller Pattern**: Check `auth()->guard('student')->check()` for guard-specific logic
- **Frontend Detection**: `usePage().props.auth.activeGuard` determines UI behavior
- **Login Flow**: Single login form at `/login` with `login_as` parameter determines guard
- **Session**: Each guard has separate session driver and timeout

## Docker SSL/HTTPS Configuration
- **Self-signed certificates**: Generated via `./docker/generate-ssl.sh` (valid 365 days)
- **Certificate location**: `docker/ssl/bkk-esemkasari.{crt,key}`
- **Nginx config**: `docker/nginx-ssl.conf` with SSL settings and security headers
- **Vite HTTPS**: Auto-detects SSL certs via `fs.existsSync()` check in `vite.config.ts`
- **HMR**: WebSocket Secure (WSS) protocol for hot module replacement
- **Security headers**: HSTS, X-Frame-Options, X-Content-Type-Options, X-XSS-Protection
- **Trust certificate**: Run `./docker/trust-cert.sh` to avoid browser warnings (optional)
- **Production**: Use Let's Encrypt for CA-signed certificates (see `PRODUCTION-SSL.md`)

## Import/Export Workflows
- **Student Import**: Excel via `maatwebsite/excel` supporting CSV/XLS/XLSX formats
- **Vacancy Exports**: Partners can export qualified applicants as Excel
- **Template System**: Consistent import templates with validation in Action classes

## Laravel Ecosystem Integration
- **Laravel 12 Features**: New routing configuration in `bootstrap/app.php`
- **Middleware Stack**: Custom `HandleInertiaRequests` and `HandleAppearance` middleware
- **Queue Driver**: Database-based with `jobs` and `job_batches` tables
- **Storage**: Public disk with `HasFeaturedFile` trait pattern
- **TinyMCE**: API key configured in `config/tinymce.php`
- **Ziggy**: Route helper for frontend (`route('name', params)`)

## Language & Communication
- **User Content**: All Bahasa Indonesia (UI, validation messages, notifications)
- **Code/Comments**: English for maintainability
- **Validation Messages**: Bahasa Indonesia in `app/Actions/` and custom Request classes

## When Implementing Features
1. **Follow the Action → Controller → Inertia → Vue flow**
2. **Create TypeScript types** in `resources/js/types/` for all backend data structures
3. **Use PrimeVue components consistently**: Button, Card, DataTable, Dialog, Toast, VirtualScroller
4. **Implement responsive design** with mobile-first approach using Tailwind breakpoints
5. **Add proper loading states** (skeletons, spinners) and error handling with Toast notifications
6. **Follow breadcrumb pattern** for navigation consistency across all pages
7. **Use utility-first styling** with Tailwind CSS before writing custom CSS
8. **File uploads**: Use `HasFeaturedFile` trait methods, never manual storage handling
9. **Extract filename display**: Use `file_url.split('/').pop()` for user-friendly file names
10. **Auth guard awareness**: Always check `activeGuard` for conditional rendering

## Key Files & Directories
- `app/Actions/` - Business logic layer (Action pattern)
- `app/Observers/` - Model observers extending `BaseObserver`
- `app/Traits/HasFeaturedFile.php` - File upload trait with auto-cleanup
- `resources/js/pages/` - Inertia page components
- `resources/js/types/` - TypeScript type definitions
- `docker/nginx-ssl.conf` - Nginx HTTPS configuration
- `vite.config.ts` - Auto-detects SSL for HTTPS development
- `routes/web.php` - Multi-guard route definitions
- `routes/auth.php` - Authentication routes for all guards


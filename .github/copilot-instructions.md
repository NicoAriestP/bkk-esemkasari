# BKK Esemkasari – AI Coding Agent Guide

## Project Overview
BKK Esemkasari is a job fair management system for SMKN Purwosari Bojonegoro. Stack: **Laravel 12** · **Inertia.js** · **Vue 3** (`<script setup>`, TypeScript) · **PrimeVue v4** · **Tailwind CSS v4**.

Features: tracer study questionnaires, announcements with lazy loading, job vacancy screening/application workflow, Excel import/export, multi-guard auth (admin/student/partner).

---

## Architecture

### Multi-Guard Auth
- Guards: `auth:web` (admin), `auth:student`, `auth:partner` — each with its own session driver
- Shared routes: `middleware('auth:web,student,partner')`; guard-specific logic: `auth()->guard('student')->check()`
- Frontend guard detection: `usePage().props.auth.activeGuard` → `'web' | 'student' | 'partner'`
- Single login form at `/login` using `login_as` field to select guard; all auth routes in `routes/auth.php`

### Action-Controller Pattern
Every entity has `app/Actions/{Entity}/{Entity}Action.php`. Controllers inject the Action and delegate all business logic — they only handle HTTP responses and redirects. FormRequest classes live in `app/Http/Requests/{Entity}/`.

```php
// Controller: delegate, never implement business logic
public function store(Year $year, StudentClass $studentClass, CreateStudentFormRequest $request, StudentAction $action)
{
    $model = $action->save($request);
    return redirect()->route('years.student-classes.students.index', [...]);
}
```

### Observer Pattern
All models have observers in `app/Observers/` extending `BaseObserver`. `BaseObserver::__construct()` calls `Auth::loginUsingId(1)` when running in console (needed for seeders/migrations). Observers set `created_by`/`updated_by` via `Blameable` trait. Register new observers in `app/Providers/AppServiceProvider.php`.

### Data Hierarchy & Nested Routes
- `Year → StudentClass → Students` — deeply nested: `years/{year}/student-classes/{studentClass}/students`
- `Partners → Vacancies → VacancyApplications`
- `Students → TracerStudy Answers` (one record per type: activity/working/university/entrepreneur)

### Inertia Page Components
- Pages in `resources/js/pages/{entity}/` (lowercase), rendered via `Inertia::render('entity/List', [...])`
- Authenticated pages use `AppLayout`; public pages set `layout: null`
- All pages receive `BreadcrumbItem[]` as a required prop
- Shared page props typed as `SharedData` in `resources/js/types/index.d.ts` (includes `auth`, `tinymce`, `ziggy`, `sidebarOpen`)

---

## Developer Workflows

```bash
# Local (non-Docker)
composer run dev          # Laravel + queue + Vite HMR (color-coded output)
composer run dev:ssr      # + Inertia SSR + Pail logs
composer run test         # Pest tests

# Docker + HTTPS (first-time setup)
./docker/generate-ssl.sh
echo "127.0.0.1 bkk-esemkasari.dev" | sudo tee -a /etc/hosts
docker compose up -d --build
docker exec -it bkk-esemkasari-app php artisan migrate
docker exec -it bkk-esemkasari-app php artisan storage:link

# Docker daily workflow
docker compose up -d
npm run dev               # Vite on HOST (auto-detects SSL for HTTPS)
# App: https://bkk-esemkasari.dev | MySQL: localhost:3902

# Frontend tooling
npm run lint              # ESLint auto-fix
npm run format            # Prettier format
npm run build:ssr         # SSR production build
```

> Vite runs on HOST (not in container). It auto-detects `docker/ssl/bkk-esemkasari.{crt,key}` via `fs.existsSync()` in `vite.config.ts` and switches to HTTPS/WSS automatically.

---

## Key Conventions

### Vue / TypeScript
- Always `<script setup lang="ts">` — never Options API
- Import PrimeVue individually: `import Button from 'primevue/button'` (no bulk imports)
- Date formatting: dayjs with `'dayjs/locale/id'` and `relativeTime` plugin
- Inertia navigation: `router.get(route('name', params), options)`
- Define TypeScript types for all backend data shapes in `resources/js/types/`

### Styling
- Tailwind CSS v4: configured via `@import 'tailwindcss'` + inline theme in `resources/css/app.css`
- PrimeVue theme: Lara preset, `darkModeSelector: false` (set in `resources/js/app.ts`)
- Card pattern: `rounded-xl shadow-sm border`; badges are color-coded; always include empty states with icons

### File Uploads (`HasFeaturedFile` trait — never handle storage manually)
- `$model->updateFile($file)` / `$model->deleteFile()` → stored at `{table}/files/`
- `$model->updateCvFile($file)` / `$model->deleteCvFile()` → stored at `{table}/cv_files/`
- `$model->file_url` appended attribute; display filename in Vue: `file_url.split('/').pop()`

### DataTable (admin lists)
```vue
<DataTable :value="items" paginator :rows="10" :rowsPerPageOptions="[5,10,20,50]"
    paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
    currentPageReportTemplate="Menampilkan {first} sampai {last} dari {totalRecords} data">
```

### Lazy Loading (infinite scroll)
- Announcements: PrimeVue `VirtualScroller` with `onLazyLoad` event
- Vacancies: custom `IntersectionObserver` on `<div ref="loaderTrigger">` (threshold 0.1)
- Backend accepts `?lazy_load=true&first={offset}`; search requests use `preserveState: true, preserveScroll: false, replace: true`

### TinyMCE
- API key: `usePage().props.tinymce.api_key`
- Toolbar: `'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image | removeformat'`
- Render stored HTML via `v-html` inside `.prose` wrapper

### Excel Import/Export
- Student import: `maatwebsite/excel`, `app/Imports/StudentsImport.php`, supports CSV/XLS/XLSX
- Vacancy applicant export: `app/Exports/Vacancy/`, partners export qualified applicants

---

## Language
- **UI, validation messages, toast notifications**: Bahasa Indonesia
- **Code, comments, type names**: English

---

## Key Directories

| Path | Purpose |
|------|---------|
| `app/Actions/` | Business logic (one Action class per entity) |
| `app/Observers/` | Model observers (all extend `BaseObserver`) |
| `app/Http/Requests/` | FormRequest validation per entity |
| `app/Traits/HasFeaturedFile.php` | File upload/cleanup trait |
| `resources/js/pages/` | Inertia page components (lowercase dirs) |
| `resources/js/components/` | Shared UI components (`app/`, `ui/`) |
| `resources/js/composables/` | `useAppearance`, `useInitials` |
| `resources/js/types/` | TypeScript definitions (`SharedData`, `Auth`, `BreadcrumbItem`) |
| `routes/web.php` | Multi-guard route groups (deeply nested) |
| `routes/auth.php` | Auth routes for all guards |
| `vite.config.ts` | Auto-detects SSL certs for HTTPS dev |
| `docker/nginx-ssl.conf` | Nginx HTTPS + security headers config |

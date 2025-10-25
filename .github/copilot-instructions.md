
# BKK Esemkasari – AI Coding Agent Guide

## Project Overview
BKK Esemkasari is a job fair management system for SMKN Purwosari Bojonegoro, built with Laravel 12, Inertia.js, Vue 3, TypeScript, and PrimeVue v4 with Tailwind CSS v4.

**Core Features:**
- Tracer study questionnaires (multi-answer types)
- Announcements with VirtualScroller lazy loading
- Job vacancy screening and application workflow
- Multi-guard authentication (admin/student/partner)

## Architecture & Data Flow
- **Multi-Guard Auth:**
  - Admin (`auth:web`): Full CRUD operations
  - Student (`auth:student`): View announcements, complete tracer study, apply to vacancies
  - Partner (`auth:partner`): Manage vacancies, screen applications
  - Route separation: `routes/web.php` uses middleware groups, `routes/auth.php` handles all guards
- **Action-Controller Pattern:**
  - Business logic in `app/Actions/{Entity}/{Entity}Action.php` (e.g., `StudentAction::save()`)
  - Controllers delegate to Actions, never handle business logic directly
  - Actions return models, Controllers handle HTTP responses and redirects
- **Observer Pattern:**
  - All models use observers in `app/Observers/` extending `BaseObserver`
  - `BaseObserver` auto-authenticates user ID 1 in console for seeders/migrations
  - Observers handle created_by/updated_by via `Blameable` trait
- **Data Hierarchy:**
  - Year → StudentClass → Students (nested educational structure)
  - Partners → Vacancies → VacancyApplications (job workflow)
  - Students → TracerStudy Answers (one-to-one per answer type: activity/working/university/entrepreneur)

## Essential Developer Workflows
- **Local Dev:** `composer run dev` (concurrently runs Laravel server + queue + Vite HMR with color coding)
- **SSR Dev:** `composer run dev:ssr` (includes Inertia SSR server + Laravel Pail for logs)
- **Docker Dev:** `docker-compose up -d` (app/nginx/mysql containers, frontend runs on host)
- **Database:** MySQL 8.0, use `bkk_esemkasari` database, migrations handle schema
- **Frontend Build:** `npm run dev` (Vite HMR), `npm run build` (production), `npm run build:ssr` (SSR)
- **Testing:** Pest PHP (`composer run test`), organized in `tests/Feature/` and `tests/Unit/`
- **Code Quality:** ESLint + Prettier (`npm run lint`, `npm run format`)

## Critical Patterns & Conventions
- **Vue Components:**
  - Always use `<script setup>` syntax in all Vue files
  - Page components: `resources/js/pages/{Entity}/` with proper `AppLayout` or `layout: null`
  - Auth guard detection: `usePage().props.auth.activeGuard`
  - Breadcrumbs: Required `BreadcrumbItem[]` for all pages
- **Styling Philosophy:**
  - **Tailwind CSS v4**: Uses `@import 'tailwindcss';` with inline theme configuration
  - **Utility-first approach**: Always prioritize Tailwind CSS classes over custom CSS
  - **Mobile-responsive**: All layouts must work on mobile (`sm:`, `md:`, `lg:` breakpoints)
  - **Professional UI patterns**: Card layouts, icon-enhanced columns, hover states
- **File Upload System:**
  - Use `HasFeaturedFile` trait on models (`Announcement`, `Vacancy`, `Student`)
  - Methods: `updateFile()`, `updateCvFile()`, `deleteFile()`, `deleteCvFile()`
  - Auto-organized by model type in `storage/app/public/`
  - Access via `{model}FileUrl` attributes

## Specialized Implementation Patterns
- **DataTable Lists:**
  - Template: `paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"`
  - Responsive pagination with CSS order properties for mobile
  - Professional empty states with icons and descriptive text
- **VirtualScroller (Announcements/Vacancies):**
  - Lazy loading via Intersection Observer with `loaderTrigger` ref
  - Backend supports `?lazy_load=true&first={offset}` parameters
  - Skeleton components for loading states during async operations
  - Search with `preserveState: true, preserveScroll: false, replace: true`
  - **Announcements**: Uses PrimeVue VirtualScroller with `onLazyLoad` event
  - **Vacancies**: Uses custom Intersection Observer pattern with threshold 0.1
- **TinyMCE Integration:**
  - API key: `usePage().props.tinymce.api_key`
  - Consistent toolbar configuration across forms
  - Content stored as HTML in database
  - Styled with `.prose` classes for v-html content

## Vue 3 + TypeScript Specifics
- **Component Structure:** Import only used PrimeVue components individually
- **Date Formatting:** dayjs with `'dayjs/locale/id'` and `relativeTime` plugin
- **Route Navigation:** Use `router.get(route('name', params), options)` for Inertia
- **State Management:** Vue 3 Composition API with `ref()`, `computed()`, `watch()`
- **TypeScript Types:** All data types defined in `resources/js/types/`
- **Vite Configuration:** Uses Tailwind CSS v4 plugin, path aliases for `@/` → `resources/js/`

## Multi-Guard Authentication Flow
- **Route Guards:** `middleware('auth:web,student,partner')` for shared routes
- **Controller Pattern:** Check `auth()->guard('student')->check()` for guard-specific logic
- **Frontend Detection:** `usePage().props.auth.activeGuard` determines UI behavior
- **Login Flow:** Single login form with `login_as` parameter determines guard

## Docker Development Setup
- **Quick Start:** `cp .env.docker .env && docker-compose up -d --build`
- **Database:** MySQL 8.0 at localhost:3306 (username: root, password: root)
- **Access Points:** App at localhost:8000, Vite at localhost:5173
- **Frontend:** Run `npm install && npm run dev` on host (not containerized)
- **File structure:** `/var/www/app` in containers, volume-mounted for development
- **Container commands:** `docker-compose exec app php artisan {command}`

## Import/Export Workflows
- **Student Import:** Excel via `maatwebsite/excel` supporting CSV/XLS/XLSX formats
- **Vacancy Exports:** Partners can export qualified applicants as Excel
- **Template System:** Consistent import templates with validation

## Laravel Ecosystem Integration
- **Laravel 12 Features:** Uses new routing configuration in `bootstrap/app.php`
- **Middleware Stack:** Custom `HandleInertiaRequests` and `HandleAppearance` middleware
- **Queue Driver:** Database-based with `jobs` and `job_batches` tables
- **Storage:** Public disk with `HasFeaturedFile` trait pattern
- **TinyMCE Integration:** API key configured in `config/tinymce.php`

## Language & Communication
- **User Content:** All Bahasa Indonesia
- **Code/Comments:** English for maintainability
- **Validation Messages:** Bahasa Indonesia for user experience

## Critical Development Patterns
- **Queue Configuration:** Uses database driver, queue listener runs in dev mode via `composer run dev`
- **File Upload Integration:** 
  - Models extend `HasFeaturedFile` trait with `updateFile()` and `deleteFile()` methods
  - Auto-organized in `storage/app/public/` by model type or custom `$type` property
  - Access files via `{model}FileUrl` attributes (automatically appended)
- **PrimeVue Theme Setup:**
  - Uses Lara preset with `darkModeSelector: false` in `app.ts`
  - Components imported individually, never bulk imports
  - Toast service and Tooltip directive globally registered

## When Implementing Features
1. **Follow the Action → Controller → Inertia → Vue flow**
2. **Create TypeScript types** in `resources/js/types/` for all backend data
3. **Use PrimeVue components consistently**: Button, Card, DataTable, Dialog, Toast
4. **Implement responsive design** with mobile-first approach
5. **Add proper loading states** and error handling with Toast notifications
6. **Follow breadcrumb pattern** for navigation consistency
7. **Use utility-first styling** with Tailwind CSS before writing custom CSS

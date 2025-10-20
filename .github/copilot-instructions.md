
# BKK Esemkasari – AI Coding Agent Guide

## Project Overview
BKK Esemkasari is a job fair management system for SMKN Purwosari Bojonegoro, built with Laravel 12, Inertia.js, Vue 3, TypeScript, and PrimeVue v4 with Tailwind CSS.

**Core Features:**
- Tracer study questionnaires (multi-answer types)
- Announcements
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
- **Data Hierarchy:**
  - Year → StudentClass → Students (nested educational structure)
  - Partners → Vacancies → VacancyApplications (job workflow)
  - Students → TracerStudy Answers (one-to-one per answer type: activity/working/university/entrepreneur)

## Developer Workflows
- **Local Dev:** `composer run dev` (concurrently runs Laravel server + queue + Vite HMR)
- **Docker Dev:** `docker-compose up -d` (app/nginx/mysql containers, frontend runs on host)
- **Database:** MySQL 8.0, use `bkk_esemkasari` database, migrations handle schema
- **Testing:** Pest PHP (`composer run test`), organized in `tests/Feature/` and `tests/Unit/`
- **Frontend:** 
  - `npm run dev` (Vite HMR at localhost:5173)
  - `npm run build` (production), `npm run build:ssr` (SSR support)
  - ESLint + Prettier (`npm run lint`, `npm run format`)

## Conventions & Patterns
- **Vue:**
  - Use `<script setup>` in all components
  - Split features into reusable components (`resources/js/components/`)
  - Page components in `resources/js/pages/{Entity}/`
  - Layout via `AppLayout.vue` (or `layout: null` for standalone pages like HomePage)
  - Auth guard detection via `usePage().props.auth.activeGuard`
  - Always prioritize using Tailwind CSS utility classes for styling before custom CSS.
- **TypeScript:**
  - Types auto-generated in `resources/js/types/` for backend data
  - Shared types: `Auth`, `BreadcrumbItem`, `NavItem`, `SharedData`
- **Backend:**
  - Use model traits (e.g., `HasFeaturedFile` for uploads)
  - Observers (e.g., `AnnouncementObserver`) for created_by/updated_by
  - Validation via `app/Http/Requests/{Entity}/`
  - Soft deletes on most models
- **File Uploads:**
  - Trait-based via `HasFeaturedFile`, auto-organized by model type
  - Methods: `updateFile()`, `updateCvFile()`, `deleteFile()`, `deleteCvFile()`
  - Public disk: `storage/app/public/`, accessible via `{model}FileUrl` attributes
- **Import/Export:**
  - Bulk student import: Excel via `maatwebsite/excel` (CSV/XLS/XLSX)
  - Export qualified applicants for partners
  - Template files for consistent imports

## Docker & Environment
- **Docker Setup:** `docker-compose up -d` (app: PHP 8.2-FPM, nginx, mysql: 8.0)
- **Environment:** Copy `.env.docker` to `.env` for Docker development
- **Database Access:** localhost:3306 (Docker) or via DBeaver with MySQL 8.0 driver
- **File Structure:** `/var/www/app` in containers, volume-mounted for live development
- **Frontend:** Run `npm install` and `npm run dev` on host (not in container)

## Integrations
- **PrimeVue:** Lara theme, ToastService, global tooltip, utility-first styling approach
- **Inertia.js:** Page resolution in `resources/js/pages/`, Ziggy for routes, SSR support
- **Database:** MySQL 8.0 (`bkk_esemkasari`), queue/cache via database tables
- **TinyMCE:** Rich text editor, API key from backend via `usePage().props.tinymce.api_key`

## Specialized Features
- **Tracer Study:**
  - Multiple answer types: `StudentActivityAnswer`, `StudentWorkingAnswer`, `StudentUniversityAnswer`, `StudentEntrepreneurAnswer`
  - Questions: `questionnaires` → `questionnaire_questions` → `question_options`
  - Multi-step wizard UI in `TracerStudy.vue`
- **Vacancy Workflow:**
  - Partners create vacancies (with attachments)
  - Students apply via `VacancyStudentController::applyVacancy()`
  - Partners screen via `VacancyApplicationStatus` enum
  - Export data functionality
- **HomePage:**
  - Standalone page with `layout: null` in `defineOptions`
  - Sections: hero, stats, about, programs, facilities, news, contact
  - Smooth scrolling navigation with anchor links

## Language & Communication
- All user-facing content: Bahasa Indonesia
- Code comments/docs: English
- Error/validation: Bahasa Indonesia

**When implementing features:**
- Always follow Action → Controller → Inertia → Vue component flow
- Ensure TypeScript types for all backend→frontend data
- Use PrimeVue components consistently (Button, Card, DataTable, Dialog, etc.)
- Follow breadcrumb pattern: `BreadcrumbItem[]` for navigation

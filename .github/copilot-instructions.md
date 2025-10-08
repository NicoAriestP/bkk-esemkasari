
# BKK Esemkasari – AI Coding Agent Guide

## Project Overview
BKK Esemkasari is a job fair management system for SMKN Purwosari Bojonegoro, built with Laravel 12, Inertia.js, Vue 3, TypeScript, and PrimeVue v4.

**Core Features:**
- Tracer study questionnaires (multi-answer types)
- Announcements
- Job vacancy screening and application workflow
- Multi-guard authentication (admin/student/partner)

## Architecture & Data Flow
- **Multi-Guard Auth:**
  - Admin (`auth:web`): Full CRUD
  - Student (`auth:student`): Announcements, tracer study, apply vacancies
  - Partner (`auth:partner`): Manage vacancies, screen applications
  - Route separation via middleware groups in `routes/web.php`
- **Data Hierarchy:**
  - Year → StudentClass → Students
  - Partners → Vacancies → VacancyApplications
  - Students → TracerStudy Answers (one-to-one per answer type)
- **Business Logic:**
  - All create/update handled in `app/Actions/{Entity}/{Entity}Action.php`
  - Controllers delegate to Actions (e.g., `StudentAction::save()`)

## Developer Workflows
- **Start Dev:** `composer run dev` (Laravel server + queue + Vite)
- **Testing:** Pest PHP (`composer run test`), tests in `tests/Feature/` and `tests/Unit/`
- **Frontend Build:**
  - `npm run dev` (HMR)
  - `npm run build` (production)
  - `npm run build:ssr` (SSR)
- **CI:** See `.github/workflows/tests.yml` for build/test steps

## Conventions & Patterns
- **Vue:**
  - Use `<script setup>` in all components
  - Split features into reusable components (`resources/js/components/`)
  - Page components in `resources/js/pages/{Entity}/`
  - Layout via `AppLayout.vue` (or `layout: null` for standalone pages like HomePage)
  - Auth guard detection via `usePage().props.auth.activeGuard`
- **TypeScript:**
  - Types auto-generated in `resources/js/types/` for backend data
  - Shared types: `Auth`, `BreadcrumbItem`, `NavItem`, `SharedData`
- **Backend:**
  - Use model traits (e.g., `HasFeaturedFile` for uploads)
  - Observers (e.g., `AnnouncementObserver`) for created_by/updated_by
  - Validation via `app/Http/Requests/{Entity}/`
  - Soft deletes on most models
- **File Uploads:**
  - Trait-based, auto-organized by model type
  - Public disk: `storage/app/public/`
- **Import/Export:**
  - Bulk student import: Excel via `maatwebsite/excel` (`StudentsImport` class)
  - Application export for partners

## Integrations
- **PrimeVue:** Lara theme, ToastService, global tooltip
- **Inertia.js:** Page resolution in `resources/js/pages/`, Ziggy for routes, SSR in `resources/js/ssr.ts`
- **Database:** MySQL (`bkk_esemkasari`), root/no password, localhost:3306 (Laragon)
- **TinyMCE:** Rich text editor (API key from backend via `usePage().props.tinymce.api_key`)

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

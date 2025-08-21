# BKK Esemkasari - AI Development Guide

## Project Overview
BKK Esemkasari is a specialized job fair management system for SMKN Purwosari Bojonegoro built with Laravel 12 + Inertia.js + Vue 3 + TypeScript + PrimeVue v4.

**Core Features**: Tracer study questionnaires, announcements, job vacancy screening, multi-guard authentication (admin/student/partner).

## Architecture Patterns

### Multi-Guard Authentication System
- **Admin Guard** (`auth:web`): Full CRUD operations for all entities
- **Student Guard** (`auth:student`): Access to announcements, tracer study, vacancy applications
- **Partner Guard** (`auth:partner`): Vacancy management and application screening
- Routes are segregated by guard with middleware groups in `routes/web.php`

### Data Flow Architecture
```
Year → StudentClass → Students (hierarchical data structure)
Partners → Vacancies → VacancyApplications (partner workflow)
Students → TracerStudy Answers (questionnaire system)
```

### Action-Based Business Logic
- Business logic resides in `app/Actions/{Entity}/{Entity}Action.php` classes
- Controllers delegate to Actions for create/update operations
- Example: `StudentAction::save()` and `StudentAction::update()` handle form validation and model persistence

## Development Workflows

### Start Development Environment
```bash
composer run dev  # Starts Laravel server + queue + Vite concurrently
```

### Testing
- Uses **Pest PHP** testing framework
- Run tests: `composer run test`
- Test files in `tests/Feature/` and `tests/Unit/`

### Frontend Build
```bash
npm run dev        # Development with HMR
npm run build      # Production build
npm run build:ssr  # SSR build
```

## Critical Conventions

### Vue Component Architecture
- **Always use `<script setup>` syntax** for all Vue components
- **Component separation**: Break down features into reusable components in `resources/js/components/`
- **Layout system**: `AppLayout.vue` wraps content with sidebar navigation
- **TypeScript**: Auto-generate types in `resources/js/types/` for backend data structures

### Backend Patterns
- **Model Traits**: `HasFeaturedFile` trait for file upload handling with automatic URL generation
- **Observers**: Use model observers (e.g., `AnnouncementObserver`) for automated created_by/updated_by tracking
- **Form Requests**: Validation through dedicated request classes in `app/Http/Requests/{Entity}/`
- **Soft Deletes**: Most models use soft deletes for data retention

### File Organization
- **Frontend**: `resources/js/pages/{Entity}/` for page components
- **Backend**: `app/Actions/{Entity}/`, `app/Http/Controllers/{Entity}Controller.php`
- **Types**: Auto-generated TypeScript interfaces in `resources/js/types/`

## Key Integrations

### PrimeVue UI Framework
- Theme: Lara preset configured in `app.ts`
- Toast notifications via PrimeVue ToastService
- Tooltip directive globally registered

### File Upload System
- Uses `HasFeaturedFile` trait with automatic folder organization by model type
- CV files for students, vacancy attachments for partners
- Files stored in `storage/app/public/` with public disk configuration

### Inertia.js Configuration
- Page resolution in `resources/js/pages/` directory
- Ziggy for named route generation in frontend
- SSR support configured with `resources/js/ssr.ts`

### Development Database
- **Database**: MySQL (`bkk_esemkasari`)
- **Credentials**: root user, no password, localhost:3306
- **Location**: Laragon environment at `~/laragon/www/bkk-esemkasari`

## Specialized Patterns

### Tracer Study System
Complex questionnaire system with multiple answer types:
- `StudentActivityAnswer`, `StudentWorkingAnswer`, `StudentUniversityAnswer`, `StudentEntrepreneurAnswer`
- Each student has one-to-one relationships with different answer types
- Questions stored in `questionnaires` → `questionnaire_questions` → `question_options`

### Vacancy Application Workflow
1. Partners create vacancies with file attachments
2. Students browse and apply to vacancies
3. Partners screen applications and update status via `VacancyApplicationStatus` enum
4. Export functionality for application data

### Import/Export Features
- Student bulk import via Excel files using `maatwebsite/excel` package
- Application data export for partners
- File handling through dedicated Import/Export classes

## Language & Communication
- **All user-facing content in Bahasa Indonesia**
- **Code comments and documentation in English**
- **Error messages and validation in Bahasa Indonesia**

When implementing new features, follow the established Action → Controller → Inertia → Vue component pattern, and ensure TypeScript types are properly defined for all data structures passed between backend and frontend.

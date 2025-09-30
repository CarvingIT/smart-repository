# DAMS (Smart Repository) - AI Agent Instructions

## Architecture Overview

DAMS is a Laravel 9 institutional document repository system with role-based permissions, full-text search, and multi-storage support.

**Core Domain Models:**
- `Document` - Central entity with soft deletes, auditing, and full-text search via `FullTextSearch` trait
- `Collection` - Hierarchical containers with parent-child relationships and maintainer roles
- `User` - Authentication with role-based permissions via `UserPermission` and `UserRole` pivots
- `Sysconfig` - System configuration stored as key-value pairs (required for app functionality)

## Key Architectural Patterns

### Permission System
- Three-tier: `Permission` → `Role` → `User` with collection-level granular access
- Check permissions using `UserPermission` model with `collection_id` scope
- Admin routes use `adminhome` middleware group

### Document Management
- Documents use Laravel's `SoftDeletes` and dispatch `DocumentSaved`/`DocumentDeleted` events
- File storage abstracted through `Disk` model - supports local, S3, SFTP via `config/filesystems.php`
- OCR processing available via `thiagoalessio/tesseract_ocr` package
- Text content extraction for search indexing stored in `text_content` field

### Search Architecture
- Dual-mode: Database (`SEARCH_MODE=database`) or Elasticsearch (`SEARCH_MODE=elastic`)
- Full-text search implemented via `FullTextSearch` trait on Document model
- ElasticSearch integration through `elasticsearch/elasticsearch` package

### Frontend Structure
- Blade templates extend `layouts.app` with consistent parameters: `['class' => 'off-canvas-sidebar', 'activePage' => '...', 'titlePage' => '...']`
- Vue.js components in `resources/js/components/` compiled via Laravel Mix with `.vue()` support
- Material Design theme with icons in `public/material-design-icons/`

## Critical Development Workflows

### Initial Setup Requirements
```bash
# Essential system config (app will 503 without these)
php artisan db:seed --class=SysconfigSeeder  # or create manually via Sysconfig model
php artisan up  # Disable maintenance mode if enabled

# Frontend compilation with Vue support
npm run dev  # Requires vue-loader configuration in webpack.mix.js
```

### Database Seeding Pattern
- `UserSeeder` creates default users with `bcrypt('SmartPass!@#')` passwords
- First user (ID=1) automatically gets admin role via `user_roles` table
- Collections, permissions, and roles must be seeded before functional testing

### File Upload Workflow
- Files processed through `MediaController` and `DocumentController`
- Metadata extraction via `MetaField` and `MetaFieldValue` relationships
- Document revisions tracked through `DocumentRevision` model

## Project-Specific Conventions

### Model Relationships
- Collections support infinite nesting via `parent_id` self-reference
- Document-Collection relationship: `$collection->documents()` and `$document->collection()`
- Maintainer assignment: `Collection::maintainer()` finds user with MAINTAINER permission

### Configuration Management
- System settings stored in `sysconfig` table, not `.env` (e.g., `site_title`, `max_upload_size`)
- Feature flags: `ENABLE_REGISTRATION`, `ENABLE_COLLECTION_LIST` etc. in `.env`
- Search mode toggle: `SEARCH_MODE` in `.env` switches between database and Elasticsearch

### Security Patterns
- Email verification required: routes use `['auth' => 'verified']` middleware
- API authentication via `ApiAuthController` with token-based system
- File access controlled through `MediaController` with permission checks

## Integration Points

### External Services
- **Elasticsearch**: Full-text search with synonym support via `Synonyms` model
- **Cloud Storage**: S3/SFTP via `League\Flysystem` packages configured in `Disk` model
- **OCR**: Tesseract integration for document text extraction
- **Chatbot**: BotMan framework integration via `BotManController`

### Key Commands
```bash
# Search index management
php artisan elastic:create-index  # If using Elasticsearch mode

# File processing
php artisan queue:work  # For background document processing

# Maintenance
php artisan down/up  # Maintenance mode (critical - causes 503 if enabled)
```

### API Patterns
- RESTful API routes in `routes/api.php` with token authentication
- Document uploads via multipart forms to `DocumentController@store`
- Collection hierarchy accessible via `CollectionController` with nested JSON

## Development Notes

- **Critical**: Always check maintenance mode (`php artisan about`) if encountering 503 errors
- Route caching may fail due to `laravel-filemanager` package conflicts - skip `php artisan route:cache`
- Frontend builds require Vue.js loader configuration in `webpack.mix.js`
- Default admin credentials in `UserSeeder`: admin users use `SmartPass!@#` password pattern
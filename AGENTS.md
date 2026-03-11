# Project Guidelines

## Build and Test

- Install backend deps: `composer install`
- Install frontend deps: `npm install`
- Full project bootstrap: `composer run setup`
- Run local dev stack: `composer run dev`
- Run frontend dev server only: `npm run dev`
- Build frontend assets: `npm run build`
- Run tests: `php artisan test --compact`
- Run single test file: `php artisan test --compact tests/Feature/SomeTest.php`
- Format changed PHP files: `vendor/bin/pint --dirty --format agent`

## Architecture

- Framework: Laravel 12 with streamlined structure.
- Middleware and app configuration live in `bootstrap/app.php` (not `app/Http/Kernel.php`).
- Primary admin/UI framework is Filament v5 under `app/Filament`.
- Domain centers on media content, subscriptions, and payments:
  - Content models in `app/Models`
  - Subscription/payment logic in `app/Actions/Subscription`, `app/Listeners`, and related models
- Routes are split across `routes/web.php`, `routes/ai.php`, and `routes/console.php`.

## Conventions

- Follow existing patterns from sibling files before introducing new structure.
- Use explicit parameter and return types in PHP methods.
- Use constructor property promotion when applicable.
- Use Eloquent relationships and eager loading; avoid raw queries unless necessary.
- Use Form Request classes for validation instead of inline controller validation.
- Keep enum keys in TitleCase.
- Prefer concise PHPDoc blocks over inline comments.
- Do not add new top-level folders or new dependencies without approval.
- Only create documentation files when explicitly requested.

## Filament and UI

- Use Filament `make()` patterns and correct namespaces:
  - Form fields: `Filament\\Forms\\Components`
  - Infolists: `Filament\\Infolists\\Components`
  - Layout/schema: `Filament\\Schemas\\Components`
  - Actions: `Filament\\Actions`
- File uploads are private by default; set visibility explicitly when public access is needed.
- For Tailwind or styling work, activate and follow the `tailwindcss-development` skill.

## Agent Workflow

- Use Laravel Boost tools first for this workspace:
  - `application-info` at chat start for package/version context
  - `search-docs` before Laravel/Filament/Tailwind implementation decisions
  - `list-artisan-commands` before running unfamiliar Artisan commands
  - `get-absolute-url` when sharing URLs
  - `database-schema`/`database-query` for read-only DB inspection
  - `browser-logs` and `last-error` for debugging
- Prefer `php artisan make:* --no-interaction` for generating Laravel files.
- Use PHPUnit-style tests and run the smallest relevant test scope after changes.

## Pitfalls

- `composer run setup` includes `migrate:fresh --seed` and is destructive to existing DB data.
- Frontend changes may require `npm run dev` or `npm run build` to appear.
- Avoid maintaining both `.github/copilot-instructions.md` and `AGENTS.md`; use this file as the single source of workspace instructions.

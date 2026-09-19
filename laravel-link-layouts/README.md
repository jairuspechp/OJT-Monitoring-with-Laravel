# Link Layouts on Laravel + SQLite

These files drop into a fresh Laravel 11+ project. They replace `server.js` + MySQL
but keep the same `/api/...` URLs, so your `app.js` works unchanged.

## Setup

    composer create-project laravel/laravel link-layouts
    cd link-layouts

1. Enable API routes first (this wires up `routes/api.php`):

       php artisan install:api

2. Copy this folder's contents into the project (overwrite `routes/api.php` and `routes/web.php`):
   - `database/migrations/*`  -> `database/migrations/`
   - `app/Models/*`           -> `app/Models/`
   - `app/Http/Controllers/BoardController.php`
   - `routes/api.php`, `routes/web.php`
3. Put your `index.html` and `app.js` in `public/`.
4. Make sure `.env` uses SQLite (the Laravel default):

       DB_CONNECTION=sqlite
       # DB_DATABASE defaults to database/database.sqlite

5. Create the database file and tables:

       touch database/database.sqlite        # Windows: type nul > database\database.sqlite
       php artisan migrate

6. Run:

       php artisan serve

Open http://localhost:8000. Data is stored in `database/database.sqlite`.

## Tables
- `boards` (id, name, layout_mode, slot_count, created_at)
- `slots`  (board_id, slot_index, label, url) with a composite primary key and
  ON DELETE CASCADE to boards.

## Note
`app.js` must call relative URLs like `fetch('/api/boards')`. If it points at
`localhost:3000`, change it to relative paths (or to `localhost:8000`).

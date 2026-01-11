# Automated Kiosk for Application Queuing System
Barangay San Miguel, Pasig City

## Repo Layout
```
/queuing-system/
  /backend/                # PHP backend app
  /frontend/
    /resident-portal/      # Vue app
    /kiosk-app/            # Vue app (thin UI)
    /queue-monitor/        # Vue app (public display)
    /admin-portal/         # Vue app
  /infra/                  # Optional local Docker setup
    docker-compose.yml
    nginx/
      default.conf
  /docs/
    ARCHITECTURE.md
    API.md
    DB_SCHEMA.md
  .env.example
  README.md
```

## Quick Start (Local Dev - XAMPP)
1) Copy env files
```
cp .env.example backend/.env
cp frontend/resident-portal/.env.example frontend/resident-portal/.env
cp frontend/kiosk-app/.env.example frontend/kiosk-app/.env
cp frontend/queue-monitor/.env.example frontend/queue-monitor/.env
cp frontend/admin-portal/.env.example frontend/admin-portal/.env
```

2) Start Apache + MySQL in XAMPP.

3) Update `backend/.env` for local DB:
```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=queuing_system
DB_USERNAME=root
DB_PASSWORD=
```

4) Create the database in phpMyAdmin, then run migrations + seed data:
```
C:\\xampp\\php\\php.exe backend\\scripts\\migrate.php
C:\\xampp\\php\\php.exe backend\\scripts\\seed.php
```

5) Start the backend API:
```
C:\\xampp\\php\\php.exe -S localhost:8080 -t backend/public
```

6) Start the frontend apps (separate terminals)
```
cd frontend/resident-portal && npm install && npm run dev -- --port 5173
cd frontend/kiosk-app && npm install && npm run dev -- --port 5174
cd frontend/queue-monitor && npm install && npm run dev -- --port 5175
cd frontend/admin-portal && npm install && npm run dev -- --port 5176
```

Backend health check: `http://localhost:8080/api/health`

## Default Seeded Accounts
- Super Admin: `super.admin@brgysm.local` / `password123`
- Staff Admin: `staff.admin@brgysm.local` / `password123`

## Notes
- Backend is a lightweight PHP API scaffold (swap to Laravel 10 LTS when desired).
- The kiosk app is a thin client and relies on backend APIs for validation and ticket creation.
- Ticket creation requires an `idempotency_key` to prevent duplicates.
- See `docs/ARCHITECTURE.md`, `docs/API.md`, and `docs/DB_SCHEMA.md` for details.

## TODO
- Printing integration
- Real QR generation for residents
- Forgot password flows for residents and admins

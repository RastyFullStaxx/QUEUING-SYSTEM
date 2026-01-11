# API Endpoints

Base URL: `http://localhost:8080`

## Health
- GET `/api/health`
  - Response: `{ "status": "ok" }`

## Resident Auth
- POST `/api/auth/resident/register`
  - Body: `{ "first_name": "...", "last_name": "...", "email": "...", "password": "..." }`
- POST `/api/auth/resident/login`
  - Body: `{ "email": "...", "password": "..." }`
- GET `/api/resident/me`
  - Header: `Authorization: Bearer <token>`

## Admin Auth
- POST `/api/auth/admin/login`
  - Body: `{ "email": "...", "password": "..." }`
- GET `/api/admin/me`
  - Header: `Authorization: Bearer <token>`

## Services
- GET `/api/services`
  - Response: `{ "services": [ {"id":1,"name":"...","code":"..."} ] }`

## Kiosk
- POST `/api/kiosk/validate-qr`
  - Body: `{ "qr_code": "..." }` or `{ "resident_id": 1 }`
  - Response: `{ "approved": true, "resident": {...}, "allowed_services": [...] }`
- POST `/api/kiosk/tickets`
  - Body: `{ "resident_id": 1, "service_id": 1, "kiosk_device_id": 1, "idempotency_key": "uuid" }`
  - Response: `{ "ticket": {...} }`

## Queue Monitor
- GET `/api/queue/current?service_id=1`
  - Response: `{ "now_serving": null, "tickets": [...] }`

# API Endpoints

Base URL: `http://localhost:8080`

## Health
- GET `/api/health`
  - Response: `{ "status": "ok" }`

## Resident Auth
- POST `/api/auth/resident/register`
  - Body: `{ "first_name": "...", "last_name": "...", "email": "...", "password": "..." }`
  - Response: `resident` includes `qr_token` for QR generation.
- POST `/api/auth/resident/login`
  - Body: `{ "email": "...", "password": "..." }`
  - Response: `resident` includes `qr_token` for QR generation.
- GET `/api/resident/me`
  - Header: `Authorization: Bearer <token>`
  - Response: `resident` includes `qr_token` for QR generation.

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
  - QR formats accepted:
    - `BSM|RESIDENT|<resident_id>|<qr_token>`
    - `BSM-QR-<qr_token>`
    - `BSM-RES-<resident_id>`
    - `<resident_id>`
  - Response: `{ "approved": true, "resident": {...}, "allowed_services": [...] }`
- POST `/api/kiosk/tickets`
  - Body: `{ "resident_id": 1, "service_id": 1, "kiosk_device_id": 1, "idempotency_key": "uuid" }`
  - Response: `{ "ticket": {...} }`

## Queue Monitor
- GET `/api/queue/current?service_id=1`
  - Response: `{ "now_serving": null, "tickets": [...] }`

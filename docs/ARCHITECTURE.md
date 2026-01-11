# Architecture Overview

## Thin Kiosk Approach
- The kiosk is a thin client that calls backend APIs and does not store sensitive data locally.
- All validation, ticket creation, and queue logic live in the backend to keep kiosks fast and consistent.
- Kiosk UI avoids heavy animations and large assets to keep rendering quick.

## Idempotency
- Ticket creation requires an `idempotency_key`.
- The backend stores the key on `queue_tickets` with a unique index.
- Replaying the same request returns the existing ticket instead of creating duplicates.

## Logical Apps
- Resident Portal: registration, login, profile, and status.
- Kiosk App: QR validation, service selection, ticket creation.
- Public Queue Monitor: display and polling for current queue.
- Admin Portal: management placeholders with RBAC roles.

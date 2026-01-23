# Database Schema

## residents
- id (PK)
- first_name, last_name, email (unique)
- password_hash
- qr_token (unique)
- status (pending/approved/rejected)
- created_at, updated_at

## resident_ids
- id (PK)
- resident_id (FK -> residents)
- valid_id_url
- verification_status (pending/approved/rejected)
- created_at

## admins
- id (PK)
- name, email (unique)
- password_hash
- role (staff_admin/super_admin)
- service_ids (JSON assignment for staff_admin)
- created_at

## services
- id (PK)
- name, code (unique), active
- created_at

## kiosk_devices
- id (PK)
- device_id (unique)
- name, token, last_seen_at

## queue_tickets
- id (PK)
- ticket_no
- resident_id (FK -> residents)
- service_id (FK -> services)
- status (waiting/serving/done/cancelled)
- issued_at, issued_date
- kiosk_device_id (FK -> kiosk_devices)
- idempotency_key (unique)

### Important Indexes
- (service_id, issued_date, status)
- (resident_id, service_id, issued_date)
- unique(idempotency_key)

## audit_logs
- id (PK)
- actor_type, actor_id
- action
- meta_json
- created_at

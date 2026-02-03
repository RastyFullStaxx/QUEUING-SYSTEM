-- Dummy data for residents, kiosks, queue tickets, transactions (done/cancelled), and audit logs.

INSERT INTO services (name, code, active, created_at)
VALUES
  ('Proof of residency', 'PROOF_RESIDENCY', 1, NOW()),
  ('Barangay Indigency', 'INDIGENCY', 1, NOW()),
  ('Loan of income', 'INCOME_LOAN', 1, NOW()),
  ('Certificate of Solo parent', 'SOLO_PARENT', 1, NOW()),
  ('Special permit', 'SPECIAL_PERMIT', 1, NOW()),
  ('Building permit', 'BUILDING_PERMIT', 1, NOW())
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  active = VALUES(active);

INSERT INTO residents (first_name, last_name, email, password_hash, status, created_at, updated_at)
VALUES
  ('Rasty', 'Espartero', 'rasty@example.com', '$2y$10$dummyhash', 'approved', NOW(), NOW()),
  ('Mika', 'Santos', 'mika.santos@example.com', '$2y$10$dummyhash', 'pending', NOW(), NOW()),
  ('Luis', 'Reyes', 'luis.reyes@example.com', '$2y$10$dummyhash', 'rejected', NOW(), NOW()),
  ('Alyssa', 'Cruz', 'alyssa.cruz@example.com', '$2y$10$dummyhash', 'approved', NOW(), NOW())
ON DUPLICATE KEY UPDATE
  first_name = VALUES(first_name),
  last_name = VALUES(last_name),
  status = VALUES(status),
  updated_at = NOW();

INSERT INTO kiosk_devices (device_id, name, token, last_seen_at)
VALUES
  ('KIOSK-001', 'Barangay Lobby Kiosk', 'seed-token-001', NOW() - INTERVAL 5 MINUTE),
  ('KIOSK-002', 'Annex Kiosk', 'seed-token-002', NOW() - INTERVAL 45 MINUTE)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  token = VALUES(token),
  last_seen_at = VALUES(last_seen_at);

INSERT IGNORE INTO queue_tickets
  (ticket_no, resident_id, service_id, status, issued_at, issued_date, kiosk_device_id, idempotency_key, created_at)
VALUES
  (
    'A-1001',
    (SELECT id FROM residents WHERE email = 'rasty@example.com'),
    (SELECT id FROM services WHERE code = 'SPECIAL_PERMIT'),
    'waiting',
    NOW() - INTERVAL 20 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
    'seed-ticket-1',
    NOW()
  ),
  (
    'A-1002',
    (SELECT id FROM residents WHERE email = 'alyssa.cruz@example.com'),
    (SELECT id FROM services WHERE code = 'BUILDING_PERMIT'),
    'serving',
    NOW() - INTERVAL 12 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
    'seed-ticket-2',
    NOW()
  ),
  (
    'B-2001',
    (SELECT id FROM residents WHERE email = 'rasty@example.com'),
    (SELECT id FROM services WHERE code = 'PROOF_RESIDENCY'),
    'done',
    NOW() - INTERVAL 1 DAY,
    CURDATE() - INTERVAL 1 DAY,
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'),
    'seed-ticket-3',
    NOW() - INTERVAL 1 DAY
  ),
  (
    'B-2002',
    (SELECT id FROM residents WHERE email = 'mika.santos@example.com'),
    (SELECT id FROM services WHERE code = 'INDIGENCY'),
    'cancelled',
    NOW() - INTERVAL 2 DAY,
    CURDATE() - INTERVAL 2 DAY,
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'),
    'seed-ticket-4',
    NOW() - INTERVAL 2 DAY
  ),
  (
    'A-1003',
    (SELECT id FROM residents WHERE email = 'rasty@example.com'),
    (SELECT id FROM services WHERE code = 'BUILDING_PERMIT'),
    'waiting',
    NOW() - INTERVAL 10 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
    'seed-ticket-5',
    NOW()
  ),
  (
    'A-1004',
    (SELECT id FROM residents WHERE email = 'alyssa.cruz@example.com'),
    (SELECT id FROM services WHERE code = 'SPECIAL_PERMIT'),
    'waiting',
    NOW() - INTERVAL 8 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
    'seed-ticket-6',
    NOW()
  ),
  (
    'A-1005',
    (SELECT id FROM residents WHERE email = 'mika.santos@example.com'),
    (SELECT id FROM services WHERE code = 'PROOF_RESIDENCY'),
    'waiting',
    NOW() - INTERVAL 6 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'),
    'seed-ticket-7',
    NOW()
  ),
  (
    'A-1006',
    (SELECT id FROM residents WHERE email = 'rasty@example.com'),
    (SELECT id FROM services WHERE code = 'INDIGENCY'),
    'serving',
    NOW() - INTERVAL 4 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
    'seed-ticket-8',
    NOW()
  ),
  (
    'A-1007',
    (SELECT id FROM residents WHERE email = 'rasty@example.com'),
    (SELECT id FROM services WHERE code = 'PROOF_RESIDENCY'),
    'waiting',
    NOW() - INTERVAL 3 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
    'seed-ticket-11',
    NOW()
  ),
  (
    'A-1008',
    (SELECT id FROM residents WHERE email = 'mika.santos@example.com'),
    (SELECT id FROM services WHERE code = 'BUILDING_PERMIT'),
    'waiting',
    NOW() - INTERVAL 2 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'),
    'seed-ticket-12',
    NOW()
  ),
  (
    'A-1009',
    (SELECT id FROM residents WHERE email = 'alyssa.cruz@example.com'),
    (SELECT id FROM services WHERE code = 'SPECIAL_PERMIT'),
    'waiting',
    NOW() - INTERVAL 1 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
    'seed-ticket-13',
    NOW()
  ),
  (
    'B-2005',
    (SELECT id FROM residents WHERE email = 'rasty@example.com'),
    (SELECT id FROM services WHERE code = 'BUILDING_PERMIT'),
    'done',
    NOW() - INTERVAL 3 HOUR,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'),
    'seed-ticket-14',
    NOW() - INTERVAL 3 HOUR
  ),
  (
    'B-2006',
    (SELECT id FROM residents WHERE email = 'mika.santos@example.com'),
    (SELECT id FROM services WHERE code = 'PROOF_RESIDENCY'),
    'done',
    NOW() - INTERVAL 2 HOUR,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
    'seed-ticket-15',
    NOW() - INTERVAL 2 HOUR
  ),
  (
    'B-2007',
    (SELECT id FROM residents WHERE email = 'alyssa.cruz@example.com'),
    (SELECT id FROM services WHERE code = 'INDIGENCY'),
    'done',
    NOW() - INTERVAL 90 MINUTE,
    CURDATE(),
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'),
    'seed-ticket-16',
    NOW() - INTERVAL 90 MINUTE
  ),
  (
    'B-2003',
    (SELECT id FROM residents WHERE email = 'alyssa.cruz@example.com'),
    (SELECT id FROM services WHERE code = 'BUILDING_PERMIT'),
    'done',
    NOW() - INTERVAL 3 DAY,
    CURDATE() - INTERVAL 3 DAY,
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'),
    'seed-ticket-9',
    NOW() - INTERVAL 3 DAY
  ),
  (
    'B-2004',
    (SELECT id FROM residents WHERE email = 'rasty@example.com'),
    (SELECT id FROM services WHERE code = 'SPECIAL_PERMIT'),
    'cancelled',
    NOW() - INTERVAL 4 DAY,
    CURDATE() - INTERVAL 4 DAY,
    (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
    'seed-ticket-10',
    NOW() - INTERVAL 4 DAY
  );

INSERT INTO audit_logs (actor_type, actor_id, action, meta_json, created_at)
VALUES
  ('admin', 1, 'resident.approved', '{"resident_email": "rasty@example.com"}', NOW() - INTERVAL 2 HOUR),
  ('admin', 1, 'queue.cancelled', '{"ticket_no": "B-2002", "service": "IND"}', NOW() - INTERVAL 2 DAY),
  ('kiosk', (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'), 'kiosk.session.start',
    JSON_OBJECT(
      'session_id', 'seed-session-1',
      'resident_id', (SELECT id FROM residents WHERE email = 'rasty@example.com'),
      'kiosk_device_id', (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'),
      'approved', true
    ),
    DATE_SUB(NOW(), INTERVAL 22 MINUTE)
  ),
  ('kiosk', (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001'), 'kiosk.ticket.issued',
    JSON_OBJECT(
      'session_id', 'seed-session-1',
      'ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'A-1001'),
      'service_id', (SELECT id FROM services WHERE code = 'SPECIAL_PERMIT'),
      'resident_id', (SELECT id FROM residents WHERE email = 'rasty@example.com'),
      'kiosk_device_id', (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-001')
    ),
    DATE_SUB(NOW(), INTERVAL 20 MINUTE)
  ),
  ('kiosk', (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'), 'kiosk.session.start',
    JSON_OBJECT(
      'session_id', 'seed-session-2',
      'resident_id', (SELECT id FROM residents WHERE email = 'alyssa.cruz@example.com'),
      'kiosk_device_id', (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'),
      'approved', true
    ),
    DATE_SUB(NOW(), INTERVAL 92 MINUTE)
  ),
  ('kiosk', (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002'), 'kiosk.ticket.issued',
    JSON_OBJECT(
      'session_id', 'seed-session-2',
      'ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'B-2007'),
      'service_id', (SELECT id FROM services WHERE code = 'INDIGENCY'),
      'resident_id', (SELECT id FROM residents WHERE email = 'alyssa.cruz@example.com'),
      'kiosk_device_id', (SELECT id FROM kiosk_devices WHERE device_id = 'KIOSK-002')
    ),
    DATE_SUB(NOW(), INTERVAL 90 MINUTE)
  ),
  ('admin', 1, 'queue.serving',
    JSON_OBJECT('ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'B-2001')),
    DATE_ADD(NOW() - INTERVAL 1 DAY, INTERVAL 6 MINUTE)
  ),
  ('admin', 1, 'queue.updated',
    JSON_OBJECT('ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'B-2001'), 'status', 'done'),
    DATE_ADD(NOW() - INTERVAL 1 DAY, INTERVAL 22 MINUTE)
  ),
  ('admin', 1, 'queue.completed',
    JSON_OBJECT('ticket_no', 'B-2001'),
    DATE_ADD(NOW() - INTERVAL 1 DAY, INTERVAL 22 MINUTE)
  ),
  ('admin', 1, 'queue.serving',
    JSON_OBJECT('ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'B-2005')),
    DATE_ADD(NOW() - INTERVAL 3 HOUR, INTERVAL 7 MINUTE)
  ),
  ('admin', 1, 'queue.updated',
    JSON_OBJECT('ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'B-2005'), 'status', 'done'),
    DATE_ADD(NOW() - INTERVAL 3 HOUR, INTERVAL 24 MINUTE)
  ),
  ('admin', 1, 'queue.completed',
    JSON_OBJECT('ticket_no', 'B-2005'),
    DATE_ADD(NOW() - INTERVAL 3 HOUR, INTERVAL 24 MINUTE)
  ),
  ('admin', 1, 'queue.serving',
    JSON_OBJECT('ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'B-2006')),
    DATE_ADD(NOW() - INTERVAL 2 HOUR, INTERVAL 5 MINUTE)
  ),
  ('admin', 1, 'queue.updated',
    JSON_OBJECT('ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'B-2006'), 'status', 'done'),
    DATE_ADD(NOW() - INTERVAL 2 HOUR, INTERVAL 19 MINUTE)
  ),
  ('admin', 1, 'queue.completed',
    JSON_OBJECT('ticket_no', 'B-2006'),
    DATE_ADD(NOW() - INTERVAL 2 HOUR, INTERVAL 19 MINUTE)
  ),
  ('admin', 1, 'queue.serving',
    JSON_OBJECT('ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'B-2007')),
    DATE_ADD(NOW() - INTERVAL 90 MINUTE, INTERVAL 4 MINUTE)
  ),
  ('admin', 1, 'queue.updated',
    JSON_OBJECT('ticket_id', (SELECT id FROM queue_tickets WHERE ticket_no = 'B-2007'), 'status', 'done'),
    DATE_ADD(NOW() - INTERVAL 90 MINUTE, INTERVAL 17 MINUTE)
  ),
  ('admin', 1, 'queue.completed',
    JSON_OBJECT('ticket_no', 'B-2007'),
    DATE_ADD(NOW() - INTERVAL 90 MINUTE, INTERVAL 17 MINUTE)
  );

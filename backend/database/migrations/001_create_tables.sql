CREATE TABLE IF NOT EXISTS residents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS resident_ids (
  id INT AUTO_INCREMENT PRIMARY KEY,
  resident_id INT NOT NULL,
  valid_id_url VARCHAR(255) NOT NULL,
  verification_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL,
  FOREIGN KEY (resident_id) REFERENCES residents(id)
);

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('staff_admin', 'super_admin') NOT NULL,
  service_ids JSON NULL,
  created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  code VARCHAR(50) NOT NULL UNIQUE,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS kiosk_devices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  device_id VARCHAR(120) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  token VARCHAR(255) NOT NULL,
  last_seen_at DATETIME NULL
);

CREATE TABLE IF NOT EXISTS queue_tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_no VARCHAR(50) NOT NULL,
  resident_id INT NOT NULL,
  service_id INT NOT NULL,
  status ENUM('waiting', 'serving', 'done', 'cancelled') NOT NULL DEFAULT 'waiting',
  issued_at DATETIME NOT NULL,
  issued_date DATE NOT NULL,
  kiosk_device_id INT NULL,
  idempotency_key VARCHAR(100) NOT NULL,
  created_at DATETIME NOT NULL,
  UNIQUE KEY uq_queue_tickets_idempotency (idempotency_key),
  INDEX idx_queue_tickets_service_date_status (service_id, issued_date, status),
  INDEX idx_queue_tickets_resident_service_date (resident_id, service_id, issued_date),
  FOREIGN KEY (resident_id) REFERENCES residents(id),
  FOREIGN KEY (service_id) REFERENCES services(id),
  FOREIGN KEY (kiosk_device_id) REFERENCES kiosk_devices(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  actor_type VARCHAR(50) NOT NULL,
  actor_id INT NOT NULL,
  action VARCHAR(150) NOT NULL,
  meta_json JSON NULL,
  created_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS migrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(255) NOT NULL UNIQUE,
  executed_at DATETIME NOT NULL
);

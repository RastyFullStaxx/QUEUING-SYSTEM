CREATE TABLE IF NOT EXISTS queue_ticket_services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id INT NOT NULL,
  service_id INT NOT NULL,
  created_at DATETIME NOT NULL,
  UNIQUE KEY uq_queue_ticket_service (ticket_id, service_id),
  INDEX idx_queue_ticket_services_ticket (ticket_id),
  INDEX idx_queue_ticket_services_service (service_id),
  FOREIGN KEY (ticket_id) REFERENCES queue_tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (service_id) REFERENCES services(id)
);

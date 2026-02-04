ALTER TABLE residents
  ADD COLUMN status_message TEXT NULL AFTER status;

ALTER TABLE admins
  ADD COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
  ADD COLUMN status_message TEXT NULL,
  ADD COLUMN valid_id_url VARCHAR(255) NULL;

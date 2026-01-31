ALTER TABLE residents
  ADD COLUMN username VARCHAR(80) NULL,
  ADD COLUMN middle_name VARCHAR(100) NULL,
  ADD COLUMN date_of_birth DATE NULL,
  ADD COLUMN gender VARCHAR(30) NULL,
  ADD COLUMN civil_status VARCHAR(30) NULL,
  ADD COLUMN mobile_number VARCHAR(30) NULL,
  ADD COLUMN address TEXT NULL,
  ADD COLUMN profile_photo_url VARCHAR(255) NULL;

ALTER TABLE residents
  ADD UNIQUE KEY uq_residents_username (username);

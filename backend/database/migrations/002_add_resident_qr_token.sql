ALTER TABLE residents
  ADD COLUMN qr_token VARCHAR(64) NULL;

ALTER TABLE residents
  ADD UNIQUE KEY uq_residents_qr_token (qr_token);

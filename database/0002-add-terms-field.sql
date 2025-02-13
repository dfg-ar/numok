-- Add terms column to programs table
ALTER TABLE programs
ADD COLUMN terms TEXT NULL AFTER description;

-- Add terms_accepted column to partner_programs table
ALTER TABLE partner_programs 
ADD COLUMN terms_accepted TIMESTAMP NULL DEFAULT NULL,
ADD COLUMN terms_accepted_ip VARCHAR(45) NULL DEFAULT NULL;
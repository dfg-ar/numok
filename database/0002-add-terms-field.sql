-- Add terms column to programs table
-- Note: PostgreSQL does not support AFTER clause for column positioning
ALTER TABLE programs
ADD COLUMN terms TEXT;

-- Add terms_accepted column to partner_programs table
ALTER TABLE partner_programs
ADD COLUMN terms_accepted TIMESTAMP DEFAULT NULL,
ADD COLUMN terms_accepted_ip VARCHAR(45) DEFAULT NULL;

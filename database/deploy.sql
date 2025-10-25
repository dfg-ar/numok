-- PostgreSQL Schema for Numok Affiliate Program
-- This schema has been converted from MySQL
-- This is the full deployment schema with all migrations applied

-- Create update trigger function for updated_at columns
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';


-- Dump of table clicks
-- ------------------------------------------------------------

DROP TABLE IF EXISTS clicks CASCADE;

CREATE TABLE clicks (
  id SERIAL PRIMARY KEY,
  partner_program_id INTEGER NOT NULL,
  click_id VARCHAR(100) NOT NULL,
  ip_address VARCHAR(45) DEFAULT NULL,
  user_agent TEXT,
  referer VARCHAR(255) DEFAULT NULL,
  sub_ids JSONB DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (click_id)
);

CREATE INDEX idx_clicks_partner_program_id ON clicks(partner_program_id);
CREATE INDEX idx_click_id ON clicks(click_id);



-- Dump of table conversions
-- ------------------------------------------------------------

DROP TABLE IF EXISTS conversions CASCADE;

CREATE TABLE conversions (
  id SERIAL PRIMARY KEY,
  partner_program_id INTEGER NOT NULL,
  stripe_payment_id VARCHAR(100) NOT NULL,
  amount NUMERIC(10,2) NOT NULL,
  commission_amount NUMERIC(10,2) NOT NULL,
  status TEXT DEFAULT 'pending' CHECK (status IN ('pending','payable','rejected','paid')),
  customer_email VARCHAR(255) DEFAULT NULL,
  metadata JSONB DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (stripe_payment_id)
);

CREATE INDEX idx_conversions_partner_program_id ON conversions(partner_program_id);
CREATE INDEX idx_stripe_payment ON conversions(stripe_payment_id);

-- Create trigger for updated_at
CREATE TRIGGER update_conversions_updated_at
    BEFORE UPDATE ON conversions
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();



-- Dump of table logs
-- ------------------------------------------------------------

DROP TABLE IF EXISTS logs CASCADE;

CREATE TABLE logs (
  id SERIAL PRIMARY KEY,
  type VARCHAR(50) NOT NULL,
  message TEXT NOT NULL,
  context JSONB DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_logs_type ON logs(type);
CREATE INDEX idx_logs_created_at ON logs(created_at);



-- Dump of table partners
-- ------------------------------------------------------------

DROP TABLE IF EXISTS partners CASCADE;

CREATE TABLE partners (
  id SERIAL PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  company_name VARCHAR(255) NOT NULL,
  contact_name VARCHAR(255) NOT NULL,
  status TEXT DEFAULT 'pending' CHECK (status IN ('pending','active','rejected','suspended')),
  payment_email VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (email)
);

-- Create trigger for updated_at
CREATE TRIGGER update_partners_updated_at
    BEFORE UPDATE ON partners
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();



-- Dump of table programs
-- ------------------------------------------------------------

DROP TABLE IF EXISTS programs CASCADE;

CREATE TABLE programs (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  terms TEXT,
  commission_type TEXT NOT NULL CHECK (commission_type IN ('percentage','fixed')),
  commission_value NUMERIC(10,2) NOT NULL,
  cookie_days INTEGER DEFAULT 30,
  is_recurring BOOLEAN DEFAULT FALSE,
  reward_days INTEGER DEFAULT 0,
  landing_page VARCHAR(255) DEFAULT NULL,
  status TEXT DEFAULT 'active' CHECK (status IN ('active','inactive')),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create trigger for updated_at
CREATE TRIGGER update_programs_updated_at
    BEFORE UPDATE ON programs
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();



-- Dump of table partner_programs
-- ------------------------------------------------------------

DROP TABLE IF EXISTS partner_programs CASCADE;

CREATE TABLE partner_programs (
  id SERIAL PRIMARY KEY,
  partner_id INTEGER NOT NULL,
  program_id INTEGER NOT NULL,
  tracking_code VARCHAR(50) NOT NULL,
  postback_url VARCHAR(255) DEFAULT NULL,
  status TEXT DEFAULT 'active' CHECK (status IN ('active','inactive')),
  terms_accepted TIMESTAMP DEFAULT NULL,
  terms_accepted_ip VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (tracking_code)
);

CREATE INDEX idx_partner_programs_partner_id ON partner_programs(partner_id);
CREATE INDEX idx_partner_programs_program_id ON partner_programs(program_id);
CREATE INDEX idx_tracking_code ON partner_programs(tracking_code);

-- Create trigger for updated_at
CREATE TRIGGER update_partner_programs_updated_at
    BEFORE UPDATE ON partner_programs
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();



-- Dump of table settings
-- ------------------------------------------------------------

DROP TABLE IF EXISTS settings CASCADE;

CREATE TABLE settings (
  id SERIAL PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  value TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (name)
);

CREATE INDEX idx_setting_name ON settings(name);

-- Create trigger for updated_at
CREATE TRIGGER update_settings_updated_at
    BEFORE UPDATE ON settings
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();



-- Dump of table users
-- ------------------------------------------------------------

DROP TABLE IF EXISTS users CASCADE;

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  is_admin BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (email)
);

-- Create trigger for updated_at
CREATE TRIGGER update_users_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();



-- Add foreign key constraints
-- Note: These must be added after all tables are created

ALTER TABLE clicks
  ADD CONSTRAINT clicks_partner_program_id_fkey
  FOREIGN KEY (partner_program_id)
  REFERENCES partner_programs(id)
  ON DELETE CASCADE;

ALTER TABLE conversions
  ADD CONSTRAINT conversions_partner_program_id_fkey
  FOREIGN KEY (partner_program_id)
  REFERENCES partner_programs(id)
  ON DELETE CASCADE;

ALTER TABLE partner_programs
  ADD CONSTRAINT partner_programs_partner_id_fkey
  FOREIGN KEY (partner_id)
  REFERENCES partners(id)
  ON DELETE CASCADE,
  ADD CONSTRAINT partner_programs_program_id_fkey
  FOREIGN KEY (program_id)
  REFERENCES programs(id)
  ON DELETE CASCADE;

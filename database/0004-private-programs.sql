-- Add is_private column to programs table
-- Default to 0 (public) to preserve existing program behavior
ALTER TABLE programs
ADD COLUMN is_private TINYINT(1) NOT NULL DEFAULT 0 AFTER status;

-- Add index for efficient filtering on visibility
ALTER TABLE programs
ADD INDEX idx_is_private (is_private);

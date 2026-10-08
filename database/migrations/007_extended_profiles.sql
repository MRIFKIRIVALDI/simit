ALTER TABLE users ADD COLUMN employee_id TEXT;
ALTER TABLE users ADD COLUMN position TEXT;
ALTER TABLE users ADD COLUMN department TEXT;
ALTER TABLE users ADD COLUMN address TEXT;
ALTER TABLE users ADD COLUMN birth_date TEXT;
ALTER TABLE users ADD COLUMN avatar_stored_name TEXT;
ALTER TABLE users ADD COLUMN avatar_mime_type TEXT;
CREATE UNIQUE INDEX IF NOT EXISTS idx_users_employee_id ON users(employee_id) WHERE employee_id IS NOT NULL;

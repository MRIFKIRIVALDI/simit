ALTER TABLE software ADD COLUMN starts_at TEXT;
ALTER TABLE software ADD COLUMN ends_at TEXT;
ALTER TABLE tasks ADD COLUMN software_id INTEGER REFERENCES software(id);
UPDATE users SET role='user' WHERE role<>'admin';
UPDATE assets SET location_id=(SELECT id FROM locations WHERE name='Lab 1') WHERE location_id=(SELECT id FROM locations WHERE name='Lab Komputer');
DELETE FROM locations WHERE name='Lab Komputer';
CREATE INDEX IF NOT EXISTS idx_tasks_software_status ON tasks(software_id,status);

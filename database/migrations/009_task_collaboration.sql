CREATE TABLE IF NOT EXISTS task_contributions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    task_id INTEGER NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id),
    body TEXT NOT NULL,
    is_final INTEGER NOT NULL DEFAULT 0 CHECK(is_final IN (0,1)),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_task_contributions_task ON task_contributions(task_id,created_at);
CREATE INDEX IF NOT EXISTS idx_task_contributions_user ON task_contributions(user_id,created_at);

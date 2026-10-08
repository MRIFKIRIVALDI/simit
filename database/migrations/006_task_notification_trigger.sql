CREATE TRIGGER IF NOT EXISTS notify_users_on_new_task
AFTER INSERT ON tasks
BEGIN
    INSERT INTO notifications(user_id,title,message,created_at)
    SELECT id,'Tugas baru tersedia',NEW.title,CURRENT_TIMESTAMP
    FROM users
    WHERE is_active=1 AND id<>COALESCE(NEW.created_by,0);
END;

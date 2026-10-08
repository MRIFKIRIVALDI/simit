ALTER TABLE notifications ADD COLUMN link_url TEXT;
UPDATE notifications SET link_url = '/chat' WHERE link_url IS NULL AND (title LIKE '%chat%' OR title LIKE '%Chat%');
UPDATE notifications SET link_url = '/persetujuan' WHERE link_url IS NULL AND title LIKE '%ACC%';
UPDATE notifications SET link_url = '/tugas' WHERE link_url IS NULL AND (title LIKE '%Tugas%' OR title LIKE '%task%');
DROP TRIGGER IF EXISTS notify_new_task;
CREATE TRIGGER notify_new_task AFTER INSERT ON tasks
WHEN NEW.status = 'Tersedia'
BEGIN
    INSERT INTO notifications(user_id,title,message,link_url)
    SELECT id,'Tugas baru tersedia','Tugas baru: ' || NEW.title,'/tugas/hasil/' || NEW.id
    FROM users WHERE is_active=1 AND id<>COALESCE(NEW.created_by,0);
END;

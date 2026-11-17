DROP TRIGGER IF EXISTS notify_users_on_new_task;
DROP TRIGGER IF EXISTS notify_new_task;
CREATE TRIGGER notify_new_task AFTER INSERT ON tasks FOR EACH ROW
INSERT INTO notifications(user_id,title,message,link_url)
SELECT id,'Tugas baru tersedia',CONCAT('Tugas baru: ',NEW.title),CONCAT('/tugas/hasil/',NEW.id)
FROM users
WHERE NEW.status='Tersedia' AND is_active=1 AND id<>COALESCE(NEW.created_by,0);

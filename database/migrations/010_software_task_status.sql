UPDATE tasks
SET status='Dikerjakan', updated_at=CURRENT_TIMESTAMP
WHERE software_id IS NOT NULL
  AND status IN ('Tersedia','Draft')
  AND (title LIKE 'Instal %' OR title LIKE 'Install %');
UPDATE tasks
SET status='Dikerjakan', updated_at=CURRENT_TIMESTAMP
WHERE software_id IS NOT NULL
  AND status IN ('Tersedia','Draft')
  AND title LIKE 'Hapus %';

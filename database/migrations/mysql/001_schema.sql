CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    is_active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    phone VARCHAR(40) NULL,
    bio TEXT NULL,
    employee_id VARCHAR(80) NULL,
    position VARCHAR(120) NULL,
    department VARCHAR(120) NULL,
    address TEXT NULL,
    birth_date DATE NULL,
    avatar_stored_name VARCHAR(255) NULL,
    avatar_mime_type VARCHAR(100) NULL,
    UNIQUE KEY uq_users_email (email),
    UNIQUE KEY uq_users_employee_id (employee_id),
    CONSTRAINT chk_users_active CHECK (is_active IN (0,1)),
    CONSTRAINT chk_users_role CHECK (role IN ('admin','user'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS locations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_locations_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS asset_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_asset_categories_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(100) NOT NULL,
    type VARCHAR(20) NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    location_id BIGINT UNSIGNED NULL,
    name VARCHAR(190) NOT NULL,
    brand VARCHAR(120) NULL,
    model VARCHAR(120) NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    status VARCHAR(50) NOT NULL DEFAULT 'Baik',
    physical_condition VARCHAR(50) NOT NULL DEFAULT 'Baik',
    notes TEXT NULL,
    archived_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_assets_code (code),
    KEY idx_assets_status_location (status,location_id),
    CONSTRAINT chk_assets_type CHECK (type IN ('PC','Perangkat')),
    CONSTRAINT chk_assets_quantity CHECK (quantity > 0),
    CONSTRAINT fk_assets_category FOREIGN KEY (category_id) REFERENCES asset_categories(id),
    CONSTRAINT fk_assets_location FOREIGN KEY (location_id) REFERENCES locations(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pc_details (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id BIGINT UNSIGNED NOT NULL,
    pc_name VARCHAR(190) NOT NULL,
    ip_address VARCHAR(45) NULL,
    processor VARCHAR(190) NULL,
    ram_gb SMALLINT UNSIGNED NULL,
    storage_type VARCHAR(50) NULL,
    storage_value VARCHAR(100) NULL,
    os VARCHAR(120) NULL,
    os_version VARCHAR(120) NULL,
    monitor_condition VARCHAR(50) NULL,
    peripheral_condition VARCHAR(50) NULL,
    last_checked_at DATE NULL,
    last_os_update_at DATE NULL,
    UNIQUE KEY uq_pc_details_asset (asset_id),
    UNIQUE KEY uq_pc_details_name (pc_name),
    CONSTRAINT fk_pc_details_asset FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS software (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    category VARCHAR(120) NULL,
    version VARCHAR(100) NULL,
    size VARCHAR(100) NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Terinstall',
    purpose VARCHAR(255) NULL,
    target_remove_at DATE NULL,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    starts_at DATE NULL,
    ends_at DATE NULL,
    UNIQUE KEY uq_software_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    instructions TEXT NULL,
    category VARCHAR(100) NULL,
    priority VARCHAR(30) NOT NULL DEFAULT 'Sedang',
    estimate_minutes INT UNSIGNED NULL,
    assignee_id BIGINT UNSIGNED NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Draft',
    due_at DATE NULL,
    taken_at DATETIME NULL,
    completed_at DATETIME NULL,
    result TEXT NULL,
    revision_note TEXT NULL,
    created_by BIGINT UNSIGNED NULL,
    verified_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    software_id BIGINT UNSIGNED NULL,
    UNIQUE KEY uq_tasks_number (number),
    KEY idx_tasks_status_assignee_due (status,assignee_id,due_at),
    KEY idx_tasks_software_status (software_id,status),
    CONSTRAINT fk_tasks_assignee FOREIGN KEY (assignee_id) REFERENCES users(id),
    CONSTRAINT fk_tasks_created_by FOREIGN KEY (created_by) REFERENCES users(id),
    CONSTRAINT fk_tasks_verified_by FOREIGN KEY (verified_by) REFERENCES users(id),
    CONSTRAINT fk_tasks_software FOREIGN KEY (software_id) REFERENCES software(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS task_assets (
    task_id BIGINT UNSIGNED NOT NULL,
    asset_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (task_id,asset_id),
    CONSTRAINT fk_task_assets_task FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    CONSTRAINT fk_task_assets_asset FOREIGN KEY (asset_id) REFERENCES assets(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS inspections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id BIGINT UNSIGNED NOT NULL,
    inspector_id BIGINT UNSIGNED NULL,
    inspected_at DATE NOT NULL,
    condition_before VARCHAR(50) NULL,
    condition_after VARCHAR(50) NULL,
    findings TEXT NULL,
    affected_count INT UNSIGNED NOT NULL DEFAULT 0,
    followup_task_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_inspections_asset_date (asset_id,inspected_at),
    CONSTRAINT fk_inspections_asset FOREIGN KEY (asset_id) REFERENCES assets(id),
    CONSTRAINT fk_inspections_user FOREIGN KEY (inspector_id) REFERENCES users(id),
    CONSTRAINT fk_inspections_task FOREIGN KEY (followup_task_id) REFERENCES tasks(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    location_id BIGINT UNSIGNED NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Belum Siap',
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_events_location FOREIGN KEY (location_id) REFERENCES locations(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id BIGINT UNSIGNED NULL,
    detail TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_logs_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS task_status_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    from_status VARCHAR(50) NULL,
    to_status VARCHAR(50) NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_task_history_task FOREIGN KEY (task_id) REFERENCES tasks(id),
    CONSTRAINT fk_task_history_user FOREIGN KEY (changed_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS asset_condition_counts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    asset_id BIGINT UNSIGNED NOT NULL,
    `condition` VARCHAR(50) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    UNIQUE KEY uq_asset_condition (asset_id,`condition`),
    CONSTRAINT fk_asset_condition_asset FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS software_installations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    software_id BIGINT UNSIGNED NOT NULL,
    asset_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(50) NOT NULL,
    installed_at DATE NULL,
    target_remove_at DATE NULL,
    removed_at DATE NULL,
    pic_id BIGINT UNSIGNED NULL,
    event_id BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    UNIQUE KEY uq_software_asset (software_id,asset_id),
    CONSTRAINT fk_installations_software FOREIGN KEY (software_id) REFERENCES software(id),
    CONSTRAINT fk_installations_asset FOREIGN KEY (asset_id) REFERENCES assets(id),
    CONSTRAINT fk_installations_user FOREIGN KEY (pic_id) REFERENCES users(id),
    CONSTRAINT fk_installations_event FOREIGN KEY (event_id) REFERENCES events(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    link_url VARCHAR(2048) NULL,
    KEY idx_notifications_user_unread (user_id,read_at,created_at),
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS import_batches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    source_name VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'preview',
    accepted_rows INT UNSIGNED NOT NULL DEFAULT 0,
    rejected_rows INT UNSIGNED NOT NULL DEFAULT 0,
    created_by BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_import_batches_user FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    uploader_id BIGINT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size_bytes BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_attachments_stored_name (stored_name),
    KEY idx_attachments_task (task_id,created_at),
    CONSTRAINT fk_attachments_task FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    CONSTRAINT fk_attachments_user FOREIGN KEY (uploader_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sender_id BIGINT UNSIGNED NOT NULL,
    recipient_id BIGINT UNSIGNED NOT NULL,
    body TEXT NOT NULL,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_messages_thread (sender_id,recipient_id,created_at),
    KEY idx_messages_recipient_unread (recipient_id,read_at),
    CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id),
    CONSTRAINT fk_messages_recipient FOREIGN KEY (recipient_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS task_contributions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    body TEXT NOT NULL,
    is_final TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_task_contributions_task (task_id,created_at),
    KEY idx_task_contributions_user (user_id,created_at),
    CONSTRAINT chk_task_contributions_final CHECK (is_final IN (0,1)),
    CONSTRAINT fk_task_contributions_task FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    CONSTRAINT fk_task_contributions_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(190) NOT NULL,
    successful TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_login_attempts_window (created_at,ip_address,email),
    CONSTRAINT chk_login_attempts_success CHECK (successful IN (0,1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Pulse Feature - Database Setup
-- ============================================

-- 1. Add showpulse to settings
ALTER TABLE settings ADD COLUMN showpulse INT DEFAULT 1;

-- 2. Add sid_pulse to usr_pwrs (roles)
ALTER TABLE usr_pwrs ADD COLUMN sid_pulse INT DEFAULT 1;

-- 3. Create pulse_types table
CREATE TABLE IF NOT EXISTS pulse_types (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category ENUM('positive','negative') NOT NULL DEFAULT 'positive',
    icon VARCHAR(50) DEFAULT 'fas fa-star',
    points INT DEFAULT 1,
    isdeleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Create pulse_logs table
CREATE TABLE IF NOT EXISTS pulse_logs (
    id INT(11) NOT NULL AUTO_INCREMENT,
    employee_id INT(11) NOT NULL,
    type_id INT(11) NOT NULL,
    category ENUM('positive','negative') NOT NULL,
    rating INT DEFAULT 5,
    notes TEXT,
    recorded_by INT(11) NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_employee (employee_id),
    KEY idx_type (type_id),
    KEY idx_recorded_at (recorded_at),
    KEY idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Insert default pulse types
INSERT INTO pulse_types (name, category, icon, points) VALUES
('الالتزام بالمواعيد', 'positive', 'fas fa-clock', 3),
('جودة العمل', 'positive', 'fas fa-award', 5),
('روح الفريق', 'positive', 'fas fa-users', 4),
('المبادرة', 'positive', 'fas fa-lightbulb', 5),
('خدمة العملاء', 'positive', 'fas fa-handshake', 4),
('النظافة والترتيب', 'positive', 'fas fa-broom', 2),
('التأخر', 'negative', 'fas fa-clock', -3),
('الإهمال', 'negative', 'fas fa-exclamation-triangle', -5),
('عدم التعاون', 'negative', 'fas fa-user-slash', -4),
('سوء التعامل', 'negative', 'fas fa-frown', -5);

<?php

return new class {
    /**
     * Run the migrations.
     * 
     * @param mysqli $conn
     */
    public function up($conn) {
        // 1. Column: sid_visits in usr_pwrs
        try {
            $colVisits = $conn->query("SHOW COLUMNS FROM usr_pwrs LIKE 'sid_visits'");
            if ($colVisits && $colVisits->num_rows === 0) {
                $conn->query('ALTER TABLE usr_pwrs ADD COLUMN sid_visits INT DEFAULT 1');
            }
        } catch (Exception $e) {
            // Ignore if already exists
        }

        // 2. Column: show_main_hr in usr_pwrs
        try {
            $colMainHr = $conn->query("SHOW COLUMNS FROM usr_pwrs LIKE 'show_main_hr'");
            if ($colMainHr && $colMainHr->num_rows === 0) {
                $conn->query('ALTER TABLE usr_pwrs ADD COLUMN show_main_hr TINYINT(1) NOT NULL DEFAULT 1');
            }
        } catch (Exception $e) {
            // Ignore if already exists
        }

        // 3. Column: calc_type in employees
        try {
            $colCalcType = $conn->query("SHOW COLUMNS FROM employees LIKE 'calc_type'");
            if ($colCalcType && $colCalcType->num_rows === 0) {
                $conn->query("ALTER TABLE employees ADD COLUMN calc_type VARCHAR(20) DEFAULT 'monthly'");
            }
        } catch (Exception $e) {
            // Ignore if already exists
        }

        // 4. Column: missing_fingerprint_calc in settings
        try {
            $colMissingFp = $conn->query("SHOW COLUMNS FROM settings LIKE 'missing_fingerprint_calc'");
            if ($colMissingFp && $colMissingFp->num_rows === 0) {
                $conn->query("ALTER TABLE settings ADD COLUMN missing_fingerprint_calc DECIMAL(3,1) DEFAULT 0.5");
            }
        } catch (Exception $e) {
            // Ignore if already exists
        }

        // 5. Column: holiday_work_calc in settings
        try {
            $colHolidayWork = $conn->query("SHOW COLUMNS FROM settings LIKE 'holiday_work_calc'");
            if ($colHolidayWork && $colHolidayWork->num_rows === 0) {
                $conn->query("ALTER TABLE settings ADD COLUMN holiday_work_calc TINYINT(1) DEFAULT 1");
            }
        } catch (Exception $e) {
            // Ignore if already exists
        }

        // 6. Table: financial_transactions
        $conn->query("CREATE TABLE IF NOT EXISTS `financial_transactions` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `snd_id` INT(11) NOT NULL,
          `date` DATE NOT NULL,
          `emp_name` VARCHAR(100) NOT NULL,
          `type` TINYINT(1) NOT NULL,
          `amount` DECIMAL(10,2) NOT NULL,
          `reason` VARCHAR(255) NOT NULL,
          `notes` VARCHAR(255) DEFAULT NULL,
          `crtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    }

    /**
     * Reverse the migrations.
     * 
     * @param mysqli $conn
     */
    public function down($conn) {
        // Drop added columns
        try {
            $conn->query("ALTER TABLE usr_pwrs DROP COLUMN sid_visits");
        } catch (Exception $e) {}

        try {
            $conn->query("ALTER TABLE usr_pwrs DROP COLUMN show_main_hr");
        } catch (Exception $e) {}

        try {
            $conn->query("ALTER TABLE employees DROP COLUMN calc_type");
        } catch (Exception $e) {}

        try {
            $conn->query("ALTER TABLE settings DROP COLUMN missing_fingerprint_calc");
        } catch (Exception $e) {}

        try {
            $conn->query("ALTER TABLE settings DROP COLUMN holiday_work_calc");
        } catch (Exception $e) {}

        // Drop financial_transactions table
        $conn->query("DROP TABLE IF EXISTS `financial_transactions`");
    }
};

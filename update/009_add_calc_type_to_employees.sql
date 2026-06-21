-- Add salary period (calc_type) to employees table
-- monthly = شهري (default, backward compatible)
-- daily   = يومي

ALTER TABLE `employees`
ADD COLUMN `calc_type` VARCHAR(20) NOT NULL DEFAULT 'monthly'
AFTER `salary`;

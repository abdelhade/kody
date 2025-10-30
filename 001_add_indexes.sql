-- ==========================================
-- Migration: 001 - Add Performance Indexes
-- Purpose: إضافة indexes للأعمدة المستخدمة بكثرة
-- Date: 2025-10-17
-- Safe to run: YES (لا يؤثر على البيانات)
-- ==========================================

USE hrmsnat;

-- Indexes for myitems table
ALTER TABLE `myitems` 
  ADD INDEX `idx_barcode` (`barcode`),
  ADD INDEX `idx_group1` (`group1`),
  ADD INDEX `idx_group2` (`group2`),
  ADD INDEX `idx_group3` (`group3`),
  ADD INDEX `idx_isdeleted` (`isdeleted`),
  ADD INDEX `idx_iname` (`iname`(50)),
  ADD FULLTEXT INDEX `idx_search` (`iname`, `info`);

-- Indexes for ot_head table
ALTER TABLE `ot_head`
  ADD INDEX `idx_pro_date` (`pro_date`),
  ADD INDEX `idx_pro_tybe` (`pro_tybe`),
  ADD INDEX `idx_acc1` (`acc1`),
  ADD INDEX `idx_acc2` (`acc2`),
  ADD INDEX `idx_store_id` (`store_id`),
  ADD INDEX `idx_emp_id` (`emp_id`),
  ADD INDEX `idx_branch` (`branch_id`),
  ADD INDEX `idx_pro_num` (`pro_num`);

-- Indexes for fat_details table
ALTER TABLE `fat_details`
  ADD INDEX `idx_pro_id` (`pro_id`),
  ADD INDEX `idx_item_id` (`item_id`),
  ADD INDEX `idx_pro_tybe` (`pro_tybe`),
  ADD INDEX `idx_fat_tybe` (`fat_tybe`);

-- Indexes for imgs table
ALTER TABLE `imgs`
  ADD INDEX `idx_itemid` (`itemid`),
  ADD INDEX `idx_clprofile` (`clprofile`);

-- Indexes for item_group tables
ALTER TABLE `item_group`
  ADD INDEX `idx_parent` (`parent`);

-- Indexes for acc_head table
ALTER TABLE `acc_head`
  ADD INDEX `idx_code` (`code`),
  ADD INDEX `idx_parent_id` (`parent_id`),
  ADD INDEX `idx_is_stock` (`is_stock`),
  ADD INDEX `idx_is_fund` (`is_fund`),
  ADD INDEX `idx_isdeleted` (`isdeleted`),
  ADD FULLTEXT INDEX `idx_search` (`aname`);

-- Indexes for users table
ALTER TABLE `users`
  ADD INDEX `idx_username` (`username`);

-- Indexes for journal_entries table
ALTER TABLE `journal_entries`
  ADD INDEX `idx_journal_id` (`journal_id`),
  ADD INDEX `idx_account_id` (`account_id`),
  ADD INDEX `idx_entry_date` (`entry_date`);

-- Index for settings (usually single row, but good practice)
ALTER TABLE `settings`
  ADD UNIQUE INDEX `idx_id` (`id`);

SELECT 'Indexes created successfully!' as status;


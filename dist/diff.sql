#---------- UP ----------
DROP TABLE `group37_workstations`;
INSERT INTO `setting` VALUES('g41.closing_time','5:00pm','The time when OPR closes');
DELETE FROM `setting` WHERE `_name` = 'g36.no_bucket_max_weight';
DELETE FROM `setting` WHERE `_name` = 'g37.no_bucket_max_weight';
DELETE FROM `ship_settings` WHERE `name` = 'leftover.verify_limit_al';
DELETE FROM `ship_settings` WHERE `name` = 'leftover.verify_limit_plastic1';
#---------- DOWN ----------

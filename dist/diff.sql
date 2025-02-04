#---------- UP ----------
ALTER TABLE `setting` DEFAULT COLLATE utf8_general_ci;
ALTER TABLE `temp` DEFAULT COLLATE utf8_general_ci;
INSERT INTO `setting` VALUES('g41.closing_time','5:00pm','The time when OPR closes');
#---------- DOWN ----------

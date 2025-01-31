#---------- UP ----------
CREATE TABLE `group38_dropped_bags` (
  `bag_id` varchar(256) NOT NULL,
  `drop_off_location` varchar(256) NOT NULL,
  `accepted_time` varchar(256) DEFAULT NULL,
  `is_counted` bit(1) NOT NULL,
  PRIMARY KEY (`bag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `test1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE TABLE `test2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DROP TABLE `group37_workstations`;
delimiter //
CREATE DEFINER=`opr`@`%` TRIGGER test1 AFTER INSERT ON group39_dl_items FOR EACH ROW
BEGIN
    INSERT INTO group39_dl_item_changes (itemid, name, checked, checkerid, checktime, creationtime, creationid, assigneeid, assignerid, assigntime, status, statustime, statusid, listid, listtype, mailinglists, disabled, activated, activatetime)
    VALUES (new.id, new.name, new.checked, new.checkerid, new.checktime, new.creationtime, new.creationid, new.assigneeid, new.assignerid, new.assigntime, new.status, new.statustime, new.statusid, new.listid, new.listtype, new.mailinglists, new.disabled, new.activated, new.activatetime);
END;//
delimiter ;
delimiter //
CREATE DEFINER=`opr`@`%` trigger test_trigger2 after update on group39_dl_items for each row
BEGIN
    INSERT INTO group39_dl_item_changes (itemid, name, checked, checkerid, checktime, creationtime, creationid, assigneeid, assignerid, assigntime, status, statustime, statusid, listid, listtype, mailinglists, disabled, activated, activatetime)
    VALUES(new.id,new.name,new.checked,new.checkerid,new.checktime,new.creationtime,new.creationid,new.assigneeid,new.assignerid,new.assigntime,new.status,new.statustime,new.statusid,new.listid,new.listtype,new.mailinglists,new.disabled,new.activatetime, new.activated);
END;//
delimiter ;
delimiter //
CREATE DEFINER=`opr`@`%` trigger test_inserted after insert on test1 for each row
begin
    insert into test2 (name) values(new.name);
end;//
delimiter ;
INSERT INTO `setting` VALUES('g38.scanner_kind','rfid','Kind of the scanner used on group38. It can be qr or rfid');
INSERT INTO `setting` VALUES('g39.checklist_assignee_ids','394,395,396','Employee id list who can be assigned to a pinned checklist items');
INSERT INTO `setting` VALUES('g39.machine_assignee_ids','394,395,396','Employee id list who are mechanics');
INSERT INTO `setting` VALUES('g39.mechanic_ids','394,395,396','Employee id list who are mechanics');
INSERT INTO `setting` VALUES('g39.refresh_time','20:12','The time when the pages will be refreshed');
#---------- DOWN ----------

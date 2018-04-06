ALTER TABLE `prefix_rbac_role_permission` 
ADD `period` INT NULL DEFAULT NULL AFTER `date_create`, 
ADD `count` INT NULL DEFAULT NULL AFTER `period`,
ADD `price` FLOAT NULL AFTER `count`;
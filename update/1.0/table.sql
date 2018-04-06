CREATE TABLE `prefix_rbac_user_stat` ( 
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT , 
`user_id` BIGINT UNSIGNED NOT NULL , 
`rp_id` INT UNSIGNED NOT NULL , 
`count` INT UNSIGNED NULL DEFAULT NULL ,
`count_period` INT UNSIGNED NULL DEFAULT 0 , 
PRIMARY KEY (`id`), 
UNIQUE `uni_rp` (`rp_id`, `user_id`)
) ENGINE = InnoDB;
ALTER TABLE `appointment` ADD `prize_id` TINYINT NULL DEFAULT 0 AFTER `openid`;

ALTER TABLE `user` ADD INDEX user_mobile ( `mobile` ) ;

ALTER TABLE `user` ADD INDEX user_redeem ( `redeem_prize` ) ;

ALTER TABLE `user` ADD INDEX user_result ( `result` ) ;

-----------------------------------------------------------------------

ALTER TABLE `user` ADD INDEX user_viewed ( `viewed` ) ;

ALTER TABLE `checkin` ADD INDEX checkin_userid ( `user_id` ) ;

-- DROP TABLE IF EXISTS `redeem_record`;
-- CREATE TABLE `redeem_record` (
--   `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
--   `store_id` INT NOT NULL COMMENT '门店id',
--   `prize_id` INT NOT NULL COMMENT '奖品id',
--   `mobile` VARCHAR(25) NOT NULL COMMENT '手机号',
--   `created_at` INT NOT NULL COMMENT '创建时间戳',
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `change_store_record`;
CREATE TABLE `change_store_record` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` INT NOT NULL COMMENT '用户id',
  `from` INT NOT NULL COMMENT '原门店id',
  `to` INT NOT NULL COMMENT '新门店id',
  `created_at` INT NOT NULL COMMENT '创建时间戳',
  `updated_at` INT NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-----------------------------------------------------------------------

ALTER TABLE `appointment` ADD INDEX index_mobile (`mobile`);
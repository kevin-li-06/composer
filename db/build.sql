-- #数据库备注:
-- #--------------------------------- DATABASE ---------------------------------

-- DROP DATABASE IF EXISTS `liber`;
-- CREATE DATABASE `liber`;
-- USE `lottery`;

-- #--------------------------------- DATABASE ---------------------------------

-- #--------------------------------- ADMIN TABLE @author william.sun ---------------------------------

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`(
	`id` INT UNSIGNED AUTO_INCREMENT COMMENT 'ID',
	`id_role` INT UNSIGNED COMMENT '外键',
	`username` VARCHAR(255) NOT NULL COMMENT '用户名',
	`auth_key` VARCHAR(255) NOT NULL COMMENT 'Yii自带',
	`password_hash` VARCHAR(255) NOT NULL COMMENT '密码',
	`password_reset_token` VARCHAR(255) NOT NULL COMMENT '重置密码',
	`email` VARCHAR(255) NOT NULL COMMENT '邮箱',
	`status` SMALLINT NOT NULL DEFAULT 10 COMMENT '状态',
	`created_at` INT NOT NULL COMMENT '创建时间戳',
	`updated_at` INT NOT NULL COMMENT '更新时间戳',
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- ADMIN ROLE TABLE @author william.sun ---------------------------------

DROP TABLE IF EXISTS `admin_role`;
CREATE TABLE `admin_role`(
	`id` INT UNSIGNED AUTO_INCREMENT COMMENT 'ID',
	`role` VARCHAR(255) NOT NULL COMMENT '角色',
	PRIMARY KEY (`id`),
	UNIQUE KEY uk_role (`role`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `admin_role` VALUES(1, 'XGATE'), (2, 'JEBSEN'), (3, 'STAFF');

-- #--------------------------------- MIGRATION TABLE @author william.sun ---------------------------------

DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `filename` VARCHAR(255) NOT NULL COMMENT '文件名称',
  `status` TINYINT(1) NOT NULL COMMENT '0 - 未使用 1 - 已使用',
  `env` VARCHAR(255) NOT NULL COMMENT 'dev - 本地环境 prod - 线上环境',
  `migrated_at` INT(10) NULL COMMENT '应用时间戳',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- PRIZE TABLE @author william.sun ---------------------------------

DROP TABLE IF EXISTS `prize`;
CREATE TABLE `prize` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` VARCHAR(255) NOT NULL COMMENT '奖品名称',
  `level` TINYINT NOT NULL COMMENT '奖品等级',
  `stock_num` INT NOT NULL COMMENT '奖品库存数量',
  `gain_num` INT NOT NULL DEFAULT 0 COMMENT '已抽取数量',
  `exchange_num` INT NOT NULL DEFAULT 0 COMMENT '已兑换数量',
  `created_at` INT NOT NULL COMMENT '创建时间戳',
  `updated_at` INT NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY uk_name (`name`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `prize` (name, level, stock_num) VALUES ('Vivienne Westwood 手袋', 2, 100), ('钱包', 2, 100), ('Love Moschion 手袋', 2, 100), ('锁匙扣', 3, 100), ('太阳眼镜', 3, 100), ('手表', 3, 100)
,('饮品买二送一', 4, 100),('价值1680元尊享美容美甲体验（五选三）', 4, 100),('日式芭比美甲体验', 4, 100),('贵宾尊享SPA体验', 4, 100),('价值800元磁共振体验', 4, 100),('价值200元CT体验', 4, 100);

-- #--------------------------------- USER TABLE @author william.sun ---------------------------------

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `dms_id` VARCHAR(255) NULL COMMENT 'DMS ID',
  `username` VARCHAR(10) NULL COMMENT '姓名',
  `gender` CHAR(1) NULL COMMENT '性别',
  `region` VARCHAR(25) NULL COMMENT '地区 SC-南区 NC-北区 EC-东区 WC-西区',
  `auth_key` VARCHAR(255) NULL COMMENT 'Yii自带',
  `mobile` VARCHAR(255) NULL COMMENT '手机号码',
  `mobile_hash` VARCHAR(255) NULL COMMENT '加密mobile',
  `card` VARCHAR(255) NULL COMMENT '会员卡号',
  `openid` VARCHAR(255) NULL COMMENT 'openid',
  `small_chance` TINYINT NULL DEFAULT 0 COMMENT '抽小奖的次数',
  `big_chance` TINYINT NULL DEFAULT 0 COMMENT '抽大奖的次数',
  `lottery_chance` TINYINT NULL DEFAULT 0 COMMENT '抽奖机会',
  `continuous` TINYINT NOT NULL DEFAULT 0 COMMENT '连续签到日期0-4',
  `redeem_prize` INT NULL DEFAULT NULL COMMENT '核销奖品ID',
  `redeem_at` INT NULL DEFAULT NULL COMMENT '核销时间',
  `change_prize` INT NULL DEFAULT NULL COMMENT '变更奖品',
  `change_at` INT NULL DEFAULT NULL COMMENT '变更时间',
  `exported` TINYINT NULL DEFAULT 0 COMMENT '该核销用户是否已经导出',
  `result` TINYINT NULL DEFAULT 0 COMMENT '最高奖品',
  `is_answer` TINYINT NULL DEFAULT 0 COMMENT '是否完成答题',
  `created_at` INT NOT NULL COMMENT '创建时间戳',
  `updated_at` INT NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- RULE TABLE @author william.sun ---------------------------------

DROP TABLE IF EXISTS `rule`;
CREATE TABLE `rule` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `prize_rate` VARCHAR(255) NULL COMMENT '当天的奖品及概率',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- RECORD TABLE @author william.sun ---------------------------------

DROP TABLE IF EXISTS `record`;
CREATE TABLE `record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` INT(11) NOT NULL COMMENT '会员id',
  `type` INT(11) NOT NULL COMMENT '奖品类型 1-Small 2-Big',
  `date` VARCHAR(25) NULL DEFAULT NULL COMMENT '日期Y-m-d作为查询条件',
  `status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '抽奖状态 0-未获取 1-已获取 2-已抽奖 3-已领奖',
  `result` INT NULL DEFAULT NULL COMMENT '抽奖结果 0-未中奖 \d-奖品ID',
  `get_at` INT NULL DEFAULT NULL COMMENT '获取时间戳',
  `lottery_at` INT NULL DEFAULT NULL COMMENT '抽奖时间戳',
  `exchange_at` INT NULL DEFAULT NULL COMMENT '兑奖时间戳',
  `source` VARCHAR(255) NULL DEFAULT NULL COMMENT '抽奖机会来源sign签到share分享consumption消费seven连续签到满7天',
  `receipts_type` INT(11)  NULL COMMENT '小票类型 1为5000 2为10000 1能一天能有两条记录 2一天只能有一条记录',
  `receipts` VARCHAR(255)  NULL  COMMENT '小票',
  `store_id` INT NULL COMMENT '门店',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- JS TICKET TABLE @author arron.luo ---------------------------------

DROP TABLE IF EXISTS `wx_js_ticket`;
CREATE TABLE `wx_js_ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket` varchar(255) DEFAULT NULL COMMENT '票据',
  `expiration_time` varchar(255) DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='js_ticket';

-- #--------------------------------- ACCESS TOKEN TABLE @author william.sun ---------------------------------

DROP TABLE IF EXISTS `wx_accesstoken`;
CREATE TABLE `wx_accesstoken` (
  `id` INT UNSIGNED AUTO_INCREMENT,
	`access_token` VARCHAR(255) NOT NULL COMMENT '微信accesstoken',
	`expires_in` INT NOT NULL COMMENT '过期时间使用当前时间加上7000秒',
	PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- checkin TABLE @author chris.k ---------------------------------

DROP TABLE IF EXISTS `checkin`;
CREATE TABLE `checkin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '会员ID',
  `date` int(10) unsigned NOT NULL COMMENT '签到日期',
  `continuous` int(3) unsigned NOT NULL COMMENT '连续签到',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- STORE TABLE @author bob.qiu ---------------------------------

DROP TABLE IF EXISTS `store`;
CREATE TABLE `store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `storename` varchar(255) DEFAULT NULL COMMENT '中文店名',
  `store_code` varchar(255) DEFAULT NULL COMMENT '门店编号',
  `address` varchar(255) DEFAULT NULL COMMENT '门店详细地址',
  `status` tinyint(10) unsigned DEFAULT '1' COMMENT '门店状态默认为1,2为异常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- APPOINTMENT TABLE @author william.sun ---------------------------------

DROP TABLE IF EXISTS `appointment`;
CREATE TABLE `appointment` (
  `id` INT UNSIGNED AUTO_INCREMENT,
  	`prize_id` VARCHAR(255) NOT NULL COMMENT '奖品id',
	`name` VARCHAR(255) NOT NULL COMMENT '姓名',
	`mobile` VARCHAR(255) NOT NULL COMMENT '手机号',
  `openid` VARCHAR(255) NOT NULL COMMENT 'openid',
  `store_id` INT NOT NULL COMMENT '门店ID',
  `created_at` INT NOT NULL COMMENT '预约时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- #--------------------------------- storestock TABLE @author bob.qiu ---------------------------------

DROP TABLE IF EXISTS `store_stock`;
CREATE TABLE `store_stock` (
  `id` INT UNSIGNED AUTO_INCREMENT,
	`store_id` VARCHAR(255) NOT NULL COMMENT '门店id',
	`prize_id` VARCHAR(255) NOT NULL COMMENT '奖品id',
  `stock` VARCHAR(255) NOT NULL COMMENT '奖品库存',
  `update_at` INT NOT NULL COMMENT '库存更新时间',
	PRIMARY KEY (`id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- answer TABLE @author aaron.luo ---------------------------------

DROP TABLE IF EXISTS `answer`;
CREATE TABLE `answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `topic_one` varchar(255) DEFAULT NULL COMMENT '问题一',
  `topic_two` varchar(255) DEFAULT NULL COMMENT '问题二',
  `topic_three` varchar(255) DEFAULT NULL COMMENT '问题三',
  `topic_fout` varchar(255) DEFAULT NULL COMMENT '问题四',
  `brands` varchar(100)  DEFAULT NULL COMMENT '匹配的品牌'
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- #--------------------------------- token TABLE @author chris.kuang ---------------------------------
DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `access_token` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'API 请求令牌',
  `refresh_token` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'API 请求令牌刷新令牌',
  `alternate_token` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '备用access_token',
  `access_expires_at` int(10) unsigned NOT NULL COMMENT 'access_token 过期时间',
  `alternate_expires_at` int(10) unsigned NOT NULL COMMENT 'alternate_token 过期时间',
  `expires_in` int(10) unsigned NOT NULL COMMENT 'access_token 有效时长',
  `create_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_at` int(10) unsigned NOT NULL COMMENT '刷新access_token 时间',
  `username` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '请求令牌的username',
  `password` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '请求令牌的password',
  `account_id` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '请求令牌的account_id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

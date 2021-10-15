SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `fa_api_category`;
CREATE TABLE `fa_api_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '父级ID',
  `name` varchar(15) NOT NULL COMMENT '分类名称',
  `ord` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='API分类';

INSERT INTO `fa_api_category` (`id`, `pid`, `name`, `ord`, `createtime`, `updatetime`) VALUES ('1', '0', 'KKAPI', '0', '1612617120', '1612617120');
INSERT INTO `fa_api_category` (`id`, `pid`, `name`, `ord`, `createtime`, `updatetime`) VALUES ('2', '1', 'API01', '0', '1612617220', '1612926639');
INSERT INTO `fa_api_category` (`id`, `pid`, `name`, `ord`, `createtime`, `updatetime`) VALUES ('3', '1', 'API02', '0', '1612617320', '1612617320');
INSERT INTO `fa_api_category` (`id`, `pid`, `name`, `ord`, `createtime`, `updatetime`) VALUES ('4', '1', 'API03', '0', '1612617420', '1612839829');


DROP TABLE IF EXISTS `fa_api_resource`;
CREATE TABLE `fa_api_resource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `projectid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '项目ID',
  `cateid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类别ID',
  `apiname` varchar(100) NOT NULL DEFAULT '' COMMENT 'API名称',
  `apiuri` varchar(500) NOT NULL DEFAULT '' COMMENT 'API地址',
  `reqmethod` varchar(20) NOT NULL DEFAULT '' COMMENT '请求方式',
  `rawdata` mediumtext CHARACTER SET utf8mb4 COMMENT 'JSON数据',
  `respraw` mediumtext CHARACTER SET utf8mb4 COMMENT '响应内容',
  `docbody` mediumtext CHARACTER SET utf8mb4 COMMENT '文档内容',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `reqscheme` varchar(255) DEFAULT NULL,
  `bodytype` varchar(255) DEFAULT NULL,
  `bodyrawtype` varchar(255) DEFAULT NULL,
  `rheader_chk` tinyint(1) DEFAULT NULL,
  `rbody_chk` tinyint(1) DEFAULT NULL,
  `rheader` varchar(255) DEFAULT NULL,
  `rbody` varchar(255) DEFAULT NULL,
  `extra` text COMMENT '扩展信息',
  PRIMARY KEY (`id`),
  KEY `apiname` (`apiname`),
  KEY `apiuri` (`apiuri`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='API资源表';

/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-07-04 16:44:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_source`
-- ----------------------------
DROP TABLE IF EXISTS `org_source`;
CREATE TABLE `org_source` (
  `source_id` int(5) NOT NULL AUTO_INCREMENT,
  `source_code` varchar(10) CHARACTER SET latin1 NOT NULL,
  `source_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`source_id`),
  KEY `source_code` (`source_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='Level 0\r\nEx: Source Company';

-- ----------------------------
-- Records of org_source
-- ----------------------------
INSERT INTO `org_source` VALUES ('87', 'HEL-001', 'HELA Casual', '1', '2018-06-08 07:10:27', '1', '2018-06-28 06:39:25', '2');
INSERT INTO `org_source` VALUES ('99', 'HEL-002', 'Hela Intimates', '1', '2018-06-25 07:17:35', '1', '2018-07-03 04:55:56', '2');
INSERT INTO `org_source` VALUES ('100', 'HEL-003', 'Hela Intimates', '0', '2018-06-28 10:11:38', '1', '2018-06-29 04:10:13', '2');
INSERT INTO `org_source` VALUES ('101', 'HEL-005', 'aaaa', '0', '2018-06-29 11:05:50', '1', '2018-06-29 11:05:54', '2');
INSERT INTO `org_source` VALUES ('102', 'HEL-0016', '123456789', '0', '2018-06-29 11:07:48', '1', '2018-06-29 11:13:47', '2');

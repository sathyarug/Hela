/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-07-04 16:44:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_group`
-- ----------------------------
DROP TABLE IF EXISTS `org_group`;
CREATE TABLE `org_group` (
  `group_id` int(5) NOT NULL AUTO_INCREMENT,
  `group_code` varchar(10) CHARACTER SET latin1 NOT NULL,
  `source_id` int(10) NOT NULL,
  `group_name` varchar(255) CHARACTER SET armscii8 DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`group_id`),
  KEY `fk_org_group_org_source` (`source_id`) USING BTREE,
  CONSTRAINT `fk_org_group_org_source` FOREIGN KEY (`source_id`) REFERENCES `org_source` (`source_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='Ex: Casual/Intimates\r\nLevel 1';

-- ----------------------------
-- Records of org_group
-- ----------------------------
INSERT INTO `org_group` VALUES ('1', 'INT-001', '99', 'Hela Intimates', '1', '2018-06-08 07:10:27', '1', '2018-07-04 04:07:59', null);
INSERT INTO `org_group` VALUES ('2', 'INT-002', '99', 'Hela Casual', '1', '2018-06-29 04:05:29', '1', '2018-07-04 04:07:40', '2');
INSERT INTO `org_group` VALUES ('3', 'INT-003', '87', 'cccc', '0', '2018-06-29 04:08:00', '1', '2018-06-29 06:14:36', '2');
INSERT INTO `org_group` VALUES ('4', 'INT-004', '87', '12345', '0', '2018-06-29 04:09:42', '1', '2018-06-29 04:49:44', '2');
INSERT INTO `org_group` VALUES ('5', 'INT-005', '102', 'qwert', '0', '2018-06-29 11:09:46', '1', '2018-06-29 11:13:39', '2');

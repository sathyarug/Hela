/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-07-05 08:59:14
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_origin_type`
-- ----------------------------
DROP TABLE IF EXISTS `org_origin_type`;
CREATE TABLE `org_origin_type` (
  `origin_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `origin_type` varchar(50) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_date` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`origin_type_id`),
  KEY `company_code` (`origin_type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of org_origin_type
-- ----------------------------
INSERT INTO `org_origin_type` VALUES ('1', 'LOCAL', '1', '2018-07-04 11:39:57', '1', '2018-07-04 11:39:57', '2');
INSERT INTO `org_origin_type` VALUES ('2', 'EXPORT', '1', '2018-07-04 11:40:28', '1', '2018-07-04 11:40:28', '2');
INSERT INTO `org_origin_type` VALUES ('3', 'IMPORT', '0', '2018-07-04 11:42:04', '1', '2018-07-04 11:43:04', '2');

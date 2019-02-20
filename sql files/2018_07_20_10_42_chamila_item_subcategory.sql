/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-07-20 10:42:39
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `item_subcategory`
-- ----------------------------
DROP TABLE IF EXISTS `item_subcategory`;
CREATE TABLE `item_subcategory` (
  `subcategory_id` int(11) NOT NULL AUTO_INCREMENT,
  `subcategory_code` varchar(10) CHARACTER SET latin1 NOT NULL,
  `category_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subcategory_name` varchar(150) CHARACTER SET latin1 NOT NULL,
  `is_inspectiion_allowed` tinyint(1) DEFAULT NULL,
  `is_display` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_date` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`subcategory_id`),
  KEY `loc_code` (`subcategory_code`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='Main Cateory\r\n=> Fabric \r\n\r\nSub caterory would be => single jersey\r\n';

-- ----------------------------
-- Records of item_subcategory
-- ----------------------------
INSERT INTO `item_subcategory` VALUES ('11', 'sub cat 01', 'FAB', 'test sub cat 01', '1', '1', '1', '2018-07-11 04:08:30', '1', '2018-07-11 04:08:30', '2');
INSERT INTO `item_subcategory` VALUES ('12', 'sub cat 02', 'PAC', 'test sub cat 02', '0', '1', '1', '2018-07-11 04:09:25', '1', '2018-07-11 04:09:25', '2');
INSERT INTO `item_subcategory` VALUES ('13', 'sub cat 03', 'TRM', 'test sub cat 03', '1', '0', '1', '2018-07-11 04:09:55', '1', '2018-07-11 04:09:55', '2');
INSERT INTO `item_subcategory` VALUES ('14', 'sub cat 04', 'PAC', 'test sub category 044', '1', '0', '0', '2018-07-11 04:22:29', '1', '2018-07-11 04:39:05', '2');
INSERT INTO `item_subcategory` VALUES ('15', 'erer', 'FAB', 'erer', null, '1', '1', '2018-07-11 09:17:55', '1', '2018-07-11 09:17:55', '2');
INSERT INTO `item_subcategory` VALUES ('16', 'sub cat 06', 'TRM', 'sample category', '1', '1', '1', '2018-07-11 09:19:05', '1', '2018-07-11 09:19:05', '2');
INSERT INTO `item_subcategory` VALUES ('17', 'dfdfdf2323', 'FAB', 'fdfdf23', '1', '1', '1', '2018-07-11 10:20:50', '1', '2018-07-11 10:20:50', '2');
INSERT INTO `item_subcategory` VALUES ('18', 'fdfdf', 'PAC', 'dfdf', null, '1', '1', '2018-07-11 11:35:25', '1', '2018-07-11 11:35:25', '2');
INSERT INTO `item_subcategory` VALUES ('19', 'dssdsd', 'TRM', 'sdsd', '0', '0', '0', '2018-07-20 04:09:01', '1', '2018-07-20 04:10:33', '2');

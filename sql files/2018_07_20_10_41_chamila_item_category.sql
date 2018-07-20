/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-07-20 10:42:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `item_category`
-- ----------------------------
DROP TABLE IF EXISTS `item_category`;
CREATE TABLE `item_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_code` varchar(10) CHARACTER SET latin1 NOT NULL,
  `category_name` varchar(150) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `created_date` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `loc_code` (`category_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of item_category
-- ----------------------------
INSERT INTO `item_category` VALUES ('1', 'FAB', 'FABRIC', '1', '2018-07-10 16:50:14', '1', '2018-07-10 16:50:32', '1');
INSERT INTO `item_category` VALUES ('2', 'PAC', 'PACKING', '1', '2018-07-10 16:50:18', '1', '2018-07-10 16:50:36', '1');
INSERT INTO `item_category` VALUES ('3', 'TRM', 'TRIMS', '1', '2018-07-10 16:50:22', '1', '2018-07-10 16:50:42', '1');

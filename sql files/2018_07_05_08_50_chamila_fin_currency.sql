/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-07-05 08:56:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `fin_currency`
-- ----------------------------
DROP TABLE IF EXISTS `fin_currency`;
CREATE TABLE `fin_currency` (
  `currency_id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_code` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `currency_description` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`currency_id`),
  KEY `currency_code` (`currency_code`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of fin_currency
-- ----------------------------
INSERT INTO `fin_currency` VALUES ('98', 'AAA', 'aaaaaaa', '2018-06-13 04:38:49', '1', '2018-06-13 04:38:49', '2');
INSERT INTO `fin_currency` VALUES ('99', 'BBB1', 'bbbbb1', '2018-06-13 04:40:42', '1', '2018-06-13 04:49:35', '2');
INSERT INTO `fin_currency` VALUES ('100', 'cccc23423', 'ccccccc2323', '2018-06-13 04:41:27', '1', '2018-06-13 04:49:45', '2');
INSERT INTO `fin_currency` VALUES ('101', 'sds', 'sds', '2018-06-13 04:50:28', '1', '2018-06-13 04:50:28', '2');
INSERT INTO `fin_currency` VALUES ('102', 'wwewe', 'wewew', '2018-06-19 06:11:02', '1', '2018-06-19 06:11:02', '2');
INSERT INTO `fin_currency` VALUES ('103', 'sds', 'sds', '2018-06-19 06:23:00', '1', '2018-06-19 06:23:00', '2');
INSERT INTO `fin_currency` VALUES ('104', 'sdsd', 'sdsd', '2018-06-19 07:27:59', '1', '2018-06-19 07:27:59', '2');

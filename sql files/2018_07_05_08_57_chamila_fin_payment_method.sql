/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-07-05 08:57:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `fin_payment_method`
-- ----------------------------
DROP TABLE IF EXISTS `fin_payment_method`;
CREATE TABLE `fin_payment_method` (
  `payment_method_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method_code` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `payment_method_description` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`payment_method_id`),
  KEY `payment_code` (`payment_method_code`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of fin_payment_method
-- ----------------------------
INSERT INTO `fin_payment_method` VALUES ('13', 'dsd', 'Test 3', '2018-07-02 11:17:23', '1', '2018-07-02 11:21:08', '2', '0');
INSERT INTO `fin_payment_method` VALUES ('14', 'PM001', 'test', '2018-07-02 11:18:01', '1', '2018-07-02 11:40:17', '2', '0');
INSERT INTO `fin_payment_method` VALUES ('15', 'PM002', 'test 2qwqwq', '2018-07-02 11:19:10', '1', '2018-07-02 11:40:30', '2', '1');
INSERT INTO `fin_payment_method` VALUES ('16', 'PM003', 'Test 3', '2018-07-02 11:28:02', '1', '2018-07-02 11:28:02', '2', '1');
INSERT INTO `fin_payment_method` VALUES ('17', 'asas', 'asas', '2018-07-02 11:40:24', '1', '2018-07-02 11:40:24', '2', '1');

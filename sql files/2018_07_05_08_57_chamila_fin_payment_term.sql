/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-07-05 08:58:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `fin_payment_term`
-- ----------------------------
DROP TABLE IF EXISTS `fin_payment_term`;
CREATE TABLE `fin_payment_term` (
  `payment_term_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_code` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `payment_description` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`payment_term_id`),
  KEY `payment_code` (`payment_code`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of fin_payment_term
-- ----------------------------
INSERT INTO `fin_payment_term` VALUES ('10', 'PT1', 'test 1sd11', '2018-07-02 04:19:42', '1', '2018-07-02 06:32:03', '2', '0');
INSERT INTO `fin_payment_term` VALUES ('11', 'PT2', 'test22', '2018-07-02 04:20:00', '1', '2018-07-02 11:30:15', '2', '0');
INSERT INTO `fin_payment_term` VALUES ('12', 'PT3', 'test term', '2018-07-02 06:32:49', '1', '2018-07-02 11:38:06', '2', '0');
INSERT INTO `fin_payment_term` VALUES ('13', 'PT0002', 'sdsdfd4444', '2018-07-02 11:29:58', '1', '2018-07-02 11:38:16', '2', '1');
INSERT INTO `fin_payment_term` VALUES ('14', 'PT0003', 'ssdsd', '2018-07-02 11:32:14', '1', '2018-07-02 11:32:14', '2', '1');

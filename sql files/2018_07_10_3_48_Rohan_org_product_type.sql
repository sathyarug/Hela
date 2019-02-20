/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50542
Source Host           : localhost:3306
Source Database       : surfacedev

Target Server Type    : MYSQL
Target Server Version : 50542
File Encoding         : 65001

Date: 2018-07-10 15:49:32
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_product_type`
-- ----------------------------
DROP TABLE IF EXISTS `org_product_type`;
CREATE TABLE `org_product_type` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_code` varchar(10) DEFAULT NULL,
  `product_description` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of org_product_type
-- ----------------------------
INSERT INTO `org_product_type` VALUES ('1', 'P1', 'product 1', '2018-07-10 04:17:41', '1', '2018-07-10 04:17:41', '2', '1');
INSERT INTO `org_product_type` VALUES ('2', 'P2', 'product 1', '2018-07-10 04:18:57', '1', '2018-07-10 04:19:20', '2', '0');

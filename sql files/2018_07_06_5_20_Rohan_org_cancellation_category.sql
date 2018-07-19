/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50542
Source Host           : localhost:3306
Source Database       : surfacedev

Target Server Type    : MYSQL
Target Server Version : 50542
File Encoding         : 65001

Date: 2018-07-06 17:20:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_cancellation_category`
-- ----------------------------
DROP TABLE IF EXISTS `org_cancellation_category`;
CREATE TABLE `org_cancellation_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_code` varchar(10) DEFAULT NULL,
  `category_description` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of org_cancellation_category
-- ----------------------------
INSERT INTO `org_cancellation_category` VALUES ('1', 'TRM', 'Trim Items', '1', '2018-07-06 05:15:57', '1', '2018-07-06 05:16:33', '2');
INSERT INTO `org_cancellation_category` VALUES ('2', 'FAB', 'Fabric', '1', '2018-07-06 06:04:21', '1', '2018-07-06 06:04:21', '2');
INSERT INTO `org_cancellation_category` VALUES ('3', 'GMT', 'Garments', '0', '2018-07-06 06:04:44', '1', '2018-07-06 06:27:08', '2');
INSERT INTO `org_cancellation_category` VALUES ('4', 'WSH', 'Washing', '0', '2018-07-06 06:05:10', '1', '2018-07-06 09:49:53', '2');

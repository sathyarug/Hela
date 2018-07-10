/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50542
Source Host           : localhost:3306
Source Database       : surfacedev

Target Server Type    : MYSQL
Target Server Version : 50542
File Encoding         : 65001

Date: 2018-07-06 17:20:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_cancellation_reason`
-- ----------------------------
DROP TABLE IF EXISTS `org_cancellation_reason`;
CREATE TABLE `org_cancellation_reason` (
  `reason_id` int(11) NOT NULL AUTO_INCREMENT,
  `reason_code` varchar(10) DEFAULT NULL,
  `reason_description` varchar(100) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`reason_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of org_cancellation_reason
-- ----------------------------
INSERT INTO `org_cancellation_reason` VALUES ('1', 'WCOOD', 'Wrong Coordinator', '0', '2018-07-06 09:49:16', '1', '2018-07-06 09:50:04', '2');
INSERT INTO `org_cancellation_reason` VALUES ('2', 'CUSCANW', 'Customer Cancel with out Liability', '1', '2018-07-06 09:50:21', '1', '2018-07-06 09:50:21', '2');
INSERT INTO `org_cancellation_reason` VALUES ('3', 'CUNCANL', 'Customer Cancel with Liability', '0', '2018-07-06 09:50:35', '1', '2018-07-06 09:51:01', '2');
INSERT INTO `org_cancellation_reason` VALUES ('4', 'WIREF', 'Wrong item Reference', '1', '2018-07-06 09:50:50', '1', '2018-07-06 09:50:50', '2');
INSERT INTO `org_cancellation_reason` VALUES ('5', 'ITEMCODE', 'Wrong Item Code', '1', '2018-07-06 09:53:32', '1', '2018-07-06 09:53:32', '2');

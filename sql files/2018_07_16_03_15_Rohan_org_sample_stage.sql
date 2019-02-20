/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50542
Source Host           : localhost:3306
Source Database       : surfacedev

Target Server Type    : MYSQL
Target Server Version : 50542
File Encoding         : 65001

Date: 2018-07-16 15:15:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_sample_stage`
-- ----------------------------
DROP TABLE IF EXISTS `org_sample_stage`;
CREATE TABLE `org_sample_stage` (
  `sample_id` int(11) NOT NULL AUTO_INCREMENT,
  `sample_code` varchar(10) DEFAULT NULL,
  `sample_description` varchar(100) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`sample_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of org_sample_stage
-- ----------------------------
INSERT INTO `org_sample_stage` VALUES ('1', 'S1', 'Sample one', '2018-07-12 11:15:35', '1', '2018-07-12 11:19:50', '2', '0');

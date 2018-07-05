/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-07-04 16:45:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_location`
-- ----------------------------
DROP TABLE IF EXISTS `org_location`;
CREATE TABLE `org_location` (
  `loc_id` int(11) NOT NULL AUTO_INCREMENT,
  `loc_code` varchar(10) CHARACTER SET latin1 NOT NULL,
  `company_id` int(10) NOT NULL,
  `loc_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `loc_type` varchar(10) CHARACTER SET latin1 NOT NULL COMMENT 'INTERNAL/EXTERNAL',
  `loc_address_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `loc_address_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `city` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `country_code` varchar(5) CHARACTER SET latin1 DEFAULT NULL,
  `loc_phone` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `loc_fax` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `loc_email` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `loc_web` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `time_zone` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `currency_code` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`loc_id`),
  KEY `company_code` (`company_id`) USING BTREE,
  KEY `loc_code` (`loc_code`),
  KEY `currency_code` (`currency_code`),
  CONSTRAINT `fk_company_id` FOREIGN KEY (`company_id`) REFERENCES `org_company` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of org_location
-- ----------------------------
INSERT INTO `org_location` VALUES ('1', 'NAR-001', '1', 'Hela - Narammala', 'Yes', 'Narammala15', 'Narammala25', 'Narammala3', '2', '0112356456', '0112356456', 'narammala@helaclothing.com', 'www.narammalahela.com', '123', '1', '1', null, '1', '2018-07-04 10:10:42', null);
INSERT INTO `org_location` VALUES ('2', 'vvvv', '1', 'vvvv', 'Yes', 'vvvv', 'vvvv', 'vvvv', '1', '1010101010', '1234567890', 'dtx@gmail', 'www.123.com', '1234', '1', '0', '2018-07-04 09:28:38', '1', '2018-07-04 10:11:26', '2');
INSERT INTO `org_location` VALUES ('3', 'cccc', '1', 'cccc', 'Yes', 'cccc', 'cccc', 'cccc', '1', '1234567890', 'cc', 'cc@dd', 'dddd', 'dddd', '1', '0', '2018-07-04 09:29:48', '1', '2018-07-04 10:10:59', '2');

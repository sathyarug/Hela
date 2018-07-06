/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-07-04 16:44:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_company`
-- ----------------------------
DROP TABLE IF EXISTS `org_company`;
CREATE TABLE `org_company` (
  `company_id` int(5) NOT NULL AUTO_INCREMENT,
  `company_code` varchar(10) CHARACTER SET latin1 NOT NULL,
  `group_id` int(10) DEFAULT NULL,
  `company_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `company_address_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `company_address_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `city` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `country_code` varchar(5) CHARACTER SET latin1 DEFAULT NULL,
  `company_fax` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `company_contact_1` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `company_contact_2` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `company_logo` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `company_email` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `company_web` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `default_currency` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `finance_month` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT 'JAN to DES/MAR to APR',
  `status` tinyint(1) DEFAULT NULL,
  `company_remarks` text CHARACTER SET latin1,
  `vat_reg_no` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `tax_code` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `company_reg_no` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`company_id`),
  KEY `group_code` (`group_id`) USING BTREE,
  KEY `company_code` (`company_code`) USING BTREE,
  CONSTRAINT `fk_group_id` FOREIGN KEY (`group_id`) REFERENCES `org_group` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='Level 2\r\nEx: Private Ltd Company';

-- ----------------------------
-- Records of org_company
-- ----------------------------
INSERT INTO `org_company` VALUES ('1', 'FDN-001', '1', 'FDNS', 'Balapokuna', 'Colombo', 'Colombo', '1', '0112563563', '0112563563', '0112563563', 'FDN-001.jpg', 'info@helaintimates.com', 'www.helaintimates.com', '1', 'March to April', '1', 'Yes', '123S', '2525', 'REG001', '2018-06-08 07:10:27', '1', '2018-07-04 04:28:57', null);
INSERT INTO `org_company` VALUES ('2', 'aaaa', '2', 'aaaa', 'aaaa', 'aaaa', 'aaaa', '2', 'aaaa', 'aaaa', 'aaaa', null, 'aaaa', 'aaaa', '1', 'Jan to Dec', '0', 'aaaa', 'aaaa', 'aaaa', 'aaaa', '2018-07-03 10:02:48', '1', '2018-07-03 11:27:33', '2');
INSERT INTO `org_company` VALUES ('3', '1234', '1', '1234', '1234', '1234', '1234', '1', '1234', '1234', '1234', null, '1234', '1234.com', '1', 'March to April', '0', '1234', '1234', '1234', '1234', '2018-07-03 10:06:36', '1', '2018-07-04 10:11:43', '2');
INSERT INTO `org_company` VALUES ('4', 'FDN-0012', '2', '4568', '4568', '4568', '4568', '2', '4568', '4568', '4568', null, '4568sss', '4568', '1', 'March to April', '0', '4568', '4568sss', '4568', '4568', '2018-07-03 10:41:31', '1', '2018-07-04 10:11:39', '2');

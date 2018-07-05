/*
Navicat MySQL Data Transfer

Source Server         : LOCALHOST
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : surface

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2018-07-05 08:58:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `org_cost_center`
-- ----------------------------
DROP TABLE IF EXISTS `org_cost_center`;
CREATE TABLE `org_cost_center` (
  `cost_center_id` int(11) NOT NULL AUTO_INCREMENT,
  `cost_center_code` varchar(10) CHARACTER SET latin1 DEFAULT NULL,
  `loc_id` int(11) DEFAULT NULL,
  `cost_center_name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`cost_center_id`),
  KEY `fk_location_id` (`loc_id`),
  CONSTRAINT `fk_location_id` FOREIGN KEY (`loc_id`) REFERENCES `org_location` (`loc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Buyers Division\r\n==============\r\nEx: \r\nCK => \r\nMens under ware\r\n     Sleep ware \r\n   ';

-- ----------------------------
-- Records of org_cost_center
-- ----------------------------
INSERT INTO `org_cost_center` VALUES ('9', 'ffff', null, 'asasas23232', '2018-06-20 04:35:19', '1', '2018-07-02 11:40:07', '2', '0');
INSERT INTO `org_cost_center` VALUES ('10', 'ffff111', null, 'rrrrrrasas', '2018-06-20 04:35:30', '1', '2018-07-02 07:59:13', '2', '1');
INSERT INTO `org_cost_center` VALUES ('11', 'sdsd232', null, 'sdsd23', '2018-06-20 04:36:34', '1', '2018-06-20 04:36:42', '2', '1');
INSERT INTO `org_cost_center` VALUES ('12', 'sds112', null, 'sds1122', '2018-06-29 07:04:35', '1', '2018-06-29 08:52:05', '2', '1');
INSERT INTO `org_cost_center` VALUES ('13', 'CS001', null, 'test cost center 1', '2018-07-02 07:31:35', '1', '2018-07-02 07:31:35', '2', '1');
INSERT INTO `org_cost_center` VALUES ('14', 'CS002', null, 'Test 02', '2018-07-02 11:29:10', '1', '2018-07-02 11:29:10', '2', '1');
INSERT INTO `org_cost_center` VALUES ('15', 'CS0000', null, 'test', '2018-07-02 11:39:56', '1', '2018-07-02 11:39:56', '2', '1');

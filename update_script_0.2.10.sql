USE `sghis`;

DROP PROCEDURE IF EXISTS `report1_query`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `report1_query`(IN `startDate` DATE, IN `endDate` DATE)
SELECT * FROM 
	(SELECT * FROM `receipt` 
     WHERE (`receipt_content_datetime_paid` 
            BETWEEN startDate AND endDate) 
     AND (`receipt_type`='deposit') 
     GROUP BY `receipt_serial_number`, `receipt_content_datetime_paid`, `kod_akaun`) `c` 
GROUP BY kod_akaun,`receipt_serial_number`$$
DELIMITER ;
USE `sghis`;

DROP PROCEDURE IF EXISTS `receipt_bill_procedure`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `receipt_bill_procedure`(IN `rn` VARCHAR(11))
BEGIN

   SELECT receipt.rn, receipt.receipt_content_datetime_paid, receipt.receipt_content_sum, receipt.receipt_type, 	receipt.receipt_content_payment_method, receipt.receipt_content_payer_name, receipt.receipt_serial_number, receipt.receipt_content_description, receipt.receipt_responsible, receipt.receipt_uid FROM receipt WHERE receipt.rn = rn
    UNION
    SELECT bill.rn, bill.bill_generation_datetime, bill.bill_generation_billable_sum_rm, null, null, null, bill.bill_print_id, null, bill.generation_responsible_uid, null FROM bill WHERE bill.rn = rn AND bill.bill_generation_datetime is not null;

END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS `transaction_records`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `transaction_records`(IN `pid` VARCHAR(64))
BEGIN

	SELECT receipt.rn, receipt.receipt_content_datetime_paid, receipt.receipt_content_sum, receipt.receipt_type, 	receipt.receipt_content_payment_method, receipt.receipt_content_payer_name, receipt.receipt_serial_number, receipt.receipt_content_description, receipt.receipt_responsible,  receipt.receipt_uid
	FROM receipt
	INNER JOIN patient_admission 
    ON patient_admission.rn = receipt.rn
    INNER JOIN patient_information
    ON patient_information.patient_uid = patient_admission.patient_uid
    WHERE patient_information.patient_uid = pid
    
    UNION
     
    SELECT bill.rn, bill.bill_generation_datetime, bill.bill_generation_billable_sum_rm, null, null, null, bill.bill_print_id, null, bill.generation_responsible_uid, null
    FROM bill 
    INNER JOIN patient_admission 
    ON patient_admission.rn = bill.rn
    INNER JOIN patient_information
    ON patient_information.patient_uid = patient_admission.patient_uid
    WHERE patient_information.patient_uid = pid 
    AND bill.bill_generation_datetime is not null;
    
 
END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS `report5_query`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `report5_query`(IN `year` INT, IN `month` INT)
SELECT COUNT(`rn`) AS `rn`, SUM(`bill_generation_billable_sum_rm`) AS `bill_generation_billable_sum_rm`, 
    SUM(`receipt_content_sum`) AS `receipt_content_sum`
FROM
(   
    SELECT `bill`.`rn`, `bill_generation_billable_sum_rm`, SUM(`receipt_content_sum`) AS `receipt_content_sum`,
        `receipt_content_datetime_paid` 
    FROM `receipt` LEFT JOIN `bill` ON `receipt`.`rn` = `bill`.`rn` 
    LEFT JOIN `cancellation` ON `receipt`.`receipt_uid` = `cancellation`.`cancellation_uid` 
    WHERE (`receipt_type`='exception') AND (`department_code` IN 
    (
        SELECT `department_code` FROM `bill` WHERE (`deleted`=0) AND 
        ((`department_code` IN ('OA', 'BS')) AND (`department_code` != ''))
        AND (NOT (`bill_generation_datetime` IS NULL))
    )) 
    AND (`cancellation_uid` IS NULL) AND (`bill`.`deleted`=0) 
    AND (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= year) 
    AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= month)
    GROUP BY `bill`.`rn`
) `full_exception` 
WHERE `bill_generation_billable_sum_rm` = `receipt_content_sum`

UNION ALL

SELECT COUNT(`rn`), SUM(`bill_generation_billable_sum_rm`) AS `bill_generation_billable_sum_rm`,
        SUM(`receipt_content_sum`) AS `receipt_content_sum`
FROM 
(
    SELECT `bill`.`rn`, `bill_generation_billable_sum_rm`, SUM(`receipt_content_sum`) AS `receipt_content_sum`,
        `receipt_content_datetime_paid` 
    FROM `receipt` LEFT JOIN `bill` ON `receipt`.`rn` = `bill`.`rn` 
    LEFT JOIN `cancellation` ON `receipt`.`receipt_uid` = `cancellation`.`cancellation_uid` 
    WHERE (`receipt_type`='exception') AND (`department_code` IN 
    (
        SELECT `department_code` FROM `bill` 
        WHERE (`deleted`=0) AND ((`department_code` IN ('OA', 'BS')) AND (`department_code` != ''))
        AND (NOT (`bill_generation_datetime` IS NULL))
    ))
    AND (`cancellation_uid` IS NULL) AND (`bill`.`deleted`=0) 
    AND (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= year) 
    AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= month)
    GROUP BY `bill`.`rn`
) `partial_exception` 
WHERE `receipt_content_sum` < `bill_generation_billable_sum_rm`

UNION ALL

SELECT COUNT(`rn`), SUM(`bill_generation_billable_sum_rm`) AS `bill_generation_billable_sum_rm`,
    SUM(`receipt_content_sum`) AS `receipt_content_sum`
FROM 
(
    SELECT `bill`.`rn`, `bill_generation_billable_sum_rm`, SUM(`receipt_content_sum`) AS `receipt_content_sum`,
        `receipt_content_datetime_paid` 
    FROM `receipt` LEFT JOIN `bill` ON `receipt`.`rn` = `bill`.`rn` 
    LEFT JOIN `cancellation` ON `receipt`.`receipt_uid` = `cancellation`.`cancellation_uid` 
    WHERE (`receipt_type`='exception') AND (`department_code` IN 
    (
        SELECT `department_code` FROM `bill` 
        WHERE (`deleted`=0) AND ((NOT (`department_code` IN ('OA', 'BS')))
        AND (`department_code` != '')) 
        AND (NOT (`bill_generation_datetime` IS NULL))
    ))
    AND (`cancellation_uid` IS NULL) AND (`bill`.`deleted`=0)
    AND (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= year) 
    AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= month)
    GROUP BY `bill`.`rn`
) `government_servant`

UNION ALL

SELECT SUM(`rn`) AS `rn`, SUM(`bill_generation_billable_sum_rm`) AS `bill_generation_billable_sum_rm`,
    SUM(`receipt_content_sum`) AS `receipt_content_sum`
FROM
(
    SELECT COUNT(`rn`) AS `rn`, SUM(`bill_generation_billable_sum_rm`) AS `bill_generation_billable_sum_rm`, 
    SUM(`receipt_content_sum`) AS `receipt_content_sum`, `receipt_content_datetime_paid` 
    FROM
    (   
        SELECT `bill`.`rn`, `bill_generation_billable_sum_rm`, SUM(`receipt_content_sum`) AS `receipt_content_sum`,
            `receipt_content_datetime_paid` 
        FROM `receipt` LEFT JOIN `bill` ON `receipt`.`rn` = `bill`.`rn` 
        LEFT JOIN `cancellation` ON `receipt`.`receipt_uid` = `cancellation`.`cancellation_uid` 
        WHERE (`receipt_type`='exception') AND (`department_code` IN 
        (
            SELECT `department_code` FROM `bill` WHERE (`deleted`=0) AND 
            ((`department_code` IN ('OA', 'BS')) AND (`department_code` != ''))
            AND (NOT (`bill_generation_datetime` IS NULL))
        )) 
        AND (`cancellation_uid` IS NULL) AND (`bill`.`deleted`=0) 
        AND (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= year) 
        AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= month)
        GROUP BY `bill`.`rn`
    ) `full_exception` 
    WHERE `bill_generation_billable_sum_rm` = `receipt_content_sum`

    UNION ALL

    SELECT COUNT(`rn`), SUM(`bill_generation_billable_sum_rm`) AS `bill_generation_billable_sum_rm`,
            SUM(`receipt_content_sum`) AS `receipt_content_sum`, `receipt_content_datetime_paid` 
    FROM 
    (
        SELECT `bill`.`rn`, `bill_generation_billable_sum_rm`, SUM(`receipt_content_sum`) AS `receipt_content_sum`,
            `receipt_content_datetime_paid` 
        FROM `receipt` LEFT JOIN `bill` ON `receipt`.`rn` = `bill`.`rn` 
        LEFT JOIN `cancellation` ON `receipt`.`receipt_uid` = `cancellation`.`cancellation_uid` 
        WHERE (`receipt_type`='exception') AND (`department_code` IN 
        (
            SELECT `department_code` FROM `bill` 
            WHERE (`deleted`=0) AND ((`department_code` IN ('OA', 'BS')) AND (`department_code` != ''))
            AND (NOT (`bill_generation_datetime` IS NULL))
        ))
        AND (`cancellation_uid` IS NULL) AND (`bill`.`deleted`=0) 
        AND (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= year) 
        AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= month)
        GROUP BY `bill`.`rn`
    ) `partial_exception` 
    WHERE `receipt_content_sum` < `bill_generation_billable_sum_rm`

    UNION ALL

    SELECT COUNT(`rn`), SUM(`bill_generation_billable_sum_rm`) AS `bill_generation_billable_sum_rm`,
        SUM(`receipt_content_sum`) AS `receipt_content_sum`, `receipt_content_datetime_paid` 
    FROM 
    (
        SELECT `bill`.`rn`, `bill_generation_billable_sum_rm`, SUM(`receipt_content_sum`) AS `receipt_content_sum`,
            `receipt_content_datetime_paid` 
        FROM `receipt` LEFT JOIN `bill` ON `receipt`.`rn` = `bill`.`rn` 
        LEFT JOIN `cancellation` ON `receipt`.`receipt_uid` = `cancellation`.`cancellation_uid` 
        WHERE (`receipt_type`='exception') AND (`department_code` IN 
        (
            SELECT `department_code` FROM `bill` 
            WHERE (`deleted`=0) AND ((NOT (`department_code` IN ('OA', 'BS')))
            AND (`department_code` != '')) 
            AND (NOT (`bill_generation_datetime` IS NULL))
        ))
        AND (`cancellation_uid` IS NULL) AND (`bill`.`deleted`=0)
        AND (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= year) 
        AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= month)
        GROUP BY `bill`.`rn`
    ) `government_servant`
) `total`$$
DELIMITER ;



DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `report8_query`(IN `year` INT, IN `month` INT)
SELECT `kod_akaun`,SUM(`receipt_content_sum`) AS `total_receipt_content_sum`, `receipt_content_payment_method`,COUNT(`receipt_uid`) AS `no_receipt`
FROM `receipt`
WHERE (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= 2022) 
    AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= 10)
    AND (`receipt_uid` NOT IN (SELECT `cancellation_uid` FROM `cancellation`))
	AND `receipt_content_payment_method` = 'cash'
    
UNION ALL

SELECT `kod_akaun`,SUM(`receipt_content_sum`) AS `total_receipt_content_sum`, `receipt_content_payment_method`,COUNT(`receipt_uid`) AS `no_receipt`
FROM `receipt`
WHERE (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= 2022) 
    AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= 10)
    AND (`receipt_uid` NOT IN (SELECT `cancellation_uid` FROM `cancellation`))
	AND `receipt_content_payment_method` = 'card'
    
UNION ALL

SELECT `kod_akaun`,SUM(`receipt_content_sum`) AS `total_receipt_content_sum`, `receipt_content_payment_method`,COUNT(`receipt_uid`) AS `no_receipt`
FROM `receipt`
WHERE (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= 2022) 
    AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= 10)
    AND (`receipt_uid` NOT IN (SELECT `cancellation_uid` FROM `cancellation`))
	AND `receipt_content_payment_method` = 'cheque'
    
    
UNION ALL

SELECT `kod_akaun`,SUM(`receipt_content_sum`) AS `total_receipt_content_sum`, `receipt_content_payment_method`,COUNT(`receipt_uid`) AS `no_receipt`
FROM `receipt`
WHERE (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= 2022) 
    AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= 10)
    AND (`receipt_uid` NOT IN (SELECT `cancellation_uid` FROM `cancellation`))
	AND `receipt_content_payment_method` = 'WANG POS'

UNION ALL

SELECT `kod_akaun`,SUM(`receipt_content_sum`) AS `total_receipt_content_sum`, `receipt_content_payment_method`,COUNT(`receipt_uid`) AS `no_receipt`
FROM `receipt`
WHERE (EXTRACT(YEAR FROM `receipt_content_datetime_paid`)= 2022) 
    AND (EXTRACT(MONTH FROM `receipt_content_datetime_paid`)= 10)
    AND (`receipt_uid` NOT IN (SELECT `cancellation_uid` FROM `cancellation`))
	AND `receipt_content_payment_method` = 'RESIT BATAL'$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` EVENT `delete_nric_rn` ON SCHEDULE EVERY 1 WEEK STARTS '2022-10-19 19:36:26' ON COMPLETION NOT PRESERVE ENABLE DO DELETE A
FROM
  patient_information A
  LEFT JOIN patient_admission b ON A.patient_uid = b.patient_uid
WHERE
  b.rn IS NULL AND (A.nric IS NULL OR A.nric = ' ')
DELIMITER ;


INSERT INTO `lookup_general` (`lookup_general_uid`, `code`, `category`, `name`, `long_description`, `recommend`) VALUES ('BP541YvaOmltox8t_u2gY5THA7Xw35CY', '018', 'Collection Center', 'Admission', '', '1');

INSERT INTO `lookup_general` (`lookup_general_uid`, `code`, `category`, `name`, `long_description`, `recommend`) VALUES ('KUGjTuuIi-eGERoEibtnmVfiHUelWLXA', '003', 'Collection Center', 'Outstation', '', '1');
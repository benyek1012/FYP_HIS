USE `sghis`;

DROP PROCEDURE IF EXISTS `reminder_batch_select`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `reminder_batch_select`(IN `MIN_DATE` DATE, IN `MAX_DATE` DATE, IN `responsible_uid_` VARCHAR(64))
BEGIN

SET @r1MIN_DATE = DATE_ADD(MIN_DATE, INTERVAL -14 DAY);
SET @r2MIN_DATE = DATE_ADD(MIN_DATE, INTERVAL -28 DAY);
SET @r3MIN_DATE = DATE_ADD(MIN_DATE, INTERVAL -42 DAY);
SET @r1MAX_DATE = DATE_ADD(MAX_DATE, INTERVAL -14 DAY);
SET @r2MAX_DATE = DATE_ADD(MAX_DATE, INTERVAL -28 DAY);
SET @r3MAX_DATE = DATE_ADD(MAX_DATE, INTERVAL -42 DAY);

UPDATE patient_admission SET reminder1 = CAST('9999-12-31' AS DATE) WHERE patient_admission.rn IN (		   
    SELECT _relevantBills.rn
		FROM 
			((SELECT rn, CONVERT(bill.discharge_date,DATE) AS discharge_date, bill_generation_billable_sum_rm 						
				FROM bill 
				WHERE (bill.deleted = 0 AND !ISNULL(bill.bill_generation_datetime)) 
				AND (CONVERT(bill.bill_generation_datetime,DATE) >= @r1MIN_DATE AND CONVERT(bill.bill_generation_datetime,DATE) <= @r1MAX_DATE)   	
				)AS _relevantBills)
			LEFT OUTER JOIN
			((SELECT rn, SUM(CASE WHEN receipt.receipt_type = 'refund' THEN - receipt_content_sum ELSE receipt_content_sum END) AS receipt_sum 
				FROM receipt 
				WHERE receipt.receipt_uid NOT IN (SELECT cancellation_uid FROM cancellation) 
					AND (CONVERT(receipt_content_datetime_paid,DATE) <= @r1MAX_DATE)													
				GROUP BY rn)
   				 AS _payments)
    	ON _relevantBills.rn = _payments.rn
		WHERE (-_relevantBills.bill_generation_billable_sum_rm + COALESCE(_payments.receipt_sum,0)) <0);

UPDATE patient_admission SET reminder2 = CAST('9999-12-31' AS DATE) WHERE patient_admission.rn IN (		   
    SELECT _relevantBills.rn
		FROM 
			((SELECT rn, CONVERT(bill.discharge_date,DATE) AS discharge_date, bill_generation_billable_sum_rm 						
				FROM bill 
				WHERE (bill.deleted = 0 AND !ISNULL(bill.bill_generation_datetime)) 
				AND (CONVERT(bill.bill_generation_datetime,DATE) >= @r2MIN_DATE AND CONVERT(bill.bill_generation_datetime,DATE) <= @r2MAX_DATE)   	
				)AS _relevantBills)
			LEFT OUTER JOIN
			((SELECT rn, SUM(CASE WHEN receipt.receipt_type = 'refund' THEN - receipt_content_sum ELSE receipt_content_sum END) AS receipt_sum 
				FROM receipt 
				WHERE receipt.receipt_uid NOT IN (SELECT cancellation_uid FROM cancellation) 
					AND (CONVERT(receipt_content_datetime_paid,DATE) <= @r2MAX_DATE)													
				GROUP BY rn)
   				 AS _payments)
    	ON _relevantBills.rn = _payments.rn
		WHERE (-_relevantBills.bill_generation_billable_sum_rm + COALESCE(_payments.receipt_sum,0)) <0);
        
UPDATE patient_admission SET reminder3 = CAST('9999-12-31' AS DATE) WHERE patient_admission.rn IN (		   
    SELECT _relevantBills.rn
		FROM 
			((SELECT rn, CONVERT(bill.discharge_date,DATE) AS discharge_date, bill_generation_billable_sum_rm 						
				FROM bill 
				WHERE (bill.deleted = 0 AND !ISNULL(bill.bill_generation_datetime)) 
				AND (CONVERT(bill.bill_generation_datetime,DATE) >= @r3MIN_DATE AND CONVERT(bill.bill_generation_datetime,DATE) <= @r3MAX_DATE)   	
				)AS _relevantBills)
			LEFT OUTER JOIN
			((SELECT rn, SUM(CASE WHEN receipt.receipt_type = 'refund' THEN - receipt_content_sum ELSE receipt_content_sum END) AS receipt_sum 
				FROM receipt 
				WHERE receipt.receipt_uid NOT IN (SELECT cancellation_uid FROM cancellation) 
					AND (CONVERT(receipt_content_datetime_paid,DATE) <= @r3MAX_DATE)													
				GROUP BY rn)
   				 AS _payments)
    	ON _relevantBills.rn = _payments.rn
		WHERE (-_relevantBills.bill_generation_billable_sum_rm + COALESCE(_payments.receipt_sum,0)) <0);        
        

set @count1 = (SELECT COUNT(reminder1) FROM patient_admission WHERE reminder1 = CAST('9999-12-31' AS DATE));
SET @count2 = (SELECT COUNT(reminder2) FROM patient_admission WHERE reminder2 = CAST('9999-12-31' AS DATE));
SET @count3 = (SELECT COUNT(reminder3) FROM patient_admission WHERE reminder3 = CAST('9999-12-31' AS DATE));

UPDATE reminder_letter SET reminder1count = @count1, reminder2count = @count2, reminder3count = @count3, responsible_uid = responsible_uid_ WHERE batch_date = CAST('9999-12-31' AS DATE);

END$$
DELIMITER ;


DROP PROCEDURE IF EXISTS `reminder_select_number`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `reminder_select_number`(IN `batch_date` VARCHAR(16))
BEGIN

SELECT rn, nric, race, address1, address2, address3, guarantor_nric,  guarantor_address1,guarantor_address2,guarantor_address3, entry_datetime, reminder_no AS 'Reminder Number', reminder_batch_date AS 'Batch date', discharge_date AS 'Discharge Date', bill_generation_billable_sum_rm AS 'Billable Fee'
FROM (
SELECT bill.rn, patient_information.nric, patient_information.race, patient_information.address1, patient_information.address2, patient_information.address3, bill.guarantor_nric, bill.guarantor_address1,bill.guarantor_address2,bill.guarantor_address3, patient_admission.entry_datetime, 'reminder1' reminder_no, reminder1 reminder_batch_date, bill.discharge_date, bill.deleted, bill.bill_generation_billable_sum_rm
    FROM patient_admission 
	LEFT JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
	LEFT JOIN bill ON patient_admission.rn = bill.rn
    WHERE bill.bill_generation_datetime is not null
    union all 
    
SELECT bill.rn, patient_information.nric, patient_information.race, patient_information.address1, patient_information.address2, patient_information.address3, bill.guarantor_nric, bill.guarantor_address1,bill.guarantor_address2,bill.guarantor_address3, patient_admission.entry_datetime, 'reminder2' reminder_no, reminder2 reminder_batch_date, bill.discharge_date, bill.deleted, bill.bill_generation_billable_sum_rm
    FROM patient_admission
	LEFT JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
	LEFT JOIN bill ON patient_admission.rn = bill.rn
    WHERE bill.bill_generation_datetime is not null
	union all 
    
SELECT bill.rn, patient_information.nric, patient_information.race, patient_information.address1, patient_information.address2, patient_information.address3, bill.guarantor_nric, bill.guarantor_address1,bill.guarantor_address2,bill.guarantor_address3, patient_admission.entry_datetime, 'reminder3' reminder_no, reminder3 reminder_batch_date, bill.discharge_date, bill.deleted, bill.bill_generation_billable_sum_rm
    FROM patient_admission
	LEFT JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
	LEFT JOIN bill ON patient_admission.rn = bill.rn
    WHERE bill.bill_generation_datetime is not null
)  dummy_name
WHERE reminder_batch_date is not null AND deleted = 0 AND reminder_batch_date <= batch_date 
ORDER BY reminder_no, rn;

END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `report4_query`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `report4_query`(IN `year` INT, IN `month` INT)

BEGIN
SELECT bill_view.rn, bill_view.name,bill_view.nric, bill_view.malaysian,bill_view.initial_ward_class, bill_view.Address,
 bill_print_id,bill_generation_datetime, COALESCE(bill_generation_final_fee_rm,0) AS bill_final_fee,COALESCE(payment_sum, 0) AS payment_sum,
  COALESCE(bill_generation_final_fee_rm,0)- COALESCE(payment_view.payment_sum,0) AS amount_owe, reminder1, reminder2,reminder3 FROM 

(    
(SELECT patient_admission.rn, bill.bill_print_id, bill.bill_generation_final_fee_rm, DATE(bill.bill_generation_datetime) AS bill_generation_date, bill.bill_generation_datetime, 
patient_admission.reminder1,patient_admission.reminder2,patient_admission.reminder3,patient_admission.initial_ward_class,
patient_information.patient_uid, patient_information.name,patient_information.nric, patient_information.nric REGEXP '^[0-9]{6}-[0-9]{2}-[0-9]{4}$' AS malaysian,
CONCAT(patient_information.address1,patient_information.address2, patient_information.address3) AS Address 
FROM patient_admission
	INNER JOIN bill ON patient_admission.rn = bill.rn
 	INNER JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
    where (bill.deleted = 0) 
    AND (EXTRACT(YEAR FROM bill.bill_generation_datetime)= year) 
    AND (EXTRACT(MONTH FROM bill.bill_generation_datetime)= month)
    ) AS bill_view 
LEFT JOIN
(SELECT RN, sum(CASE WHEN receipt_type = 'Refund' THEN -receipt_content_sum ELSE receipt_content_sum END) AS payment_sum FROM receipt WHERE receipt.receipt_uid NOT IN (SELECT cancellation_uid FROM cancellation) GROUP BY RN) AS payment_view
    ON bill_view.RN = payment_view.RN
    )ORDER BY bill_view.bill_generation_date, bill_view.bill_generation_datetime;

	END$$
DELIMITER ;

INSERT INTO `lookup_general`(`lookup_general_uid`, `code`, `category`, `name`, `long_description`, `recommend`) VALUES ('hyIQRT4FpJK_7de71t2p2X1cptGMSXfI','018/76303','Kod Akaun','018/76303','018/76303','1');

INSERT INTO `lookup_general`(`lookup_general_uid`, `code`, `category`, `name`, `long_description`, `recommend`) VALUES ('ub8iwKDzSStLx2llag97qUtXKl2WgeeX','018/76302','Kod Akaun','018/76302','018/76302','1');

INSERT INTO `patient_information` (`patient_uid`, `first_reg_date`, `nric`, `nationality`, `name`, `sex`, `race`, `phone_number`, `email`, `address1`, `address2`, `address3`, `job`, `DOB`) VALUES
('m-vPBL8igioXLRSnMSBsF6awk4jOgM0C', '1950-01-01', 'PESAKITLAINLAIN', 'Malaysia', 'Pesakit Lain Lain ', 'Male', 'MA', '', NULL, NULL, NULL, NULL, NULL, '1950-01-01');

INSERT INTO `patient_admission` (`rn`, `entry_datetime`, `patient_uid`, `initial_ward_code`, `initial_ward_class`, `reference`, `medical_legal_code`, `type`, `reminder1`, `reminder2`, `reminder3`) VALUES
('LAINLAIN', '1950-01-01 00:00:00', 'm-vPBL8igioXLRSnMSBsF6awk4jOgM0C', '01', '3', 'Default', 0, 'Normal', NULL, NULL, NULL);('LAINLAIN', '1950-01-01 00:00:00', 'm-vPBL8igioXLRSnMSBsF6awk4jOgM0C', '01', '3', 'Default', 0, 'Normal', NULL, NULL, NULL);

ALTER TABLE `bill` ADD `bill_forgive_date` DATETIME NULL DEFAULT NULL AFTER `discharge_date`;

CREATE TABLE `sghis`.`bill_forgive` (`bill_forgive_uid` VARCHAR(64) NOT NULL , `bill_forgive_date` DATE NOT NULL , `comment` VARCHAR(200) NOT NULL , PRIMARY KEY (`bill_forgive_uid`)) ENGINE = InnoDB;
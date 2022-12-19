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
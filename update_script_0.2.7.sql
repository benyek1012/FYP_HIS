ALTER TABLE `bill` DROP `nurse_responsible`;

ALTER TABLE `bill` ADD `discharge_date` DATETIME NULL AFTER `deleted`;

DROP PROCEDURE IF EXISTS `reminder_batch_select`;

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
			((SELECT rn, CONVERT(bill.final_ward_datetime,DATE) AS discharge_date, bill_generation_billable_sum_rm 						
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
			((SELECT rn, CONVERT(bill.final_ward_datetime,DATE) AS discharge_date, bill_generation_billable_sum_rm 						
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
			((SELECT rn, CONVERT(bill.final_ward_datetime,DATE) AS discharge_date, bill_generation_billable_sum_rm 						
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

END


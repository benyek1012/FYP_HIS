SELECT bill_view.rn, bill_print_id, entry_datetime, discharge_date, COALESCE(bill_generation_final_fee_rm,0)- COALESCE(payment_view.payment_sum,0) AS amount_owed,
COALESCE(bill_generation_final_fee_rm,0) AS bill_final_fee, COALESCE(payment_sum, 0) AS payment_sum FROM 

(SELECT bill.rn, bill.bill_print_id, bill.bill_generation_final_fee_rm, patient_admission.entry_datetime, bill.discharge_date 
FROM bill
	INNER JOIN patient_admission ON bill.rn = patient_admission.rn
    where bill.deleted = 0) AS bill_view (datetime need to set)
    LEFT JOIN
	(SELECT RN, sum(CASE WHEN receipt_type = 'Refund' THEN -receipt_content_sum ELSE receipt_content_sum END) AS payment_sum FROM receipt WHERE receipt.receipt_uid NOT IN (SELECT cancellation_uid FROM cancellation) GROUP BY RN) AS payment_view
    ON bill_view.RN = payment_view.RN;
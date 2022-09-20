/* Update Error change to varchar(500000) Version 0.2.5 */

ALTER TABLE `pekeliling_import` CHANGE `error` `error` VARCHAR(500000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

ALTER TABLE `treatment_details` CHANGE `treatment_name` `treatment_name` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `patient_admission` 
 ADD `guarantor_address` varchar(100) DEFAULT NULL;

CREATE TABLE `printer_profile` (
  `type` varchar(64) NOT NULL,
  `address` varchar(256) NOT NULL,
  `borang_daftar` varchar(64) NOT NULL,
  `charge_sheet` varchar(64) NOT NULL,
  `case_history` varchar(64) NOT NULL,
  `sticker` varchar(64) NOT NULL,
  `receipt` varchar(64) NOT NULL,
  `bill` varchar(64) NOT NULL,
  `printer_status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `printer_profile`
  ADD UNIQUE KEY `type` (`type`,`address`);
COMMIT;


DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `reminder_select_number`(IN `batch_date` VARCHAR(16))
BEGIN

SELECT rn, nric, race, address1, address2, address3, guarantor_nric,  guarantor_address1,guarantor_address2,guarantor_address3, entry_datetime, reminder_no AS 'Reminder Number', reminder_batch_date AS 'Batch date', final_ward_datetime AS 'Discharge Date', bill_generation_billable_sum_rm AS 'Billable Fee'
FROM (
SELECT bill.rn, patient_information.nric, patient_information.race, patient_information.address1, patient_information.address2, patient_information.address3, patient_admission.guarantor_nric, patient_admission.guarantor_address1,patient_admission.guarantor_address2,patient_admission.guarantor_address3, patient_admission.entry_datetime, 'reminder1' reminder_no, reminder1 reminder_batch_date, bill.final_ward_datetime, bill.deleted, bill.bill_generation_billable_sum_rm
    FROM patient_admission 
	LEFT JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
	LEFT JOIN bill ON patient_admission.rn = bill.rn
    WHERE bill.bill_generation_datetime is not null
    union all 
    
SELECT bill.rn, patient_information.nric, patient_information.race, patient_information.address1, patient_information.address2, patient_information.address3, patient_admission.guarantor_nric, patient_admission.guarantor_address1,patient_admission.guarantor_address2,patient_admission.guarantor_address3, patient_admission.entry_datetime, 'reminder2' reminder_no, reminder2 reminder_batch_date, bill.final_ward_datetime, bill.deleted, bill.bill_generation_billable_sum_rm
    FROM patient_admission
	LEFT JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
	LEFT JOIN bill ON patient_admission.rn = bill.rn
    WHERE bill.bill_generation_datetime is not null
	union all 
    
SELECT bill.rn, patient_information.nric, patient_information.race, patient_information.address1, patient_information.address2, patient_information.address3, patient_admission.guarantor_nric, patient_admission.guarantor_address1,patient_admission.guarantor_address2,patient_admission.guarantor_address3, patient_admission.entry_datetime, 'reminder3' reminder_no, reminder3 reminder_batch_date, bill.final_ward_datetime, bill.deleted, bill.bill_generation_billable_sum_rm
    FROM patient_admission
	LEFT JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
	LEFT JOIN bill ON patient_admission.rn = bill.rn
    WHERE bill.bill_generation_datetime is not null
)  dummy_name
WHERE reminder_batch_date is not null AND deleted = 0 AND reminder_batch_date <= batch_date 
ORDER BY reminder_no;

END$$
DELIMITER ;

ALTER TABLE `variable` ADD `hospital_name` VARCHAR(64) NOT NULL DEFAULT 'Sarawak General Hospital' AFTER `read_only`, ADD `director_name` VARCHAR(64) NOT NULL AFTER `hospital_name`;
	

ALTER TABLE `patient_admission` CHANGE `guarantor_address` `guarantor_address1` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `patient_admission` ADD `guarantor_address2` VARCHAR(100) NOT NULL AFTER `guarantor_address1`, ADD `guarantor_address3` VARCHAR(100) NOT NULL AFTER `guarantor_address2`;

ALTER TABLE `variable` ADD `hospital_phone_number` VARCHAR(64) NOT NULL DEFAULT '082276666' AFTER `director_name`, ADD `hospital_email` VARCHAR(64) NOT NULL DEFAULT 'sgh.moh.gov.my' AFTER `hospital_phone_number`;

ALTER TABLE `variable` CHANGE `director_name` `director_name` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Dr. Ngian Hie Ung';

UPDATE `variable` SET `director_name` = 'Dr. Ngian Hie Ung' WHERE `variable`.`read_only` = 0;

DROP PROCEDURE IF EXISTS `transaction_records`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `transaction_records`(IN `pid` VARCHAR(64))
BEGIN

	SELECT receipt.rn, receipt.receipt_content_datetime_paid, receipt.receipt_content_sum, receipt.receipt_type, 	receipt.receipt_content_payment_method, receipt.receipt_content_payer_name, receipt.receipt_serial_number, receipt.receipt_content_description, receipt.receipt_responsible
	FROM receipt
	INNER JOIN patient_admission 
    ON patient_admission.rn = receipt.rn
    INNER JOIN patient_information
    ON patient_information.patient_uid = patient_admission.patient_uid
    WHERE patient_information.patient_uid = pid
    
    UNION
     
    SELECT bill.rn, bill.bill_generation_datetime, bill.bill_generation_billable_sum_rm, null, null, null, bill.bill_print_id, null, bill.generation_responsible_uid 
    FROM bill 
    INNER JOIN patient_admission 
    ON patient_admission.rn = bill.rn
    INNER JOIN patient_information
    ON patient_information.patient_uid = patient_admission.patient_uid
    WHERE patient_information.patient_uid = pid 
    AND bill.bill_generation_datetime is not null;
    
 
END$$
DELIMITER ;


/* Update Error change to varchar(500000) Version 0.2.5 END */
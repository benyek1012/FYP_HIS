USE `sghis`;


ALTER TABLE `bill` ADD `guarantor_name` VARCHAR(200) NULL DEFAULT NULL AFTER `collection_center_code`,
 ADD `guarantor_nric` VARCHAR(20) NULL DEFAULT NULL AFTER `guarantor_name`,
 ADD `guarantor_phone_number` VARCHAR(100) NULL DEFAULT NULL AFTER `guarantor_nric`,
 ADD `guarantor_email` VARCHAR(100) NULL DEFAULT NULL AFTER `guarantor_phone_number`,
 ADD `guarantor_address1` VARCHAR(100) NULL DEFAULT NULL AFTER `guarantor_email`,
 ADD `guarantor_address2` VARCHAR(100) NULL DEFAULT NULL AFTER `guarantor_address1`,
 ADD `guarantor_address3` VARCHAR(100) NULL DEFAULT NULL AFTER `guarantor_address2`;


UPDATE bill b 
INNER JOIN patient_admission a on a.rn = b.rn 
SET 
	b.guarantor_name = a.guarantor_name,
    b.guarantor_nric = a.guarantor_nric,
    b.guarantor_phone_number = a.guarantor_phone_number,
    b.guarantor_email = a.guarantor_address1,
    b.guarantor_address1 = a.guarantor_address1,
    b.guarantor_address2 = a.guarantor_address2,
    b.guarantor_address3 = a.guarantor_address3;


ALTER TABLE `patient_admission`
	DROP COLUMN `guarantor_name`, 
    DROP COLUMN `guarantor_nric`, 
    DROP COLUMN `guarantor_phone_number`, 
    DROP COLUMN `guarantor_email`, 
    DROP COLUMN `guarantor_address1`, 
    DROP COLUMN `guarantor_address2`, 
    DROP COLUMN `guarantor_address3`;



DROP PROCEDURE IF EXISTS `reminder_select_number`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `reminder_select_number`(IN `batch_date` VARCHAR(16)) NOT DETERMINISTIC CONTAINS SQL SQL SECURITY DEFINER 
BEGIN

SELECT rn, nric, race, address1, address2, address3, guarantor_nric,  guarantor_address1,guarantor_address2,guarantor_address3, entry_datetime, reminder_no AS 'Reminder Number', reminder_batch_date AS 'Batch date', final_ward_datetime AS 'Discharge Date', bill_generation_billable_sum_rm AS 'Billable Fee'
FROM (
SELECT bill.rn, patient_information.nric, patient_information.race, patient_information.address1, patient_information.address2, patient_information.address3, bill.guarantor_nric, bill.guarantor_address1,bill.guarantor_address2,bill.guarantor_address3, patient_admission.entry_datetime, 'reminder1' reminder_no, reminder1 reminder_batch_date, bill.final_ward_datetime, bill.deleted, bill.bill_generation_billable_sum_rm
    FROM patient_admission 
	LEFT JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
	LEFT JOIN bill ON patient_admission.rn = bill.rn
    WHERE bill.bill_generation_datetime is not null
    union all 
    
SELECT bill.rn, patient_information.nric, patient_information.race, patient_information.address1, patient_information.address2, patient_information.address3, bill.guarantor_nric, bill.guarantor_address1,bill.guarantor_address2,bill.guarantor_address3, patient_admission.entry_datetime, 'reminder2' reminder_no, reminder2 reminder_batch_date, bill.final_ward_datetime, bill.deleted, bill.bill_generation_billable_sum_rm
    FROM patient_admission
	LEFT JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
	LEFT JOIN bill ON patient_admission.rn = bill.rn
    WHERE bill.bill_generation_datetime is not null
	union all 
    
SELECT bill.rn, patient_information.nric, patient_information.race, patient_information.address1, patient_information.address2, patient_information.address3, bill.guarantor_nric, bill.guarantor_address1,bill.guarantor_address2,bill.guarantor_address3, patient_admission.entry_datetime, 'reminder3' reminder_no, reminder3 reminder_batch_date, bill.final_ward_datetime, bill.deleted, bill.bill_generation_billable_sum_rm
    FROM patient_admission
	LEFT JOIN patient_information ON patient_admission.patient_uid = patient_information.patient_uid
	LEFT JOIN bill ON patient_admission.rn = bill.rn
    WHERE bill.bill_generation_datetime is not null
)  dummy_name
WHERE reminder_batch_date is not null AND deleted = 0 AND reminder_batch_date <= batch_date 
ORDER BY reminder_no, rn;

END$$
DELIMITER ;


ALTER TABLE `bill` ADD `guarantor_comment` VARCHAR(200) NULL DEFAULT NULL AFTER `guarantor_address3`;
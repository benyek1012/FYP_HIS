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
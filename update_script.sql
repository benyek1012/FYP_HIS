/* Update Error change to varchar(500000) Version 0.2.5 */

ALTER TABLE `pekeliling_import` CHANGE `error` `error` VARCHAR(500000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

ALTER TABLE `treatment_details` CHANGE `treatment_name` `treatment_name` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `patient_admission` 
 ADD `guarantor_address` varchar(100) DEFAULT NULL;

/* Update Error change to varchar(500000) Version 0.2.5 END */

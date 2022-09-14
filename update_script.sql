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

/* Update Error change to varchar(500000) Version 0.2.5 END */


ALTER TABLE bill MODIFY COLUMN department_name VARCHAR(200) DEFAULT NULL;

DROP VIEW IF EXISTS `sghis`.`util_rn_sum_payments`;

CREATE VIEW `sghis`.`util_rn_sum_payments` 
	AS 
		( 
			SELECT patient_admission.rn, patient_admission.patient_uid, IFNULL(SUM(CASE WHEN receipt_type = 'refund' THEN -receipt_content_sum ELSE receipt_content_sum END),0) AS sum_receipts, IFNULL(SUM(CASE WHEN receipt_type = 'exception' THEN receipt_content_sum ELSE 0 END),0) AS sum_exceptions
			FROM patient_admission LEFT JOIN (SELECT * FROM receipt 
			WHERE receipt.receipt_uid NOT IN (SELECT cancellation_uid FROM cancellation)) AS receipt ON patient_admission.rn = receipt.rn
			GROUP BY patient_admission.rn, patient_admission.patient_uid
		)
		
DROP VIEW IF EXISTS `sghis`.`util_patient_citizenship`;

CREATE VIEW `sghis`.`util_patient_citizenship` 
	AS 
		( 
			-- Check by bill (status_code) WHERE bill [deleted IS NULL or deleted <> 1 ] & admission isn't cancelled [bill.statuscode not in ('AC','ACOA') AND bill.department_code <> 'AC']
			-- If above has value, status_code IN ('PDOA','OA') -> Orang Asing. Else if not null, Malaysian.
			-- If null, check patient_information for nationality. 001->Malaysian, 002->Orang Asing, else NULL (unknown)  
			SELECT patient_information.patient_uid, patient_information.name, patient_information.nric,  patient_information.nationality, bill.status_code, bill.rn,
				CASE WHEN bill.status_code IS NOT NULL THEN 
					(CASE WHEN bill.status_code IN ('PDOA','OA') THEN 0 ELSE 1 END)
				WHEN patient_information.nationality = '001' THEN 1 
				WHEN patient_information.nationality = '002' THEN 0
				ELSE NULL 
				END
				AS is_Malaysian
			FROM 
			patient_information LEFT JOIN 
			(SELECT patient_admission.patient_uid AS patient_uid, MAX(bill.rn) AS rn, MAX(bill.status_code) AS status_code 
			FROM 
				(SELECT * FROM bill WHERE (deleted IS NULL OR deleted <> 1) AND (status_code NOT IN ('AC','ACOA') AND department_code <> 'AC')) AS bill 
				INNER JOIN patient_admission ON bill.rn = patient_admission.rn 
				WHERE bill_generation_datetime IN 
					(SELECT MAX(bill_generation_datetime) FROM bill WHERE (deleted IS NULL OR deleted <> 1) AND (status_code NOT IN ('AC','ACOA') AND department_code <> 'AC') GROUP BY rn)
				GROUP BY patient_admission.patient_uid) 
			AS bill ON bill.patient_uid = patient_information.patient_uid
		)		
		
		
		
		
DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3a1`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3a1`(
	IN billStartDateInclusive DATE,
	IN billEndDateInclusive DATE
)
BEGIN
-- Report 3A1: Senarai bil-bil Hospital Yang Sudah Dibayar
-- Select 'fully paid' bills where generation datetime within daterange, and where paid bills are are not fully paid using exceptions
	SELECT CAST(target_bills.bill_generation_datetime AS DATE) AS Tarikh, target_bills.rn AS RegNo, patient_information.name as Nama, patient_information.nric AS nric, target_bills.bill_generation_billable_sum_rm as Amaun, target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Baki, target_bills.description AS penjelasan, target_bills.bill_print_id AS NoBil, CONCAT(COALESCE(target_bills.status_code,''), ' (', COALESCE(target_bills.class,''),')') AS No_dan_Kelas_Wad, target_bills.department_code AS Kod_Jabatan FROM 
		(SELECT * FROM bill WHERE CAST(bill_generation_datetime AS DATE) >= billStartDateInclusive AND CAST(bill_generation_datetime AS DATE) <= billEndDateInclusive AND deleted <> 1) AS target_bills 
		INNER JOIN (SELECT * FROM util_rn_sum_payments WHERE util_rn_sum_payments.sum_receipts = util_rn_sum_payments.sum_exceptions
) as util_rn_sum_payments ON target_bills.rn = util_rn_sum_payments.rn 
		INNER JOIN patient_information ON util_rn_sum_payments.patient_uid = patient_information.patient_uid
		WHERE target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts = 0
		ORDER BY target_bills.bill_generation_datetime;
END$$

DELIMITER ;$$

DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3a2`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3a2`(
	IN billStartDateInclusive DATE,
	IN billEndDateInclusive DATE
)
BEGIN
-- Report 3A2: Senarai bil-bil Hospital Yang Belum Dibayar
-- Select 'not fully paid' bills where generation datetime within daterange
	SELECT CAST(target_bills.bill_generation_datetime AS DATE) AS Tarikh, target_bills.rn AS RegNo, patient_information.name as Nama, patient_information.nric AS nric, target_bills.bill_generation_billable_sum_rm as Amaun, target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Baki, target_bills.description AS penjelasan, target_bills.bill_print_id AS NoBil, CONCAT(COALESCE(target_bills.status_code,''), ' (', COALESCE(target_bills.class,''),')') AS No_dan_Kelas_Wad, target_bills.department_code AS Kod_Jabatan FROM 
		(SELECT * FROM bill WHERE CAST(bill_generation_datetime AS DATE) >= billStartDateInclusive AND CAST(bill_generation_datetime AS DATE) <= billEndDateInclusive AND deleted <> 1) AS target_bills 
		INNER JOIN util_rn_sum_payments ON target_bills.rn = util_rn_sum_payments.rn 
		INNER JOIN patient_information ON util_rn_sum_payments.patient_uid = patient_information.patient_uid
		WHERE target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts <> 0
		ORDER BY target_bills.bill_generation_datetime;
END$$

DELIMITER ;$$

DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3a3`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3a3`(
	IN billStartDateInclusive DATE,
	IN billEndDateInclusive DATE
)
BEGIN
-- Report 3A3: Senarai bil-bil Hospital Yang dikecualikan keseluruhannya
-- Select 'fully' bills where generation datetime within daterange where exception = billable sum (if billable sum = 0 from 'free', it's fine too) 
	SELECT CAST(target_bills.bill_generation_datetime AS DATE) AS Tarikh, target_bills.rn AS RegNo, patient_information.name as Nama, patient_information.nric AS nric, target_bills.bill_generation_billable_sum_rm as Amaun, target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Baki, target_bills.description AS penjelasan, target_bills.bill_print_id AS NoBil, CONCAT(COALESCE(target_bills.status_code,''), ' (', COALESCE(target_bills.class,''),')') AS No_dan_Kelas_Wad, target_bills.department_code AS Kod_Jabatan FROM 
		(SELECT * FROM bill WHERE CAST(bill_generation_datetime AS DATE) >= billStartDateInclusive AND CAST(bill_generation_datetime AS DATE) <= billEndDateInclusive AND deleted <> 1) AS target_bills 
		INNER JOIN util_rn_sum_payments ON target_bills.rn = util_rn_sum_payments.rn 
		INNER JOIN patient_information ON util_rn_sum_payments.patient_uid = patient_information.patient_uid
		WHERE target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_exceptions = 0
		ORDER BY target_bills.bill_generation_datetime;
END$$

DELIMITER ;$$

DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3a4`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3a4`(
	IN billStartDateInclusive DATE,
	IN billEndDateInclusive DATE
)
BEGIN
-- Report 3A4: Senarai bil-bil Hospital Yang dibatalkan
-- Select deleted bill list
	SELECT CAST(target_bills.bill_generation_datetime AS DATE) AS Tarikh, target_bills.rn AS RegNo, patient_information.name as Nama, patient_information.nric AS nric, target_bills.bill_generation_billable_sum_rm as Amaun, 'Bill Cancelled' AS Baki, target_bills.description AS penjelasan, target_bills.bill_print_id AS NoBil, CONCAT(COALESCE(target_bills.status_code,''), ' (', COALESCE(target_bills.class,''),')') AS No_dan_Kelas_Wad, target_bills.department_code AS Kod_Jabatan FROM 
		(SELECT * FROM bill WHERE CAST(bill_generation_datetime AS DATE) >= billStartDateInclusive AND CAST(bill_generation_datetime AS DATE) <= billEndDateInclusive AND deleted = 1) AS target_bills 
		INNER JOIN util_rn_sum_payments ON target_bills.rn = util_rn_sum_payments.rn 
		INNER JOIN patient_information ON util_rn_sum_payments.patient_uid = patient_information.patient_uid
		ORDER BY target_bills.bill_generation_datetime;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3a5`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3a5`(
	IN billStartDateInclusive DATE,
	IN billEndDateInclusive DATE
)
BEGIN
-- Report 3A5: Senarai bil-bil Hospital
-- Union 3A1, 3A2, 3A3, 3A4
-- can't manually call the stored procedures, so have to copy-paste codes


	(SELECT CAST(target_bills.bill_generation_datetime AS DATE) AS Tarikh, target_bills.rn AS RegNo, patient_information.name as Nama, patient_information.nric AS nric, target_bills.bill_generation_billable_sum_rm as Amaun, target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Baki, target_bills.description AS penjelasan, target_bills.bill_print_id AS NoBil, CONCAT(COALESCE(target_bills.status_code,''), ' (', COALESCE(target_bills.class,''),')') AS No_dan_Kelas_Wad, target_bills.department_code AS Kod_Jabatan, 'Dibayar Penuh' AS Jenis FROM 
		(SELECT * FROM bill WHERE CAST(bill_generation_datetime AS DATE) >= billStartDateInclusive AND CAST(bill_generation_datetime AS DATE) <= billEndDateInclusive AND deleted <> 1) AS target_bills 
		INNER JOIN (SELECT * FROM util_rn_sum_payments WHERE util_rn_sum_payments.sum_receipts <> util_rn_sum_payments.sum_exceptions
) as util_rn_sum_payments ON target_bills.rn = util_rn_sum_payments.rn 
		INNER JOIN patient_information ON util_rn_sum_payments.patient_uid = patient_information.patient_uid
		WHERE target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts = 0
		ORDER BY target_bills.bill_generation_datetime)
	UNION ALL
	(SELECT CAST(target_bills.bill_generation_datetime AS DATE) AS Tarikh, target_bills.rn AS RegNo, patient_information.name as Nama, patient_information.nric AS nric, target_bills.bill_generation_billable_sum_rm as Amaun, target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Baki, target_bills.description AS penjelasan, target_bills.bill_print_id AS NoBil, CONCAT(COALESCE(target_bills.status_code,''), ' (', COALESCE(target_bills.class,''),')') AS No_dan_Kelas_Wad, target_bills.department_code AS Kod_Jabatan, 'Belum Dibayar' AS Jenis FROM 
		(SELECT * FROM bill WHERE CAST(bill_generation_datetime AS DATE) >= billStartDateInclusive AND CAST(bill_generation_datetime AS DATE) <= billEndDateInclusive AND deleted <> 1) AS target_bills 
		INNER JOIN util_rn_sum_payments ON target_bills.rn = util_rn_sum_payments.rn 
		INNER JOIN patient_information ON util_rn_sum_payments.patient_uid = patient_information.patient_uid
		WHERE target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts <> 0
		ORDER BY target_bills.bill_generation_datetime)
	UNION ALL
	(SELECT CAST(target_bills.bill_generation_datetime AS DATE) AS Tarikh, target_bills.rn AS RegNo, patient_information.name as Nama, patient_information.nric AS nric, target_bills.bill_generation_billable_sum_rm as Amaun, target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Baki, target_bills.description AS penjelasan, target_bills.bill_print_id AS NoBil, CONCAT(COALESCE(target_bills.status_code,''), ' (', COALESCE(target_bills.class,''),')') AS No_dan_Kelas_Wad, target_bills.department_code AS Kod_Jabatan, 'Dikecualikan Seluruhnya' AS Jenis FROM 
		(SELECT * FROM bill WHERE CAST(bill_generation_datetime AS DATE) >= billStartDateInclusive AND CAST(bill_generation_datetime AS DATE) <= billEndDateInclusive AND deleted <> 1) AS target_bills 
		INNER JOIN util_rn_sum_payments ON target_bills.rn = util_rn_sum_payments.rn 
		INNER JOIN patient_information ON util_rn_sum_payments.patient_uid = patient_information.patient_uid
		WHERE target_bills.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_exceptions = 0
		ORDER BY target_bills.bill_generation_datetime)
	UNION ALL
	(SELECT CAST(target_bills.bill_generation_datetime AS DATE) AS Tarikh, target_bills.rn AS RegNo, patient_information.name as Nama, patient_information.nric AS nric, target_bills.bill_generation_billable_sum_rm as Amaun, 'Bill Cancelled' AS Baki, target_bills.description AS penjelasan, target_bills.bill_print_id AS NoBil, CONCAT(COALESCE(target_bills.status_code,''), ' (', COALESCE(target_bills.class,''),')') AS No_dan_Kelas_Wad, target_bills.department_code AS Kod_Jabatan, 'Dibatalkan' AS Jenis  FROM 
		(SELECT * FROM bill WHERE CAST(bill_generation_datetime AS DATE) >= billStartDateInclusive AND CAST(bill_generation_datetime AS DATE) <= billEndDateInclusive AND deleted = 1) AS target_bills 
		INNER JOIN util_rn_sum_payments ON target_bills.rn = util_rn_sum_payments.rn 
		INNER JOIN patient_information ON util_rn_sum_payments.patient_uid = patient_information.patient_uid
		ORDER BY target_bills.bill_generation_datetime);
END$$

DELIMITER ;$$

DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3b1`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3b1`(
	IN receiptStartDateInclusive DATE,
	IN receiptEndDateInclusive DATE
)
BEGIN
-- Report 3B1: Senarai Bayaran Bil Hospital
-- Paid List
	SELECT CAST(receipt.receipt_content_datetime_paid AS DATE) AS Tarikh, receipt.rn AS RegNo, patient_information.name AS Nama, patient_information.nric AS nric, receipt.receipt_content_sum AS amaun, receipt.receipt_content_description AS Penjelasan, receipt.receipt_serial_number AS NoResit, CASE WHEN receipt.receipt_serial_number IS NULL THEN '003' ELSE '018' END AS CollCenter, CASE WHEN cancellation.cancellation_uid IS NULL THEN '' ELSE 'YES' END AS Receipt_Cancelled
	FROM 
		(Select * FROM receipt WHERE CAST(receipt_content_datetime_paid AS DATE) >= receiptStartDateInclusive AND CAST(receipt_content_datetime_paid AS DATE) <= receiptEndDateInclusive AND receipt_type = 'bill') AS receipt 
	INNER JOIN patient_admission on patient_admission.rn = receipt.rn
	INNER JOIN patient_information on patient_information.patient_uid = patient_admission.patient_uid
	LEFT JOIN cancellation ON receipt.receipt_uid = cancellation.cancellation_uid
	ORDER BY receipt.receipt_content_datetime_paid;
END$$

DELIMITER ;$$

DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3b2`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3b2`(
	IN receiptStartDateInclusive DATE,
	IN receiptEndDateInclusive DATE
)
BEGIN
-- Report 3B2: Senarai Bayaran Bil Lain-Lain Hospital
-- 'Others' bill, not related to RN
	SELECT CAST(receipt.receipt_content_datetime_paid AS DATE) AS Tarikh, '' AS RegNo, receipt.receipt_content_payer_name AS Nama, '' AS nric, receipt.receipt_content_sum AS amaun, receipt.receipt_content_description AS Penjelasan, receipt.receipt_serial_number AS NoResit, CASE WHEN receipt.receipt_serial_number IS NULL THEN '003' ELSE '018' END AS CollCenter, CASE WHEN cancellation.cancellation_uid IS NULL THEN '' ELSE 'YES' END AS Receipt_Cancelled
	FROM 
		(Select * FROM receipt WHERE CAST(receipt_content_datetime_paid AS DATE) >= receiptStartDateInclusive AND CAST(receipt_content_datetime_paid AS DATE) <= receiptEndDateInclusive AND receipt_type = 'other') AS receipt 
	LEFT JOIN cancellation ON receipt.receipt_uid = cancellation.cancellation_uid
	ORDER BY receipt.receipt_content_datetime_paid;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3b3`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3b3`(
	IN receiptStartDateInclusive DATE,
	IN receiptEndDateInclusive DATE
)
BEGIN
-- Report 3B3: Senarai Bayaran Deposit Hospital
-- Just deposits
	SELECT CAST(receipt.receipt_content_datetime_paid AS DATE) AS Tarikh, receipt.rn AS RegNo, patient_information.name AS Nama, patient_information.nric AS nric, receipt.receipt_content_sum AS amaun, receipt.receipt_content_description AS Penjelasan, receipt.receipt_serial_number AS NoResit, CASE WHEN receipt.receipt_serial_number IS NULL THEN '003' ELSE '018' END AS CollCenter, CASE WHEN cancellation.cancellation_uid IS NULL THEN '' ELSE 'YES' END AS Receipt_Cancelled
	FROM 
		(Select * FROM receipt WHERE CAST(receipt_content_datetime_paid AS DATE) >= receiptStartDateInclusive AND CAST(receipt_content_datetime_paid AS DATE) <= receiptEndDateInclusive AND receipt_type = 'deposit') AS receipt 
	INNER JOIN patient_admission on patient_admission.rn = receipt.rn
	INNER JOIN patient_information on patient_information.patient_uid = patient_admission.patient_uid
	LEFT JOIN cancellation ON receipt.receipt_uid = cancellation.cancellation_uid
	ORDER BY receipt.receipt_content_datetime_paid;
END$$

DELIMITER ;$$

DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3b4`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3b4`(
	IN receiptStartDateInclusive DATE,
	IN receiptEndDateInclusive DATE
)
BEGIN
-- Report 3B4: Senarai Bayaran Hospital Yang dikecualikan
-- Just exceptions
	SELECT CAST(receipt.receipt_content_datetime_paid AS DATE) AS Tarikh, receipt.rn AS RegNo, patient_information.name AS Nama, patient_information.nric AS nric, receipt.receipt_content_sum AS amaun, receipt.receipt_content_description AS Penjelasan, receipt.receipt_serial_number AS NoResit, CASE WHEN receipt.receipt_serial_number IS NULL THEN '003' ELSE '018' END AS CollCenter, CASE WHEN cancellation.cancellation_uid IS NULL THEN '' ELSE 'YES' END AS Receipt_Cancelled
	FROM 
		(Select * FROM receipt WHERE CAST(receipt_content_datetime_paid AS DATE) >= receiptStartDateInclusive AND CAST(receipt_content_datetime_paid AS DATE) <= receiptEndDateInclusive AND receipt_type = 'exception') AS receipt 
	INNER JOIN patient_admission on patient_admission.rn = receipt.rn
	INNER JOIN patient_information on patient_information.patient_uid = patient_admission.patient_uid
	LEFT JOIN cancellation ON receipt.receipt_uid = cancellation.cancellation_uid
	ORDER BY receipt.receipt_content_datetime_paid;
END$$

DELIMITER ;$$
DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3c`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3c`(
	IN receiptStartDateInclusive DATE,
	IN receiptEndDateInclusive DATE
)
BEGIN
-- Report 3C: Senarai Pungutan Menurut Operator
	SELECT receipt.receipt_serial_number AS NoResit, CAST(receipt.receipt_content_datetime_paid AS DATE) AS Tarikh, receipt.receipt_content_sum AS amaun, receipt.receipt_type AS receipt_type,
		CASE WHEN receipt.receipt_type = 'Deposit' THEN 'D' 
			WHEN receipt.receipt_type = 'Exception' THEN 'E'
			WHEN receipt.receipt_type = 'Refund' THEN 'R'
			WHEN receipt.receipt_type = 'Bill' THEN 'I'
			WHEN receipt.receipt_type = 'Other' THEN 'O'
			WHEN receipt.receipt_serial_number = '' THEN 'E' -- null is for bayaran di luar hospital, it wont clash', not expecting it to reach here either
			ELSE 'Unknown' END
			AS Type, 
		receipt.rn AS RN, patient_information.name AS Nama, new_user.username AS Operator, CASE WHEN cancellation.cancellation_uid IS NULL THEN '' ELSE 'Yes' END AS Dibatalkan
	FROM 
		(Select * FROM receipt WHERE CAST(receipt_content_datetime_paid AS DATE) >= receiptStartDateInclusive AND CAST(receipt_content_datetime_paid AS DATE) <= receiptEndDateInclusive) AS receipt 
	INNER JOIN patient_admission on patient_admission.rn = receipt.rn
	INNER JOIN patient_information on patient_information.patient_uid = patient_admission.patient_uid
	LEFT JOIN new_user ON receipt.receipt_responsible = new_user.user_uid
	LEFT JOIN cancellation ON receipt.receipt_uid = cancellation.cancellation_uid
	WHERE receipt.receipt_type <> 'Refund'
	ORDER BY receipt.receipt_content_datetime_paid;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_3d`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_3d`(
	IN receiptStartDateInclusive DATE,
	IN receiptEndDateInclusive DATE
)
BEGIN
-- Report 3D: Senarai Bayaran Yang Dikembalikan 

	SELECT  receipt.receipt_serial_number AS NoResit, CAST(receipt.receipt_content_datetime_paid AS DATE) AS Tarikh, receipt.receipt_content_sum AS amaun, receipt.receipt_type AS receipt_type,
		CASE WHEN receipt.receipt_type = 'Deposit' THEN 'D' 
			WHEN receipt.receipt_type = 'Exception' THEN 'E'
			WHEN receipt.receipt_type = 'Refund' THEN 'R'
			WHEN receipt.receipt_type = 'Bill' THEN 'I'
			WHEN receipt.receipt_type = 'Other' THEN 'O'
			WHEN receipt.receipt_serial_number = '' THEN 'E' -- null is for bayaran di luar hospital, it wont clash', not expecting it to reach here either
			ELSE 'Unknown' END
			AS Type, 
		receipt.rn AS RN, patient_information.name AS Nama, new_user.username AS Operator, CASE WHEN cancellation.cancellation_uid IS NULL THEN '' ELSE 'Yes' END AS Dibatalkan
	FROM 
		(Select * FROM receipt WHERE CAST(receipt_content_datetime_paid AS DATE) >= receiptStartDateInclusive AND CAST(receipt_content_datetime_paid AS DATE) <= receiptEndDateInclusive) AS receipt 
	INNER JOIN patient_admission on patient_admission.rn = receipt.rn
	INNER JOIN patient_information on patient_information.patient_uid = patient_admission.patient_uid
	LEFT JOIN new_user ON receipt.receipt_responsible = new_user.user_uid
	LEFT JOIN cancellation ON receipt.receipt_uid = cancellation.cancellation_uid
	WHERE receipt.receipt_type = 'Refund'
	ORDER BY receipt.receipt_content_datetime_paid;
END$$

DELIMITER ;$$

DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_4`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_4`(
	IN entryStartDateInclusive DATE,
	IN entryEndDateInclusive DATE
)
BEGIN
-- Report 4: Senarai Bil-bil Hospital yang Belum Dijelaskan (Swasta)
-- Swasta (Report4) = Kod Taraf LIKE 'PG%' represents penjamin swasta
-- Awam (Report6) Kod Taraf LIKE 'GS%' & Kod Jabatan = '5F' or 'F' represents penjamin kerajaan
-- Report 5 = none of the above


	SELECT bill.guarantor_name AS Nama_Syarikat, patient_information.name AS Nama_Pesakit, patient_information.nric AS NRIC, bill.bill_print_id AS Bill_No, CAST(patient_admission.entry_datetime AS DATE) AS Tarikh, bill.bill_generation_billable_sum_rm AS Jumlah_bil, bill.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Jumlah, bill.description AS Lain_Lain_Hal, bill.guarantor_comment AS catatan
	FROM (SELECT * FROM patient_admission WHERE CAST(patient_admission.entry_datetime AS DATE) >= entryStartDateInclusive AND 
		CAST(patient_admission.entry_datetime AS DATE) <= entryEndDateInclusive) AS patient_admission
	-- filter for 'not fully paid' bills as part of inner join
	INNER JOIN (SELECT * FROM bill WHERE (deleted IS NULL OR deleted <> 1) AND status_code LIKE 'PG%') AS bill ON patient_admission.rn = bill.rn
	INNER JOIN util_rn_sum_payments ON patient_admission.rn = util_rn_sum_payments.rn AND bill.bill_generation_billable_sum_rm > util_rn_sum_payments.sum_receipts
	INNER JOIN patient_information ON patient_information.patient_uid = patient_admission.patient_uid
	ORDER BY bill.guarantor_name, patient_admission.entry_datetime;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_5`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_5`(
	IN entryStartDateInclusive DATE,
	IN entryEndDateInclusive DATE
)
BEGIN
-- Report 5: Senarai Bil-bil Hospital yang Belum Dijelaskan (Bagi Pesakit Awam)
-- Swasta (Report4) = Kod Taraf LIKE 'PG%' represents penjamin swasta
-- Awam (Report6) Kod Taraf LIKE 'GS%' & Kod Jabatan = '5F' or 'F' represents penjamin kerajaan
-- Report 5 = none of the above


	SELECT patient_information.name AS Nama_Pesakit, patient_information.nric AS No_Kad_Pengenalan, CONCAT(COALESCE(patient_information.address1,''), ' ', COALESCE(patient_information.address2,''), ' ', COALESCE(patient_information.address3,'')) AS Alamat_Pesakit, bill.bill_print_id AS No_Bil, CAST(patient_admission.entry_datetime AS DATE) AS Tarikh, bill.bill_generation_billable_sum_rm AS Jumlah_bil, bill.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Jumlah, bill.description AS Lain_lain_hal
	FROM (SELECT * FROM patient_admission WHERE CAST(patient_admission.entry_datetime AS DATE) >= entryStartDateInclusive AND 
		CAST(patient_admission.entry_datetime AS DATE) <= entryEndDateInclusive) AS patient_admission
	INNER JOIN (SELECT * FROM bill WHERE (deleted IS NULL OR deleted <> 1) AND NOT ( status_code LIKE 'PG%' OR (status_code LIKE 'GS%' AND department_code IN ('5F', 'F')))) AS bill ON patient_admission.rn = bill.rn
	-- filter for 'not fully paid' bills as part of inner join
	INNER JOIN util_rn_sum_payments ON patient_admission.rn = util_rn_sum_payments.rn AND bill.bill_generation_billable_sum_rm > util_rn_sum_payments.sum_receipts
	INNER JOIN patient_information ON patient_information.patient_uid = patient_admission.patient_uid
	ORDER BY bill.guarantor_name, patient_admission.entry_datetime;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_6`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_6`(
	IN entryStartDateInclusive DATE,
	IN entryEndDateInclusive DATE
)
BEGIN
-- Report 6: Senarai Bil-bil Hospital yang Belum Dijelaskan (Awam)
-- Swasta (Report4) = Kod Taraf LIKE 'PG%' represents penjamin swasta
-- Awam (Report6) Kod Taraf LIKE 'GS%' & Kod Jabatan = '5F' or 'F' represents penjamin kerajaan
-- Report 5 = none of the above


	SELECT bill.guarantor_name AS Nama_Syarikat, patient_information.nric AS NRIC, CONCAT(COALESCE(bill.guarantor_address1,''), ' ', COALESCE(bill.guarantor_address2,''), ' ', COALESCE(bill.guarantor_address3,'')) AS Alamat_Penjamin, patient_information.name AS Nama_Pesakit, bill.rn AS RN, bill.class AS Klas_wad, bill.bill_print_id AS No_Bil, CAST(patient_admission.entry_datetime AS DATE) AS Tarikh, bill.bill_generation_billable_sum_rm AS Amaun_bil, bill.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Amaun, bill.description AS Lain_Lain_Hal, bill.guarantor_comment AS Catatan
	FROM (SELECT * FROM patient_admission WHERE CAST(patient_admission.entry_datetime AS DATE) >= entryStartDateInclusive AND 
		CAST(patient_admission.entry_datetime AS DATE) <= entryEndDateInclusive) AS patient_admission
	INNER JOIN (SELECT * FROM bill WHERE (deleted IS NULL OR deleted <> 1) AND status_code LIKE 'GS%' AND department_code IN ('5F', 'F')) AS bill ON patient_admission.rn = bill.rn
	-- filter for 'not fully paid' bills as part of inner join
	INNER JOIN util_rn_sum_payments ON patient_admission.rn = util_rn_sum_payments.rn AND bill.bill_generation_billable_sum_rm > util_rn_sum_payments.sum_receipts
	INNER JOIN patient_information ON patient_information.patient_uid = patient_admission.patient_uid
	ORDER BY bill.guarantor_name, patient_admission.entry_datetime;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_15`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_15`(
	IN entryStartDateInclusive DATE,
	IN entryEndDateInclusive DATE
)
BEGIN
-- Report 15: Penyata Bulanan Pesakit-Pesakit Yang Meminta Pengecualian Bayaran


	SELECT patient_admission.rn AS No_Pendaftaran_Pesakit, patient_information.name AS Nama_Pesakit, CAST(patient_admission.entry_datetime AS DATE) AS Tarikh, bill.bill_generation_billable_sum_rm AS Jumlah_Kena_Dibayar, util_rn_sum_payments.sum_receipts - util_rn_sum_payments.sum_exceptions AS Jumlah_yang_Dibayar, util_rn_sum_payments.sum_exceptions AS Jumlah_yang_dikecualikan, receipt_comments.comments AS Catatan

	FROM (SELECT * FROM patient_admission WHERE CAST(patient_admission.entry_datetime AS DATE) >= entryStartDateInclusive AND 
		CAST(patient_admission.entry_datetime AS DATE) <= entryEndDateInclusive) AS patient_admission
	INNER JOIN (SELECT * FROM util_rn_sum_payments WHERE sum_exceptions > 0) AS util_rn_sum_payments ON patient_admission.rn = util_rn_sum_payments.rn
	INNER JOIN patient_information ON patient_information.patient_uid = patient_admission.patient_uid
	LEFT JOIN (SELECT * FROM bill WHERE (deleted IS NULL OR deleted <> 1)) AS bill ON patient_admission.rn = bill.rn
	LEFT JOIN (SELECT rn, GROUP_CONCAT(COALESCE(receipt_content_description,'')) AS comments FROM receipt WHERE receipt_type = 'Exception' AND receipt_uid NOT IN (SELECT cancellation_uid FROM cancellation) GROUP BY rn) AS receipt_comments ON patient_admission.rn = receipt_comments.rn
	ORDER BY patient_admission.entry_datetime;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_16a`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_16a`(
	IN billDischargeStartDateInclusive DATE,
	IN billDischargeEndDateInclusive DATE
)
BEGIN
-- Report 16a: Senarai Bil-Bil Hospital Yang Belum Dibayar


	SELECT lookup_ward.ward_name AS Wad, bill.bill_print_id AS No_Bill, CAST(bill.discharge_date AS DATE) AS Tarikh_Discaj, bill.rn AS No_Pesakit, patient_information.name AS Nama_Pesakit, CONCAT(COALESCE(patient_information.address1,''), ' ', COALESCE(patient_information.address2,''), ' ', COALESCE(patient_information.address3,'')) AS Alamat_Pesakit, bill.bill_generation_billable_sum_rm AS Amaun, bill.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Balance, 
CASE WHEN patient_admission.reminder3 IS NOT NULL THEN '3' WHEN patient_admission.reminder2 IS NOT NULL THEN '2' WHEN patient_admission.reminder1 IS NOT NULL THEN '1' ELSE '' END AS No_Surat_Peringatan, 
CAST(COALESCE(patient_admission.reminder3, patient_admission.reminder2, patient_admission.reminder1, '') AS DATE) AS Tarikh_Surat, CONCAT(COALESCE(bill.description,''), ' ', COALESCE(bill.guarantor_comment,'')) AS Penjelasan

	FROM (SELECT * FROM bill WHERE CAST(discharge_date AS DATE) >= billDischargeStartDateInclusive AND 
		CAST(discharge_date AS DATE) <= billDischargeEndDateInclusive AND (deleted IS NULL OR deleted <> 1)) AS bill
	INNER JOIN util_rn_sum_payments ON bill.rn = util_rn_sum_payments.rn AND bill.bill_generation_billable_sum_rm > util_rn_sum_payments.sum_receipts
	-- filter performed on the above join
	INNER JOIN patient_admission ON patient_admission.rn = bill.rn 
	INNER JOIN patient_information ON patient_information.patient_uid = patient_admission.patient_uid
	LEFT JOIN lookup_ward ON patient_admission.initial_ward_code = lookup_ward.ward_code
	ORDER BY lookup_ward.ward_name, bill.discharge_date, bill.bill_print_id;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_16b`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_16b`(
	IN billDischargeStartDateInclusive DATE,
	IN billDischargeEndDateInclusive DATE
)
BEGIN
-- Report 16b: Senarai Bil-Bil Hospital Yang Belum Dibayar Menurut Kelas
	SELECT bill.class as Kls, bill.rn AS RN, patient_information.name AS Nama_Pesakit, patient_information.NRIC AS No_KP, CONCAT(COALESCE(patient_information.address1,''), ' ', COALESCE(patient_information.address2,''), ' ', COALESCE(patient_information.address3,'')) AS Alamat, bill.bill_print_id AS Rujukan_Bil, CAST(bill.discharge_date AS DATE) AS Tarikh_Bil_Discaj, bill.bill_generation_billable_sum_rm AS Amaun, bill.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Balance, DATEDIFF(CURDATE(), CAST(bill.discharge_date AS DATE))+1 AS Bil_Hari, 
CASE WHEN patient_admission.reminder1 IS NOT NULL THEN DATE_ADD(CAST(bill.discharge_date AS DATE), INTERVAL 14 DAY) ELSE NULL END AS Tarikh_Surat_Peringatan_1, 
CASE WHEN patient_admission.reminder2 IS NOT NULL THEN DATE_ADD(CAST(bill.discharge_date AS DATE), INTERVAL 28 DAY) ELSE NULL END AS Tarikh_Surat_Peringatan_2, 
CASE WHEN patient_admission.reminder3 IS NOT NULL THEN DATE_ADD(CAST(bill.discharge_date AS DATE), INTERVAL 42 DAY) ELSE NULL END AS Tarikh_Surat_Peringatan_3, 
patient_admission.reminder1 AS Tarikh_Batch_Surat_Peringatan_1, patient_admission.reminder2 AS Tarikh_Batch_Surat_Peringatan_2, patient_admission.reminder3 AS Tarikh_Batch_Surat_Peringatan_3

	FROM (SELECT * FROM bill WHERE CAST(discharge_date AS DATE) >= billDischargeStartDateInclusive AND 
		CAST(discharge_date AS DATE) <= billDischargeEndDateInclusive AND (deleted IS NULL OR deleted <> 1)) AS bill
	INNER JOIN util_rn_sum_payments ON bill.rn = util_rn_sum_payments.rn AND bill.bill_generation_billable_sum_rm > util_rn_sum_payments.sum_receipts
	-- filter performed on the above join
	INNER JOIN patient_admission ON patient_admission.rn = bill.rn 
	INNER JOIN patient_information ON patient_information.patient_uid = patient_admission.patient_uid
	ORDER BY bill.class, bill.discharge_date, bill.bill_print_id;
END$$

DELIMITER ;$$

DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_16b1`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_16b1`(
	IN billDischargeStartDateInclusive DATE,
	IN billDischargeEndDateInclusive DATE
)
BEGIN
-- Report 16b1: Senarai Bil-Bil Hospital Warganegara Yang Belum Dibayar Menurut Kelas
	SELECT bill.class as Kls, bill.rn AS RN, util_patient_citizenship.is_Malaysian, patient_information.name AS Nama_Pesakit, patient_information.NRIC AS No_KP, CONCAT(COALESCE(patient_information.address1,''), ' ', COALESCE(patient_information.address2,''), ' ', COALESCE(patient_information.address3,'')) AS Alamat, bill.bill_print_id AS Rujukan_Bil, CAST(bill.discharge_date AS DATE) AS Tarikh_Bil_Discaj, bill.bill_generation_billable_sum_rm AS Amaun, bill.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Balance, DATEDIFF(CURDATE(), CAST(bill.discharge_date AS DATE))+1 AS Bil_Hari, 
CASE WHEN patient_admission.reminder1 IS NOT NULL THEN DATE_ADD(CAST(bill.discharge_date AS DATE), INTERVAL 14 DAY) ELSE NULL END AS Tarikh_Surat_Peringatan_1, 
CASE WHEN patient_admission.reminder2 IS NOT NULL THEN DATE_ADD(CAST(bill.discharge_date AS DATE), INTERVAL 28 DAY) ELSE NULL END AS Tarikh_Surat_Peringatan_2, 
CASE WHEN patient_admission.reminder3 IS NOT NULL THEN DATE_ADD(CAST(bill.discharge_date AS DATE), INTERVAL 42 DAY) ELSE NULL END AS Tarikh_Surat_Peringatan_3, 
patient_admission.reminder1 AS Tarikh_Batch_Surat_Peringatan_1, patient_admission.reminder2 AS Tarikh_Batch_Surat_Peringatan_2, patient_admission.reminder3 AS Tarikh_Batch_Surat_Peringatan_3

	FROM (SELECT * FROM bill WHERE CAST(discharge_date AS DATE) >= billDischargeStartDateInclusive AND 
		CAST(discharge_date AS DATE) <= billDischargeEndDateInclusive AND (deleted IS NULL OR deleted <> 1)) AS bill
	INNER JOIN util_rn_sum_payments ON bill.rn = util_rn_sum_payments.rn AND bill.bill_generation_billable_sum_rm > util_rn_sum_payments.sum_receipts
	-- filter performed on the above join
	INNER JOIN patient_admission ON patient_admission.rn = bill.rn 
	INNER JOIN patient_information ON patient_information.patient_uid = patient_admission.patient_uid
	INNER JOIN util_patient_citizenship ON util_patient_citizenship.patient_uid = patient_admission.patient_uid AND util_patient_citizenship.is_Malaysian =1
	ORDER BY bill.class, bill.discharge_date, bill.bill_print_id;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_16b2`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_16b2`(
	IN billDischargeStartDateInclusive DATE,
	IN billDischargeEndDateInclusive DATE
)
BEGIN
-- Report 16b2: Senarai Bil-Bil Hospital Orang Asing Yang Belum Dibayar Menurut Kelas
	SELECT bill.class as Kls, bill.rn AS RN, util_patient_citizenship.is_Malaysian, patient_information.name AS Nama_Pesakit, patient_information.NRIC AS No_KP, CONCAT(COALESCE(patient_information.address1,''), ' ', COALESCE(patient_information.address2,''), ' ', COALESCE(patient_information.address3,'')) AS Alamat, bill.bill_print_id AS Rujukan_Bil, CAST(bill.discharge_date AS DATE) AS Tarikh_Bil_Discaj, bill.bill_generation_billable_sum_rm AS Amaun, bill.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Balance, DATEDIFF(CURDATE(), CAST(bill.discharge_date AS DATE))+1 AS Bil_Hari, 
CASE WHEN patient_admission.reminder1 IS NOT NULL THEN DATE_ADD(CAST(bill.discharge_date AS DATE), INTERVAL 14 DAY) ELSE NULL END AS Tarikh_Surat_Peringatan_1, 
CASE WHEN patient_admission.reminder2 IS NOT NULL THEN DATE_ADD(CAST(bill.discharge_date AS DATE), INTERVAL 28 DAY) ELSE NULL END AS Tarikh_Surat_Peringatan_2, 
CASE WHEN patient_admission.reminder3 IS NOT NULL THEN DATE_ADD(CAST(bill.discharge_date AS DATE), INTERVAL 42 DAY) ELSE NULL END AS Tarikh_Surat_Peringatan_3, 
patient_admission.reminder1 AS Tarikh_Batch_Surat_Peringatan_1, patient_admission.reminder2 AS Tarikh_Batch_Surat_Peringatan_2, patient_admission.reminder3 AS Tarikh_Batch_Surat_Peringatan_3

	FROM (SELECT * FROM bill WHERE CAST(discharge_date AS DATE) >= billDischargeStartDateInclusive AND 
		CAST(discharge_date AS DATE) <= billDischargeEndDateInclusive AND (deleted IS NULL OR deleted <> 1)) AS bill
	INNER JOIN util_rn_sum_payments ON bill.rn = util_rn_sum_payments.rn AND bill.bill_generation_billable_sum_rm > util_rn_sum_payments.sum_receipts
	-- filter performed on the above join
	INNER JOIN patient_admission ON patient_admission.rn = bill.rn 
	INNER JOIN patient_information ON patient_information.patient_uid = patient_admission.patient_uid
	INNER JOIN util_patient_citizenship ON util_patient_citizenship.patient_uid = patient_admission.patient_uid AND util_patient_citizenship.is_Malaysian =0
	ORDER BY bill.class, bill.discharge_date, bill.bill_print_id;
END$$

DELIMITER ;$$

DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`report_17`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `report_17`(
	IN billDischargeStartDateInclusive DATE,
	IN billDischargeEndDateInclusive DATE
)
BEGIN
-- Report 17: Senarai Bil-Bil Hospital
	SELECT bill.rn AS RN, patient_information.name AS Nama_Pesakit, patient_information.nric AS nric, CONCAT(COALESCE(patient_information.name,''), ' ', COALESCE(patient_information.nric,'')) AS Nama_Pesakit_Tambah_IC, CONCAT(COALESCE(bill.status_code,''), ' (', COALESCE(bill.class,''),')') AS No_dan_Kelas_Wad, bill.department_code AS Kod_Jabatan, CAST(patient_admission.entry_datetime AS DATE)AS Tarikh_Masuk_Wad, CAST(bill.discharge_date AS DATE) AS Tarikh_Keluar_Wad, deposit.Deposit_nombor AS Deposit_Nombor_Resit, deposit.Deposit_Tarikh_Resit AS Deposit_Tarikh_Resit, deposit.Deposit_Amaun AS Deposit_Amaun, bill.bill_generation_billable_sum_rm AS Jumlah_Caj_Dikenakan, util_rn_sum_payments.sum_exceptions AS Pengecualian_Bayaran_Yang_Diluluskan, bill.bill_print_id AS Bil_Nombor_Resit, CAST(bill.bill_generation_datetime AS DATE) AS Bil_Tarikh, bill.bill_generation_billable_sum_rm AS Bil_Nilai_Bersih, receipt.receipt_serial_number AS Bayaran_Nombor, CAST(receipt.receipt_content_datetime_paid AS DATE) AS Bayaran_Tarikh_Rujukan,CASE WHEN receipt.receipt_type = 'Refund' THEN -receipt.receipt_content_sum ELSE receipt.receipt_content_sum END AS Bayaran_Amaun, bill.bill_generation_billable_sum_rm - util_rn_sum_payments.sum_receipts AS Baki_Bil_Belum_Selesai
	FROM (SELECT * FROM bill WHERE CAST(discharge_date AS DATE) >= billDischargeStartDateInclusive AND 
		CAST(discharge_date AS DATE) <= billDischargeEndDateInclusive AND (deleted IS NULL OR deleted <> 1)) AS bill
	INNER JOIN util_rn_sum_payments ON bill.rn = util_rn_sum_payments.rn
	INNER JOIN patient_admission ON patient_admission.rn = bill.rn 
	INNER JOIN patient_information ON patient_information.patient_uid = patient_admission.patient_uid
	LEFT JOIN lookup_ward ON patient_admission.initial_ward_code = lookup_ward.ward_code
	LEFT JOIN (SELECT * FROM receipt WHERE receipt_type IN ('Refund','Bill','Other') AND receipt_uid NOT IN (SELECT cancellation_uid FROM cancellation)) AS receipt ON bill.rn = receipt.rn
	LEFT JOIN (SELECT rn, GROUP_CONCAT(COALESCE(receipt_serial_number,'')) AS Deposit_nombor, GROUP_CONCAT(COALESCE(CAST(receipt_content_datetime_paid AS DATE),'')) AS Deposit_Tarikh_Resit, GROUP_CONCAT(COALESCE(receipt_content_sum,'0')) AS Deposit_Amaun FROM receipt WHERE receipt_type = 'Deposit' AND receipt_uid NOT IN (SELECT cancellation_uid FROM cancellation) GROUP BY rn) AS deposit ON bill.rn = deposit.rn
	ORDER BY bill.discharge_date, bill.rn, receipt.receipt_content_datetime_paid;
END$$

DELIMITER ;$$


DELIMITER $$;

DROP PROCEDURE IF EXISTS `sghis`.`reminder_batch_select`$$

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
				AND (CONVERT(bill.discharge_date,DATE) >= @r1MIN_DATE AND CONVERT(bill.discharge_date,DATE) <= @r1MAX_DATE)   	
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
				AND (CONVERT(bill.discharge_date,DATE) >= @r2MIN_DATE AND CONVERT(bill.discharge_date,DATE) <= @r2MAX_DATE)   	
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
				AND (CONVERT(bill.discharge_date,DATE) >= @r3MIN_DATE AND CONVERT(bill.discharge_date,DATE) <= @r3MAX_DATE)   	
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

DELIMITER ;$$
ALTER TABLE `cancellation` ADD `deleted_datetime` DATETIME NULL AFTER `responsible_uid`;

ALTER TABLE `cancellation` CHANGE `deleted_datetime` `deleted_datetime` DATETIME NOT NULL;

ALTER TABLE `new_user` CHANGE `retire` `retire` tinyint(1) NOT NULL DEFAULT 1;

REPLACE INTO `new_user` 
(`user_uid`, `username`, `user_password`, `role_cashier`, `role_clerk`, `role_admin`, `role_guest_print`, `Case_Note`, `Registration`, `Charge_Sheet`, `Sticker_Label`, `retire`, `authKey`) VALUES
('011BJIjHHpoDWrsDWRyk_dkHc2GUwDBG', 'administrator1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 0, 1, 0, NULL, NULL, NULL, NULL, 1, '12345b'),
('2wHPf777EC532SCrMDSR47dTw4nRqx2V', 'cashier1', '7b9efcfad5bc24b82b5acbe6175842f2', 1, 0, 0, 0, NULL, NULL, NULL, NULL, 1, '12345a'),
('3BUf9deDPpjBuaD7YO3_7vPrmxE4THBo', 'clerk1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 1, 0, 0, NULL, NULL, NULL, NULL, 1, '12345c'),
('iwJ4pQTEP0chTyfqzfr8KvpSo7XlMQ3S', 'guest_print1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 0, 0, 1, NULL, NULL, NULL, NULL, 1, 'pyLoI1aXGp7sAq72FW-D5u9RxSxub71p');

DROP PROCEDURE IF EXISTS `receipt_bill_procedure`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `receipt_bill_procedure`(IN `rn` VARCHAR(11))
BEGIN

   SELECT receipt.rn, receipt.receipt_content_datetime_paid, receipt.receipt_content_sum, receipt.receipt_type, 	receipt.receipt_content_payment_method, receipt.receipt_content_payer_name, receipt.receipt_serial_number, receipt.receipt_content_description, receipt.receipt_responsible FROM receipt WHERE receipt.rn = rn
    UNION
    SELECT bill.rn, bill.bill_generation_datetime, bill.bill_generation_billable_sum_rm, null, null, null, bill.bill_print_id, null, bill.generation_responsible_uid FROM bill WHERE bill.rn = rn AND bill.bill_generation_datetime is not null;

END$$
DELIMITER ;

ALTER TABLE `cancellation` ADD `deleted_datetime` DATETIME NULL AFTER `responsible_uid`;

ALTER TABLE `cancellation` CHANGE `deleted_datetime` `deleted_datetime` DATETIME NOT NULL;

ALTER TABLE `new_user` CHANGE `retire` `retire` tinyint(1) NOT NULL DEFAULT 1;

REPLACE INTO `new_user` 
(`user_uid`, `username`, `user_password`, `role_cashier`, `role_clerk`, `role_admin`, `role_guest_print`, `Case_Note`, `Registration`, `Charge_Sheet`, `Sticker_Label`, `retire`, `authKey`) VALUES
('011BJIjHHpoDWrsDWRyk_dkHc2GUwDBG', 'administrator1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 0, 1, 0, NULL, NULL, NULL, NULL, 1, '12345b'),
('2wHPf777EC532SCrMDSR47dTw4nRqx2V', 'cashier1', '7b9efcfad5bc24b82b5acbe6175842f2', 1, 0, 0, 0, NULL, NULL, NULL, NULL, 1, '12345a'),
('3BUf9deDPpjBuaD7YO3_7vPrmxE4THBo', 'clerk1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 1, 0, 0, NULL, NULL, NULL, NULL, 1, '12345c'),
('iwJ4pQTEP0chTyfqzfr8KvpSo7XlMQ3S', 'guest_print1', '7b9efcfad5bc24b82b5acbe6175842f2', 0, 0, 0, 1, NULL, NULL, NULL, NULL, 1, 'pyLoI1aXGp7sAq72FW-D5u9RxSxub71p');
ALTER TABLE `cancellation` ADD `deleted_datetime` DATETIME NULL AFTER `responsible_uid`;

ALTER TABLE `cancellation` CHANGE `deleted_datetime` `deleted_datetime` DATETIME NOT NULL;
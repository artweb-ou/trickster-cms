CREATE TABLE `engine_module_collection` (
	`id` INT(10) NOT NULL DEFAULT '0',
	`title` TEXT NOT NULL,
	`image` VARCHAR(255) NOT NULL DEFAULT '',
	`content` TEXT NOT NULL,
	`originalName` VARCHAR(255) NOT NULL DEFAULT '',
	`link` TEXT NOT NULL,
	`languageId` INT(11) NOT NULL,
	`priceSortingEnabled` TINYINT(1) NOT NULL DEFAULT '1',
	`nameSortingEnabled` TINYINT(1) NOT NULL DEFAULT '0',
	`introduction` TEXT NOT NULL,
	`metaTitle` TEXT NOT NULL,
	`metaDescription` TEXT NOT NULL,
	`canonicalUrl` VARCHAR(255) NOT NULL,
	`metaDenyIndex` TINYINT(1) NOT NULL,
	`availabilityFilterEnabled` TINYINT(1) NOT NULL DEFAULT '0',
	`parameterFilterEnabled` TINYINT(1) NOT NULL DEFAULT '0',
	`discountFilterEnabled` TINYINT(1) NOT NULL DEFAULT '0',
	`amountOnPageEnabled` TINYINT(1) NOT NULL,
	`dateSortingEnabled` TINYINT(1) NOT NULL,
	`categoryFilterEnable` TINYINT(1) NOT NULL,
	PRIMARY KEY (`id`, `languageId`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

CREATE TABLE `engine_module_collections_list` (
	`id` INT(10) NOT NULL DEFAULT '0',
	`title` TEXT NOT NULL,
	`columns` VARCHAR(10) NOT NULL,
	`languageId` INT(11) NOT NULL,
	`metaTitle` TEXT NOT NULL,
	`metaDescription` TEXT NOT NULL,
	`canonicalUrl` VARCHAR(255) NOT NULL,
	`metaDenyIndex` TINYINT(1) NOT NULL,
	`connectAll` TINYINT(1) NOT NULL,
	`content` LONGTEXT NOT NULL,
	`productsLayout` VARCHAR(20) NOT NULL,
	`collection` VARCHAR(20) NOT NULL,
	`h1` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`, `languageId`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

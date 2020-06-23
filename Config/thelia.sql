
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- address_colissimo_pickup_point
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `address_colissimo_pickup_point`;

CREATE TABLE `address_colissimo_pickup_point`
(
    `id` INTEGER NOT NULL,
    `title_id` INTEGER NOT NULL,
    `company` VARCHAR(255),
    `firstname` VARCHAR(255) NOT NULL,
    `lastname` VARCHAR(255) NOT NULL,
    `address1` VARCHAR(255) NOT NULL,
    `address2` VARCHAR(255) NOT NULL,
    `address3` VARCHAR(255) NOT NULL,
    `zipcode` VARCHAR(10) NOT NULL,
    `city` VARCHAR(255) NOT NULL,
    `country_id` INTEGER NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `type` VARCHAR(10) NOT NULL,
    `cellphone` VARCHAR(20),
    PRIMARY KEY (`id`),
    INDEX `FI_address_colissimo_pickup_point_customer_title_id` (`title_id`),
    INDEX `FI_address_colissimo_pickup_point_country_id` (`country_id`),
    CONSTRAINT `fk_address_colissimo_pickup_point_customer_title_id`
        FOREIGN KEY (`title_id`)
        REFERENCES `customer_title` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `fk_address_colissimo_pickup_point_country_id`
        FOREIGN KEY (`country_id`)
        REFERENCES `country` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- order_address_colissimo_pickup_point
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order_address_colissimo_pickup_point`;

CREATE TABLE `order_address_colissimo_pickup_point`
(
    `id` INTEGER NOT NULL,
    `code` VARCHAR(10) NOT NULL,
    `type` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_order_address_colissimo_pickup_point_order_address_id`
        FOREIGN KEY (`id`)
        REFERENCES `order_address` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- colissimo_pickup_point_price_slices
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `colissimo_pickup_point_price_slices`;

CREATE TABLE `colissimo_pickup_point_price_slices`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `area_id` INTEGER NOT NULL,
    `weight_max` FLOAT,
    `price_max` FLOAT,
    `franco_min_price` FLOAT,
    `price` FLOAT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `FI_colissimo_pickup_point_price_slices_area_id` (`area_id`),
    CONSTRAINT `fk_colissimo_pickup_point_price_slices_area_id`
        FOREIGN KEY (`area_id`)
        REFERENCES `area` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- colissimo_pickup_point_freeshipping
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `colissimo_pickup_point_freeshipping`;

CREATE TABLE `colissimo_pickup_point_freeshipping`
(
    `id` INTEGER NOT NULL,
    `active` TINYINT(1) DEFAULT 0,
    `freeshipping_from` DECIMAL(18,2),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- colissimo_pickup_point_area_freeshipping
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `colissimo_pickup_point_area_freeshipping`;

CREATE TABLE `colissimo_pickup_point_area_freeshipping`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `area_id` INTEGER NOT NULL,
    `cart_amount` DECIMAL(18,2) DEFAULT 0.00,
    PRIMARY KEY (`id`),
    INDEX `FI_colissimo_pickup_point_area_freeshipping_pr_area_id` (`area_id`),
    CONSTRAINT `fk_colissimo_pickup_point_area_freeshipping_pr_area_id`
        FOREIGN KEY (`area_id`)
        REFERENCES `area` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;

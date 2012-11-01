/**
 * PHP Store Locator
 *
 * Copyright (c) 2012 Brett O'Donnell <brett@mrphp.com.au>
 * Source Code: https://github.com/cornernote/php-store-locator
 * Home Page: http://mrphp.com.au/blog/php-store-locator
 * License: GPLv3
 */

CREATE TABLE `custom_location_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(64) NOT NULL,
  `state` varchar(16) NOT NULL,
  `zipcode` varchar(8) NOT NULL,
  `country` varchar(8) NOT NULL,
  `lat` decimal(10,6) NOT NULL,
  `lon` decimal(10,6) NOT NULL,
  PRIMARY KEY (`id`)
);
CREATE TABLE `custom_store_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(8) NOT NULL,
  `zipcode` varchar(8) NOT NULL,
  `country` varchar(255) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `status` char(1) NOT NULL,
  `lat` decimal(10,6) NOT NULL,
  `lon` decimal(10,6) NOT NULL,
  PRIMARY KEY (`id`)
);
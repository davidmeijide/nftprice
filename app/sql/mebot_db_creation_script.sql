-- ----------------------------------------------------------------------------
-- MySQL Workbench Migration
-- Migrated Schemata: mebot2
-- Source Schemata: mebot
-- Created: Fri Jul 22 20:57:11 2022
-- Workbench Version: 8.0.25
-- ----------------------------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- Schema mebot2
-- ----------------------------------------------------------------------------
DROP SCHEMA IF EXISTS `mebot2` ;
CREATE SCHEMA IF NOT EXISTS `mebot2` ;

-- ----------------------------------------------------------------------------
-- Table mebot2.alerts
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mebot2`.`alerts` (
  `id_alert` INT NOT NULL AUTO_INCREMENT,
  `floor_price` DECIMAL(7,2) NOT NULL,
  `symbol` VARCHAR(45) NOT NULL,
  `expiry_date` DATETIME NULL DEFAULT NULL,
  `fk_username` VARCHAR(45) NULL DEFAULT NULL,
  `executed` TINYINT NOT NULL,
  PRIMARY KEY (`id_alert`),
  INDEX `fk_alerts_username_idx` (`fk_username` ASC) VISIBLE,
  CONSTRAINT `fk_alerts_username`
    FOREIGN KEY (`fk_username`)
    REFERENCES `mebot2`.`users` (`username`)
    ON DELETE SET NULL
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

-- ----------------------------------------------------------------------------
-- Table mebot2.users
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mebot2`.`users` (
  `username` VARCHAR(45) NOT NULL,
  `password` VARCHAR(250) NOT NULL,
  `creation_date` DATETIME NULL DEFAULT NULL,
  `last_login` DATETIME NULL DEFAULT NULL,
  `telegram_id` VARCHAR(45) NULL DEFAULT NULL,
  `role` VARCHAR(45) NULL DEFAULT NULL,
  `email` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`username`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;
SET FOREIGN_KEY_CHECKS = 1;

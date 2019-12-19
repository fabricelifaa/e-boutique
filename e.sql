-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `e-com`.`delivery_cities`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `delivery_cities` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `Parent` INT NOT NULL,
  `label` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `e-com`.`delivery_companies`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `delivery_companies` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `company_name` VARCHAR(45) NULL,
  `address` VARCHAR(255) NULL,
  `phone` VARCHAR(45) NULL,
  `logo` VARCHAR(255) NULL,
  `email` VARCHAR(45) NULL,
  `commercial_register` VARCHAR(45) NULL,
  `ifu` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `e-com`.`pricing`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pricing` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `delivery_companies_id` INT NOT NULL,
  `delivery_cities_id` INT NOT NULL,
  `value` VARCHAR(45) NULL,
  PRIMARY KEY (`id`, `delivery_companies_id`, `delivery_cities_id`),
  INDEX `fk_delivery_companies_has_delivery_cities_delivery_cities1_idx` (`delivery_cities_id` ASC),
  INDEX `fk_delivery_companies_has_delivery_cities_delivery_companie_idx` (`delivery_companies_id` ASC),
  CONSTRAINT `fk_delivery_companies_has_delivery_cities_delivery_companies`
    FOREIGN KEY (`delivery_companies_id`)
    REFERENCES `delivery_companies` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_delivery_companies_has_delivery_cities_delivery_cities1`
    FOREIGN KEY (`delivery_cities_id`)
    REFERENCES `delivery_cities` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `e-com`.`delivery_delay`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `delivery_delay` (
  `id` INT NOT NULL,
  `delay` VARCHAR(45) NULL,
  `pricing_id` INT NOT NULL,
  `pricing_delivery_companies_id` INT NOT NULL,
  `pricing_delivery_cities_id` INT NOT NULL,
  PRIMARY KEY (`id`, `pricing_id`, `pricing_delivery_companies_id`, `pricing_delivery_cities_id`),
  INDEX `fk_delivery_delay_pricing1_idx` (`pricing_id` ASC, `pricing_delivery_companies_id` ASC, `pricing_delivery_cities_id` ASC),
  CONSTRAINT `fk_delivery_delay_pricing1`
    FOREIGN KEY (`pricing_id` , `pricing_delivery_companies_id` , `pricing_delivery_cities_id`)
    REFERENCES `pricing` (`id` , `delivery_companies_id` , `delivery_cities_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

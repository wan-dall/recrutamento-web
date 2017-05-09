-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema recrutamento-web
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `recrutamento-web` ;

-- -----------------------------------------------------
-- Schema recrutamento-web
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `recrutamento-web` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `recrutamento-web` ;

-- -----------------------------------------------------
-- Table `recrutamento-web`.`clubes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `recrutamento-web`.`clubes` ;

CREATE TABLE IF NOT EXISTS `recrutamento-web`.`clubes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `nome_UNIQUE` (`nome` ASC))
ENGINE = MyISAM;


-- -----------------------------------------------------
-- Table `recrutamento-web`.`socios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `recrutamento-web`.`socios` ;

CREATE TABLE IF NOT EXISTS `recrutamento-web`.`socios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(150) NOT NULL,
  `clube_id` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM;

SET SQL_MODE = '';
GRANT USAGE ON *.* TO recrutamento_web;
 DROP USER recrutamento_web;
SET SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';
CREATE USER 'recrutamento_web'@'localhost' IDENTIFIED BY '#php$passwd!';

GRANT ALL ON `recrutamento-web`.* TO 'recrutamento_web';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

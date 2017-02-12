SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `webdesk` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `webdesk` ;

-- -----------------------------------------------------
-- Table `webdesk`.`tenants`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`tenants` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`tenants` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `address_id` BIGINT(20) UNSIGNED NOT NULL ,
  `number_of_employees` VARCHAR(45) NULL ,
  `number_of_contractors` VARCHAR(45) NULL ,
  `contact_name` VARCHAR(255) NULL ,
  `contact_phone` VARCHAR(45) NULL ,
  `contact_email` VARCHAR(320) NULL ,
  `created_at` TIMESTAMP NULL ,
  `updated_at` TIMESTAMP NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`users` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`users` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` VARCHAR(255) NULL ,
  `password` VARCHAR(255) NULL ,
  `status` VARCHAR(32) NOT NULL ,
  `last_login_attempt` DATETIME NULL ,
  `failed_logins` INT NOT NULL DEFAULT 0 ,
  `created_at` TIMESTAMP NULL ,
  `remember_token` VARCHAR(100) NULL ,
  `updated_at` TIMESTAMP NULL ,
  `tenant_id` BIGINT(20) UNSIGNED NOT NULL ,
  `uuid` VARCHAR(255) NULL ,
  `profile_id` BIGINT(20) UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `token_idx` (`remember_token` ASC) ,
  INDEX `user_status` (`status` ASC) ,
  INDEX `user_instance` (`tenant_id` ASC) ,
  CONSTRAINT `user_tenant_id`
    FOREIGN KEY (`tenant_id` )
    REFERENCES `webdesk`.`tenants` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`lookup_address_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`lookup_address_types` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`lookup_address_types` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `key` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`lookup_address_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`lookup_address_status` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`lookup_address_status` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `key` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`addresses`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`addresses` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`addresses` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `street1` VARCHAR(255) NULL ,
  `street2` VARCHAR(255) NULL ,
  `city` VARCHAR(255) NULL ,
  `state` VARCHAR(255) NULL ,
  `postal` VARCHAR(45) NULL ,
  `country` VARCHAR(255) NULL ,
  `address_type` BIGINT(20) UNSIGNED NOT NULL DEFAULT 1 ,
  `status` BIGINT(20) UNSIGNED NOT NULL DEFAULT 1 ,
  `lat` INT NULL ,
  `lon` INT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `address_type_idx` (`address_type` ASC) ,
  INDEX `status_idx` (`status` ASC) ,
  INDEX `address_lat` (`lat` ASC) ,
  INDEX `address_lon` (`lon` ASC) ,
  CONSTRAINT `address_type`
    FOREIGN KEY (`address_type` )
    REFERENCES `webdesk`.`lookup_address_types` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `status`
    FOREIGN KEY (`status` )
    REFERENCES `webdesk`.`lookup_address_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`user_profiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`user_profiles` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`user_profiles` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'This table is deprecated. Remove after data is migrated.' ,
  `user_id` BIGINT(20) UNSIGNED NOT NULL ,
  `email` VARCHAR(255) NULL ,
  `address_id` BIGINT(20) UNSIGNED NULL ,
  `first_name` VARCHAR(255) NULL ,
  `last_name` VARCHAR(255) NULL ,
  `date_of_birth` DATE NOT NULL ,
  `gender` ENUM('M','F') NULL ,
  `daily_spending_limit` BIGINT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id_idx` (`user_id` ASC) ,
  CONSTRAINT `user_profiles_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `webdesk`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`lookup_user_status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`lookup_user_status` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`lookup_user_status` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `key` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`roles` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`roles` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `created_at` TIMESTAMP NULL ,
  `updated_at` TIMESTAMP NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`role_permissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`role_permissions` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`role_permissions` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` BIGINT(20) UNSIGNED NULL ,
  `permission` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`user_preferences`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`user_preferences` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`user_preferences` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` BIGINT(20) UNSIGNED NOT NULL ,
  `store_location_id` BIGINT(20) UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `user_prefs_user_id_idx` (`user_id` ASC) ,
  CONSTRAINT `user_prefs_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `webdesk`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`system_settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`system_settings` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`system_settings` (
  `id` BIGINT(20) UNSIGNED NOT NULL ,
  `motd` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`settings` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`settings` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `value` VARCHAR(2048) NOT NULL ,
  `tenant_id` BIGINT(20) UNSIGNED NULL ,
  `user_id` BIGINT(20) UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `setting_name` (`name` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`user_settings`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`user_settings` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`user_settings` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `setting_id` BIGINT(20) UNSIGNED NOT NULL ,
  `user_id` BIGINT(20) UNSIGNED NOT NULL ,
  `value` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `setting_id_fk_idx` (`setting_id` ASC) ,
  INDEX `user_id_fk_idx` (`user_id` ASC) ,
  CONSTRAINT `setting_id_fk`
    FOREIGN KEY (`setting_id` )
    REFERENCES `webdesk`.`settings` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `user_id_fk`
    FOREIGN KEY (`user_id` )
    REFERENCES `webdesk`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`user_subscriptions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`user_subscriptions` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`user_subscriptions` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '		' ,
  `user_id` BIGINT(20) UNSIGNED NOT NULL ,
  `subscription_id` BIGINT(20) UNSIGNED NOT NULL ,
  `created_at` DATETIME NOT NULL ,
  `ref_id` VARCHAR(20) NULL ,
  `arb_subscription_id` VARCHAR(16) NULL ,
  `canceled_at` DATETIME NULL ,
  `ends_at` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `usub_user_id_idx` (`user_id` ASC) ,
  CONSTRAINT `usub_user_id`
    FOREIGN KEY (`user_id` )
    REFERENCES `webdesk`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`days`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`days` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`days` (
  `day` DATETIME NOT NULL ,
  PRIMARY KEY (`day`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`resources`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`resources` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`resources` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(512) NOT NULL ,
  `base_rate` BIGINT(11) NULL ,
  `tenant_id` BIGINT(20) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`employees`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`employees` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`employees` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `first_name` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL ,
  `last_name` VARCHAR(45) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL ,
  `comp_amount` BIGINT(11) NOT NULL DEFAULT 0 ,
  `base_rate` BIGINT(11) NOT NULL DEFAULT 0 ,
  `email` VARCHAR(512) NULL ,
  `uuid` VARCHAR(128) NULL ,
  `external_id` VARCHAR(64) NULL ,
  `resource_id` BIGINT(20) UNSIGNED NULL ,
  `created_at` TIMESTAMP NULL ,
  `supervisor_email` VARCHAR(512) NULL ,
  `user_id` BIGINT(20) UNSIGNED NULL ,
  `full_time` TINYINT(1) NOT NULL DEFAULT 0 ,
  `daily_billable_hours` INT NOT NULL DEFAULT 8 ,
  `tenant_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  `department` VARCHAR(256) NULL ,
  `manager_id` BIGINT(20) UNSIGNED NULL ,
  `worker_type` ENUM('Employee', 'Part-time Employee','Contractor') NULL DEFAULT 'Employee' ,
  `comp_type` ENUM('Hourly','Annual','Monthly') NULL DEFAULT 'Hourly' ,
  `updated_at` TIMESTAMP NULL ,
  `job_title` VARCHAR(512) NULL ,
  `location` VARCHAR(512) NULL ,
  `hire_date` DATE NULL ,
  `personal_email` VARCHAR(512) NULL ,
  `status` ENUM('Active','Inactive') NOT NULL DEFAULT 'Active' ,
  `profile_image_id` BIGINT(20) UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `emp_extid` (`external_id` ASC) ,
  INDEX `emp_resource_idx` (`resource_id` ASC) ,
  INDEX `employee_tenant_id_idx` (`tenant_id` ASC) ,
  INDEX `emp_manager_id` (`manager_id` ASC) ,
  INDEX `emp_department` (`department` ASC) ,
  INDEX `emp_worker_type` (`worker_type` ASC) ,
  CONSTRAINT `emp_resource`
    FOREIGN KEY (`resource_id` )
    REFERENCES `webdesk`.`resources` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `employee_tenant_id`
    FOREIGN KEY (`tenant_id` )
    REFERENCES `webdesk`.`tenants` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`lookup_comp_types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`lookup_comp_types` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`lookup_comp_types` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `key` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`jira_employees`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`jira_employees` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`jira_employees` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `employee_id` BIGINT(20) UNSIGNED NOT NULL ,
  `jira_login` VARCHAR(256) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `je_eid_idx` (`employee_id` ASC) ,
  CONSTRAINT `je_eid`
    FOREIGN KEY (`employee_id` )
    REFERENCES `webdesk`.`employees` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`companies`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`companies` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`companies` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(256) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' NULL ,
  `uuid` VARCHAR(128) NULL ,
  `external_id` VARCHAR(64) NULL ,
  `address_id` BIGINT(20) UNSIGNED NULL ,
  `created_at` TIMESTAMP NULL ,
  `active` TINYINT(1) NOT NULL DEFAULT 1 ,
  `billing_email` VARCHAR(512) NULL ,
  `contact_name` VARCHAR(512) NULL ,
  `tenant_id` BIGINT(20) UNSIGNED NOT NULL ,
  `updated_at` TIMESTAMP NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `comp_extid` (`external_id` ASC) ,
  INDEX `company_active` (`active` ASC) ,
  INDEX `company_tenant_id_idx` (`tenant_id` ASC) ,
  CONSTRAINT `company_tenant_id`
    FOREIGN KEY (`tenant_id` )
    REFERENCES `webdesk`.`tenants` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`accounts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`accounts` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`accounts` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(512) NULL ,
  `company_id` BIGINT(20) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `accounts_company_id_idx` (`company_id` ASC) ,
  CONSTRAINT `accounts_company_id`
    FOREIGN KEY (`company_id` )
    REFERENCES `webdesk`.`companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`projects` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`projects` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(256) NULL ,
  `company_id` BIGINT(20) UNSIGNED NULL ,
  `account_id` BIGINT(20) UNSIGNED NULL ,
  `external_id` VARCHAR(64) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `project_company_id_idx` (`company_id` ASC) ,
  INDEX `project_account_id_idx` (`account_id` ASC) ,
  INDEX `project_extid` (`external_id` ASC) ,
  CONSTRAINT `project_company_id`
    FOREIGN KEY (`company_id` )
    REFERENCES `webdesk`.`companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `project_account_id`
    FOREIGN KEY (`account_id` )
    REFERENCES `webdesk`.`accounts` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`jira_companies`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`jira_companies` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`jira_companies` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `company_id` BIGINT(20) UNSIGNED NOT NULL ,
  `jira_company` VARCHAR(256) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `jccid_idx` (`company_id` ASC) ,
  CONSTRAINT `jccid`
    FOREIGN KEY (`company_id` )
    REFERENCES `webdesk`.`companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`jira_projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`jira_projects` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`jira_projects` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_id` BIGINT(20) UNSIGNED NOT NULL ,
  `jira_project` VARCHAR(256) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `jppid_idx` (`project_id` ASC) ,
  CONSTRAINT `jppid`
    FOREIGN KEY (`project_id` )
    REFERENCES `webdesk`.`projects` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`resource_rates`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`resource_rates` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`resource_rates` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `rate` BIGINT(11) NOT NULL ,
  `company_id` BIGINT(20) UNSIGNED NOT NULL ,
  `resource_id` BIGINT(20) UNSIGNED NOT NULL COMMENT 'The resource for which this rate applies' ,
  `description` VARCHAR(512) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `rr_cid_idx` (`company_id` ASC) ,
  INDEX `rr_resourceid_idx` (`resource_id` ASC) ,
  CONSTRAINT `rr_cid`
    FOREIGN KEY (`company_id` )
    REFERENCES `webdesk`.`companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `rr_resourceid`
    FOREIGN KEY (`resource_id` )
    REFERENCES `webdesk`.`resources` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`company_billing`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`company_billing` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`company_billing` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `company_id` BIGINT(20) UNSIGNED NOT NULL ,
  `amount` BIGINT(11) NOT NULL DEFAULT 0 ,
  `memo` VARCHAR(512) NULL ,
  `created_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `cbcid_idx` (`company_id` ASC) ,
  CONSTRAINT `cbcid`
    FOREIGN KEY (`company_id` )
    REFERENCES `webdesk`.`companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`billing_entries`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`billing_entries` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`billing_entries` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '		' ,
  `amount` INT(11) NOT NULL DEFAULT 0 COMMENT '							' ,
  `company_id` BIGINT(20) UNSIGNED NOT NULL ,
  `account_id` BIGINT(20) UNSIGNED NULL ,
  `description` VARCHAR(512) NULL ,
  `created_at` DATETIME NULL ,
  `invoice_id` BIGINT(20) UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `be_company_id_idx` (`company_id` ASC) ,
  INDEX `be_account_id_idx` (`account_id` ASC) ,
  CONSTRAINT `be_company_id`
    FOREIGN KEY (`company_id` )
    REFERENCES `webdesk`.`companies` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `be_account_id`
    FOREIGN KEY (`account_id` )
    REFERENCES `webdesk`.`accounts` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`timesheets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`timesheets` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`timesheets` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `employee_id` BIGINT(20) UNSIGNED NOT NULL ,
  `start_date` DATETIME NOT NULL ,
  `end_date` VARCHAR(45) NOT NULL ,
  `uuid` VARCHAR(128) NULL ,
  `status` ENUM('Open','Closed','Approved','Rejected') NULL ,
  `manager_id` BIGINT(20) UNSIGNED NULL ,
  `created_at` DATETIME NULL ,
  `updated_at` DATETIME NULL ,
  `status_date` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `ts_empid_idx` (`employee_id` ASC) ,
  INDEX `ts_status` (`status` ASC) ,
  CONSTRAINT `ts_empid`
    FOREIGN KEY (`employee_id` )
    REFERENCES `webdesk`.`employees` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`timesheet_entries`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`timesheet_entries` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`timesheet_entries` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `timesheet_id` BIGINT(20) UNSIGNED NOT NULL ,
  `hours` DECIMAL(8,2) NOT NULL ,
  `rate` INT(11) NOT NULL ,
  `project_id` BIGINT(20) UNSIGNED NOT NULL ,
  `day` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `te_timesheet_id_idx` (`timesheet_id` ASC) ,
  INDEX `te_project_id_idx` (`project_id` ASC) ,
  INDEX `te_day` (`day` ASC) ,
  CONSTRAINT `te_timesheet_id`
    FOREIGN KEY (`timesheet_id` )
    REFERENCES `webdesk`.`timesheets` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `te_project_id`
    FOREIGN KEY (`project_id` )
    REFERENCES `webdesk`.`projects` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`worklogs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`worklogs` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`worklogs` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `date_worked` DATETIME NOT NULL ,
  `hours` DECIMAL(8,2) NOT NULL ,
  `employee_id` BIGINT(20) UNSIGNED NOT NULL ,
  `description` LONGTEXT NULL ,
  `project_id` BIGINT(20) UNSIGNED NOT NULL ,
  `task` VARCHAR(1024) NULL ,
  `external_id` VARCHAR(256) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `wl_project_id_idx` (`project_id` ASC) ,
  INDEX `wl_empid_idx` (`employee_id` ASC) ,
  CONSTRAINT `wl_project_id`
    FOREIGN KEY (`project_id` )
    REFERENCES `webdesk`.`projects` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `wl_empid`
    FOREIGN KEY (`employee_id` )
    REFERENCES `webdesk`.`employees` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`budget_requests`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`budget_requests` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`budget_requests` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `created_at` DATETIME NULL ,
  `approved_at` DATETIME NULL ,
  `approved_by` VARCHAR(512) NULL ,
  `user_id` BIGINT(20) UNSIGNED NOT NULL ,
  `amount` BIGINT(11) NOT NULL ,
  `description` MEDIUMTEXT NULL ,
  `summary` VARCHAR(1024) NOT NULL ,
  `project_id` BIGINT(20) UNSIGNED NOT NULL ,
  `updated_at` DATETIME NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`role_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`role_user` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`role_user` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` BIGINT(20) UNSIGNED NOT NULL ,
  `user_id` BIGINT(20) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `role_user` (`user_id` ASC) ,
  INDEX `role_role` (`role_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`permissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`permissions` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`permissions` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` BIGINT(20) UNSIGNED NOT NULL ,
  `type` VARCHAR(255) NOT NULL ,
  `action` VARCHAR(255) NOT NULL ,
  `resource` VARCHAR(255) NOT NULL ,
  `created_at` TIMESTAMP NULL ,
  `updated_at` TIMESTAMP NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `perm_user` (`user_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`user_tokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`user_tokens` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`user_tokens` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` BIGINT(20) UNSIGNED NOT NULL ,
  `token` VARCHAR(255) NOT NULL ,
  `token_type` ENUM('remember','activation','api','pwreset') NOT NULL ,
  `created_at` TIMESTAMP NULL ,
  `updated_at` TIMESTAMP NULL ,
  `expires_at` TIMESTAMP NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `tokens_userid_idx` (`user_id` ASC) ,
  INDEX `tokens_token` (`token` ASC) ,
  INDEX `tokens_type` (`token_type` ASC) ,
  CONSTRAINT `tokens_userid`
    FOREIGN KEY (`user_id` )
    REFERENCES `webdesk`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`employee_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`employee_history` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`employee_history` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `employee_id` BIGINT(20) UNSIGNED NOT NULL ,
  `hist_status` ENUM('Hired','Terminated') NOT NULL ,
  `notes` MEDIUMTEXT NULL ,
  `created_at` TIMESTAMP NULL ,
  `updated_at` TIMESTAMP NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`files`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`files` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`files` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `filename` VARCHAR(255) NOT NULL ,
  `path` VARCHAR(1024) NOT NULL ,
  `created_at` DATETIME NULL ,
  `updated_at` DATETIME NULL ,
  `created_by` BIGINT(20) UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `files_createdby_idx` (`created_by` ASC) ,
  CONSTRAINT `files_createdby`
    FOREIGN KEY (`created_by` )
    REFERENCES `webdesk`.`users` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`employee_files`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`employee_files` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`employee_files` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `file_id` BIGINT(20) UNSIGNED NOT NULL ,
  `employee_id` BIGINT(20) UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NULL ,
  `description` MEDIUMTEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `emp_files_fileid_idx` (`file_id` ASC) ,
  INDEX `emp_files_empid_idx` (`employee_id` ASC) ,
  CONSTRAINT `emp_files_fileid`
    FOREIGN KEY (`file_id` )
    REFERENCES `webdesk`.`files` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `emp_files_empid`
    FOREIGN KEY (`employee_id` )
    REFERENCES `webdesk`.`employees` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`images`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`images` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`images` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `file_id` BIGINT(20) UNSIGNED NOT NULL ,
  `height` INT NOT NULL ,
  `width` INT NOT NULL ,
  `name` VARCHAR(255) NULL ,
  `description` MEDIUMTEXT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `image_file_id_idx` (`file_id` ASC) ,
  CONSTRAINT `image_file_id`
    FOREIGN KEY (`file_id` )
    REFERENCES `webdesk`.`files` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `webdesk`.`profiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `webdesk`.`profiles` ;

CREATE  TABLE IF NOT EXISTS `webdesk`.`profiles` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'This table is deprecated. Remove after data is migrated.' ,
  `email` VARCHAR(255) NULL ,
  `address_id` BIGINT(20) UNSIGNED NULL ,
  `first_name` VARCHAR(255) NULL ,
  `last_name` VARCHAR(255) NULL ,
  `date_of_birth` DATE NOT NULL ,
  `gender` ENUM('M','F') NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;

USE `webdesk` ;

-- -----------------------------------------------------
-- procedure filldates
-- -----------------------------------------------------

USE `webdesk`;
DROP procedure IF EXISTS `webdesk`.`filldates`;

DELIMITER $$
USE `webdesk`$$
CREATE PROCEDURE webdesk.filldates(dateStart DATE, dateEnd DATE)
BEGIN
  WHILE dateStart <= dateEnd DO
    INSERT INTO days (day) VALUES (dateStart);
    SET dateStart = date_add(dateStart, INTERVAL 1 DAY);
  END WHILE;
END;
$$

DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `webdesk`.`lookup_address_types`
-- -----------------------------------------------------
START TRANSACTION;
USE `webdesk`;
INSERT INTO `webdesk`.`lookup_address_types` (`id`, `name`, `key`) VALUES (1, 'Residential', 'RESIDENTIAL');
INSERT INTO `webdesk`.`lookup_address_types` (`id`, `name`, `key`) VALUES (2, 'Commercial', 'COMMERCIAL');

COMMIT;

-- -----------------------------------------------------
-- Data for table `webdesk`.`lookup_address_status`
-- -----------------------------------------------------
START TRANSACTION;
USE `webdesk`;
INSERT INTO `webdesk`.`lookup_address_status` (`id`, `name`, `key`) VALUES (1, 'Active', 'ACTIVE');
INSERT INTO `webdesk`.`lookup_address_status` (`id`, `name`, `key`) VALUES (2, 'Disabled', NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `webdesk`.`lookup_user_status`
-- -----------------------------------------------------
START TRANSACTION;
USE `webdesk`;
INSERT INTO `webdesk`.`lookup_user_status` (`id`, `name`, `key`) VALUES (1, 'Active', 'ACTIVE');
INSERT INTO `webdesk`.`lookup_user_status` (`id`, `name`, `key`) VALUES (2, 'Disabled', 'DISABLED');

COMMIT;

-- -----------------------------------------------------
-- Data for table `webdesk`.`system_settings`
-- -----------------------------------------------------
START TRANSACTION;
USE `webdesk`;
INSERT INTO `webdesk`.`system_settings` (`id`, `motd`) VALUES (1, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `webdesk`.`lookup_comp_types`
-- -----------------------------------------------------
START TRANSACTION;
USE `webdesk`;
INSERT INTO `webdesk`.`lookup_comp_types` (`id`, `name`, `key`) VALUES (1, 'Hourly', 'HOURLY');
INSERT INTO `webdesk`.`lookup_comp_types` (`id`, `name`, `key`) VALUES (2, 'Annual', 'ANNUAL');

COMMIT;

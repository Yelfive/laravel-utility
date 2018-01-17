<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2018-01-15
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InitAclMigration extends Migration
{

    public function up()
    {
        $prefix = DB::getTablePrefix();
        DB::statement(<<<SQL
CREATE TABLE IF NOT EXISTS `{$prefix}admin` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(20) NOT NULL COMMENT 'Username to login with',
	`nickname` VARCHAR(50) NOT NULL COMMENT 'Display name',
	`type` TINYINT UNSIGNED NOT NULL COMMENT '0=root;1=platform admin;2=company admin',
	`password_hash` VARCHAR(64) NOT NULL COMMENT 'Hashed password',
	`role_id` INT UNSIGNED NOT NULL COMMENT 'role.id',
	`created_by` INT UNSIGNED NOT NULL COMMENT 'Creator id, which is also in this table',
	`updated_by` INT UNSIGNED NOT NULL COMMENT 'Updater id, which is also in this table',
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`deleted` ENUM('no','yes') NOT NULL DEFAULT 'no',
	`disabled` ENUM('no', 'yes') NOT NULL DEFAULT 'no',
	PRIMARY KEY (`id`) ,
	UNIQUE INDEX `username_unq` (`username` ASC) USING HASH
) COMMENT "Table of administrators";
SQL
        );

        DB::statement(<<<SQL
CREATE TABLE IF NOT EXISTS `{$prefix}admin_role` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`role_name` VARCHAR(255) NOT NULL COMMENT 'Name of the role',
	`privileges` TEXT NOT NULL COMMENT 'Routes the role can access',
	`created_by` INT UNSIGNED NOT NULL COMMENT 'Creator id, which is in table admin',
	`updated_by` INT UNSIGNED NOT NULL COMMENT 'Updater id, which is in table admin',
	`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`deleted` ENUM('no','yes') NOT NULL DEFAULT 'no',
	PRIMARY KEY (`id`)
) COMMENT "Table of administrator role, which defines its access";
SQL
        );
    }

    public function down()
    {
        $prefix = DB::getTablePrefix();
        DB::statement(<<<SQL
DROP TABLE IF EXISTS `{$prefix}admin`;
DROP TABLE IF EXISTS `{$prefix}admin_role`;
SQL
        );
    }
}
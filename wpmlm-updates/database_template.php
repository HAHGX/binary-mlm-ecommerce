<?php
/**
 * WP eCommerce Database template
 *
 * This is the WPMLM database template it is a multidimensional associative array used to create and update the database tables.
 * @package wp-e-commerce
 * @subpackage wpmlm-updating-code 
 */
 
// code to create or update the {$wpdb->prefix}wpmlm_also_bought table

//Code for create table "{$wp_table_prefix}wpmlm_users"
$table_name = WPMLM_TABLE_USER;
$wpmlm_database_template[$table_name]['columns']['id'] = "bigint(20) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['user_id'] = "BIGINT(20) NOT NULL COMMENT 'foreign key of the users table'";
$wpmlm_database_template[$table_name]['columns']['user_key'] = "VARCHAR( 15 ) NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['parent_key'] = "VARCHAR( 15 ) NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['sponsor_key'] = "VARCHAR( 15 ) NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['leg'] = "ENUM(  '1',  '0' ) NOT NULL DEFAULT '0' COMMENT '1 indicate right leg and 0 indicate left leg'";
$wpmlm_database_template[$table_name]['columns']['payment_status'] = "enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0-Subscriber, 1- below criteria, 2- fulfilled the criteria'";
$wpmlm_database_template[$table_name]['columns']['banned'] = "enum('1','0') NOT NULL DEFAULT '1' COMMENT '1-Inactive and 0-Active'";
$wpmlm_database_template[$table_name]['columns']['qualification_pv'] = "int(11) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['left_pv'] = "float NOT NULL"; 
$wpmlm_database_template[$table_name]['columns']['right_pv'] = "float NOT NULL"; 
$wpmlm_database_template[$table_name]['columns']['own_pv'] = "float NOT NULL"; 
$wpmlm_database_template[$table_name]['columns']['create_date'] = "datetime NOT NULL"; 
$wpmlm_database_template[$table_name]['columns']['paid_date'] = "datetime NOT NULL"; 
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";

//Code for create table {$wp_table_prefix}wpmlm_leftleg"
$table_name = WPMLM_TABLE_LEFT_LEG;
$wpmlm_database_template[$table_name]['columns']['id'] = "bigint(20) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['pkey'] = "VARCHAR( 15 ) NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['ukey'] = "VARCHAR( 15 ) NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";

//Code for create table {$wp_table_prefix}wpmlm_rightleg"
$table_name = WPMLM_TABLE_RIGHT_LEG;
$wpmlm_database_template[$table_name]['columns']['id'] = "bigint(20) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['pkey'] = "VARCHAR( 15 ) NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['ukey'] = "VARCHAR( 15 ) NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";

//Code for create table "{$wp_table_prefix}wpmlm_wpmlm_pv_transaction"
$table_name = WPMLM_TABLE_PV_TRANSACTION;
$wpmlm_database_template[$table_name]['columns']['id'] = "bigint(20) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['pkey'] = "varchar(15) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['ukey'] = "varchar(10) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['opening_left'] = "float NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['opening_right'] = "float NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['closing_left'] = "float NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['closing_right'] = "float NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['debit_left'] = "float NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['debit_right'] = "float NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['credit_left'] = "float NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['credit_right'] = "float NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['payout_id'] = "int(11) NOT NULL COMMENT 'Foreign Key to mlm_payout_master'";
$wpmlm_database_template[$table_name]['columns']['date'] = "date NOT NULL";
$wpmlm_database_template[$table_name]['columns']['status'] = "enum('0','1') NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";

//Code for create table "{$wp_table_prefix}wpmlm_mlm_payout"
$table_name = WPMLM_TABLE_PAYOUT;
$wpmlm_database_template[$table_name]['columns']['id'] = "int(10) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['userid'] = "bigint(20) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['date'] = "date NOT NULL";
$wpmlm_database_template[$table_name]['columns']['payout_id'] = "int(11) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['units'] = "int(25) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['commission_amount'] = "double(10,2) DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['bonus_amount'] = "double(10,2) DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['neft_code'] = "varchar(10) DEFAULT NULL";
$wpmlm_database_template[$table_name]['columns']['cheque_no'] = "varchar(10) DEFAULT NULL";
$wpmlm_database_template[$table_name]['columns']['cheque_date'] = "date DEFAULT NULL";
$wpmlm_database_template[$table_name]['columns']['bank_name'] = "varchar(50) DEFAULT NULL";
$wpmlm_database_template[$table_name]['columns']['user_bank_name'] = "varchar(50) DEFAULT NULL";
$wpmlm_database_template[$table_name]['columns']['user_bank_account_no'] = "varchar(10) DEFAULT NULL";
$wpmlm_database_template[$table_name]['columns']['tds'] = "double(10,2) DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['service_charge'] = "double(10,2) DEFAULT '0'";
$wpmlm_database_template[$table_name]['columns']['dispatch_date'] = "date DEFAULT NULL";
$wpmlm_database_template[$table_name]['columns']['courier_name'] = "varchar(20) DEFAULT NULL";
$wpmlm_database_template[$table_name]['columns']['awb_no'] = "varchar(20) DEFAULT NULL";
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";

//Code for create table "{$wp_table_prefix}wpmlm_mlm_payout"
$table_name = WPMLM_TABLE_PAYOUT_MASTER;
$wpmlm_database_template[$table_name]['columns']['id'] = "int(10) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['date'] = "date NOT NULL";
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";

//Code for create table "{$wp_table_prefix}wpmlm_bonus"
$table_name = WPMLM_TABLE_BONUS;
$wpmlm_database_template[$table_name]['columns']['id'] = "int(10) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['units'] = "int(11) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['amount'] = "float NOT NULL";
$wpmlm_database_template[$table_name]['columns']['creationdate'] = "datetime NOT NULL";
$wpmlm_database_template[$table_name]['columns']['lastupdate'] = "datetime NOT NULL";
$wpmlm_database_template[$table_name]['columns']['status'] = "char(1) NOT NULL COMMENT '0-Active, 1-Inactive'";
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";

//Code for create table "{$wp_table_prefix}wpmlm_bonus"
$table_name = WPMLM_TABLE_BONUS_PAYOUT;
$wpmlm_database_template[$table_name]['columns']['id'] = "int(10) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['user_id'] = "int(11) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['bonus_id'] = "int(11) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['amount'] = "float NOT NULL";
$wpmlm_database_template[$table_name]['columns']['payout_id'] = "int(11) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['date'] = "datetime NOT NULL";
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";



$table_name = WPMLM_TABLE_COUNTRY; /* !wpmlm_currency_list */
$wpmlm_database_template[$table_name]['columns']['id'] = "bigint(20) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['country'] = "varchar(255) NOT NULL DEFAULT '' ";
$wpmlm_database_template[$table_name]['columns']['isocode'] = "char(2) NULL DEFAULT '' ";
$wpmlm_database_template[$table_name]['columns']['currency'] = "varchar(255) NOT NULL DEFAULT '' ";
$wpmlm_database_template[$table_name]['columns']['symbol'] = "varchar(10) NOT NULL DEFAULT '' ";
$wpmlm_database_template[$table_name]['columns']['symbol_html'] = "varchar(10) NOT NULL DEFAULT '' ";
$wpmlm_database_template[$table_name]['columns']['code'] = "char(3) NOT NULL DEFAULT '' ";
$wpmlm_database_template[$table_name]['columns']['has_regions'] = "char(1) NOT NULL DEFAULT '0' ";
$wpmlm_database_template[$table_name]['columns']['tax'] = "varchar(8) NOT NULL DEFAULT '' ";
$wpmlm_database_template[$table_name]['columns']['continent'] = "varchar(20) NOT NULL DEFAULT '' ";
$wpmlm_database_template[$table_name]['columns']['visible'] = "varchar(1) NOT NULL DEFAULT '1' ";
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";
$wpmlm_database_template[$table_name]['actions']['after']['all'] = "wpmlm_add_currency_list";
$wpmlm_database_template[$table_name]['previous_names'] = "{$wpdb->prefix}currency_list";


$table_name = WPMLM_TABLE_PV_DETAIL; /* !wpmlm_currency_list */
$wpmlm_database_template[$table_name]['columns']['id'] = "int(11) unsigned NOT NULL auto_increment";
$wpmlm_database_template[$table_name]['columns']['order_id'] = "int(11) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['user_id'] = "int(11) NULL ";
$wpmlm_database_template[$table_name]['columns']['total_amount'] = "int(11) NOT NULL";
$wpmlm_database_template[$table_name]['columns']['total_pv'] = "int(11) NOT NULL ";
$wpmlm_database_template[$table_name]['columns']['status'] = "int(11) NOT NULL DEFAULT '0'";
$wpmlm_database_template[$table_name]['indexes']['PRIMARY'] = "PRIMARY KEY  ( `id` )";
$wpmlm_database_template[$table_name]['indexes']['UNIQUE'] = "UNIQUE KEY  ( `order_id` )";




?>
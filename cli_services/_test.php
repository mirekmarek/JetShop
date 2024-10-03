<?php

/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


require __DIR__.'/../application/bootstrap_cli_service.php';

$shop = Shops::get('cz_cs_CZ');

$invoice = Invoice::get(8);
$invoice->recalculate();
$invoice->save();

/*
$start_date = '2024-08-13';

var_dump( Calendar::getNextBusinessDate( $shop, 1, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $shop, 2, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $shop, 3, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $shop, 4, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $shop, 5, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $shop, 6, $start_date )->format('Y-m-d') );
*/

/*
$start_date = '2024-08-26';

var_dump( Calendar::getPrevBusinessDate( $shop, 1, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $shop, 2, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $shop, 3, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $shop, 4, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $shop, 5, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $shop, 6, $start_date )->format('Y-m-d') );
*/

var_dump( Calendar::howManyWorkingDays( $shop, '2024-08-13', '2024-08-26' ) );


/*
$order = Order::getByNumber('CZ202407000016', Shops::get('cz_cs_CZ'));

WarehouseManagement::manageOrderUpdated( $order );
*/
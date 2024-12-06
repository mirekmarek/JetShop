<?php

/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


require __DIR__.'/../application/bootstrap_cli_service.php';

$eshop = EShops::get('cz_cs_CZ');

$invoice = Invoice::get(8);
$invoice->recalculate();
$invoice->save();

/*
$start_date = '2024-08-13';

var_dump( Calendar::getNextBusinessDate( $eshop, 1, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $eshop, 2, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $eshop, 3, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $eshop, 4, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $eshop, 5, $start_date )->format('Y-m-d') );
var_dump( Calendar::getNextBusinessDate( $eshop, 6, $start_date )->format('Y-m-d') );
*/

/*
$start_date = '2024-08-26';

var_dump( Calendar::getPrevBusinessDate( $eshop, 1, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $eshop, 2, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $eshop, 3, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $eshop, 4, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $eshop, 5, $start_date )->format('Y-m-d') );
var_dump( Calendar::getPrevBusinessDate( $eshop, 6, $start_date )->format('Y-m-d') );
*/

var_dump( Calendar::howManyWorkingDays( $eshop, '2024-08-13', '2024-08-26' ) );


/*
$order = Order::getByNumber('CZ202407000016', Shops::get('cz_cs_CZ'));

WarehouseManagement::manageOrderUpdated( $order );
*/
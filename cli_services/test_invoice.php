<?php

/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


require __DIR__.'/../application/bootstrap_cli_service.php';

$order = Order::get( 225 );

/*
$inv = Invoice::createByOrder( $order );

var_dump($inv->getTotalWithoutVat());
var_dump($inv->getTotalVat());
var_dump($inv->getTotalRound());
var_dump($inv->getTotal());

var_dump($inv->getVATOverview());

$inv->save();
*/

/*
$inv = Invoice::get( 1 );

$correction_inv = $inv->prepareCorrectionInvoice();
$correction_inv->setCorrectionReason( 'test test test test test test test test test test test test test test test test test test test test test test test' );

$correction_inv->save();
*/

/*
$inv = InvoiceInAdvance::createByOrder( $order );

var_dump($inv->getTotalWithoutVat());
var_dump($inv->getTotalVat());
var_dump($inv->getTotalRound());
var_dump($inv->getTotal());

var_dump($inv->getVATOverview());

$inv->save();
*/

$inv = DeliveryNote::createByOrder( $order );

var_dump($inv->getTotalWithoutVat());
var_dump($inv->getTotalVat());
var_dump($inv->getTotalRound());
var_dump($inv->getTotal());

var_dump($inv->getVATOverview());

$inv->save();

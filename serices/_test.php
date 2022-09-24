<?php
namespace JetShop;

require __DIR__.'/../application/bootstrap_service.php';

//$customer = Customer::get(1);

//$customer->changeEmail('mirek.marek@web-jet.cz', 'test', 'test comment');

//$customer->mailingSubscribe('order:'.time());


Shops::setCurrent( Shops::get('cz_cs_CZ'), true );

$category = Category::get(1861);

var_dump( $category->getProductListing()->getAutoAppendProductIds() );
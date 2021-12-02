<?php
namespace JetShop;

require __DIR__.'/../../application/bootstrap_service.php';

$exports = Exports::getActiveModules();

foreach($exports as $export) {
	foreach( Shops::getList() as $shop ) {

		if($export->isAllowedForShop($shop)) {
			echo $export->getCode().' - '.$shop->getShopName().''.PHP_EOL;
			$export->generateExports( $shop );
		}
	}
}

<?php
namespace JetShop;

require __DIR__.'/../../../../application/bootstrap_service.php';

foreach( Shops::getList() as $shop ) {
	echo $shop->getName().':'.PHP_EOL;
	Delivery_PersonalTakeover::actualizePlaces( $shop, true );
}

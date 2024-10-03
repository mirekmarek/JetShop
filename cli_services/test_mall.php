<?php
namespace JetApplication;

use JetApplicationModule\MarketplaceIntegration\Mall\Main as Mall;

require __DIR__.'/../application/bootstrap_cli_service.php';

$shop = Shops::get('cz_cs_CZ');

/**
 * @var Mall $mall
 */
$mall = MarketplaceIntegration::getActiveModule('Mall');
//$mall->actualizeCategories( $shop );
//$mall->actualizeCategory( $shop, 'EA007' );

var_dump( $mall->productToData(
	$shop,
	60777,
	$shop->getDefaultPricelist(),
	$shop->getDefaultAvailability()
) );


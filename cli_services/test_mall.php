<?php
namespace JetApplication;

use JetApplicationModule\MarketplaceIntegration\Mall\Main as Mall;

require __DIR__.'/../application/bootstrap_cli_service.php';

$eshop = EShops::get('cz_cs_CZ');

/**
 * @var Mall $mall
 */
$mall = MarketplaceIntegration::getActiveModule('Mall');
//$mall->actualizeCategories( $eshop );
//$mall->actualizeCategory( $eshop, 'EA007' );

var_dump( $mall->productToData(
	$eshop,
	60777,
	$eshop->getDefaultPricelist(),
	$eshop->getDefaultAvailability()
) );


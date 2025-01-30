<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasActivation_Interface;
use JetApplication\EShopEntity_HasActivation_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasInternalParams_Interface;
use JetApplication\EShopEntity_HasInternalParams_Trait;

#[DataModel_Definition]
abstract class Core_EShopEntity_Common extends EShopEntity_Basic implements
	EShopEntity_HasInternalParams_Interface,
	EShopEntity_HasGet_Interface,
	EShopEntity_HasActivation_Interface
{
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasInternalParams_Trait;
	use EShopEntity_HasActivation_Trait;
	
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\EShopEntity_Address;
use JetApplication\EShop;

abstract class Core_Admin_Managers_Customer extends Admin_EntityManager_Module
{
	abstract public function formatAddress( EShop $eshop, EShopEntity_Address $address ) : string;
}
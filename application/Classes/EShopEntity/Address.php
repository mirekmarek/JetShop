<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\DataModel_Definition;
use JetShop\Core_EShopEntity_Address;

#[DataModel_Definition(
	name: 'address',
	database_table_name: 'address',
)]
class EShopEntity_Address extends Core_EShopEntity_Address
{
}
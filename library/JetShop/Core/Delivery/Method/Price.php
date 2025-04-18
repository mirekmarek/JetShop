<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;
use JetApplication\EShopEntity_Price;

#[DataModel_Definition(
	name: 'delivery_methods_price',
	database_table_name: 'delivery_methods_price'
)]
abstract class Core_Delivery_Method_Price extends EShopEntity_Price
{
}
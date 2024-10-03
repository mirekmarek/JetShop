<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Entity_Price;

#[DataModel_Definition(
	name: 'deliver_methods_price',
	database_table_name: 'deliver_methods_price'
)]
abstract class Core_Delivery_Method_Price extends Entity_Price
{
}
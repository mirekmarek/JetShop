<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Entity_Price;

#[DataModel_Definition(
	name: 'payment_methods_price',
	database_table_name: 'payment_methods_price'
)]
abstract class Core_Payment_Method_Price extends Entity_Price
{
}
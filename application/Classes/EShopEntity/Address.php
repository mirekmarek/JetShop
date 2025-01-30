<?php
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
<?php
namespace JetApplication;

use Jet\DataModel_Definition;
use JetShop\Core_Entity_Address;

#[DataModel_Definition(
	name: 'address',
	database_table_name: 'address',
)]
class Entity_Address extends Core_Entity_Address
{
}
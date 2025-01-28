<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Entity_Basic;
use JetApplication\Entity_HasEShopRelation_Interface;
use JetApplication\Entity_HasEShopRelation_Trait;

#[DataModel_Definition]
abstract class Core_Entity_WithEShopRelation extends Entity_Basic implements
	Entity_HasEShopRelation_Interface
{
	use Entity_HasEShopRelation_Trait;
}

<?php
namespace JetShop;

use Jet\DataModel_Definition;
use JetApplication\Entity_Basic;
use JetApplication\Entity_HasActivation_Interface;
use JetApplication\Entity_HasActivation_Trait;
use JetApplication\Entity_HasGet_Interface;
use JetApplication\Entity_HasGet_Trait;
use JetApplication\Entity_HasInternalParams_Interface;
use JetApplication\Entity_HasInternalParams_Trait;

#[DataModel_Definition]
abstract class Core_Entity_Common extends Entity_Basic implements
	Entity_HasInternalParams_Interface,
	Entity_HasGet_Interface,
	Entity_HasActivation_Interface
{
	use Entity_HasGet_Trait;
	use Entity_HasInternalParams_Trait;
	use Entity_HasActivation_Trait;
	
	
}
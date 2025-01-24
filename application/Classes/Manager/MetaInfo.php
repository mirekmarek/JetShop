<?php
namespace JetApplication;

use JetShop\Core_Manager_MetaInfo;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class Manager_MetaInfo extends Core_Manager_MetaInfo {

}
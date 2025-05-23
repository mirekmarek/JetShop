<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Content_MagicTag;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Magic tags',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_MagicTags extends Application_Module
{
	/**
	 * @return Content_MagicTag[]
	 */
	abstract public function getList() : array;
	
	abstract public function init() : void;
	
	abstract public function processText( string $text ) : string;
	
}
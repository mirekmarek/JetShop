<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Images',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_EShop_Managers_Image extends Application_Module
{
	abstract public function getUrl( string $image ) : string;
	
	abstract public function getThumbnailUrl( string $image, int $max_w, int $max_h ) : string;
}
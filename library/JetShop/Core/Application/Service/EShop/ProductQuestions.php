<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Application_Service_MetaInfo;
use JetApplication\Application_Service_EShop;
use JetApplication\Product_EShopData;

#[Application_Service_MetaInfo(
	group: Application_Service_EShop::GROUP,
	is_mandatory: false,
	name: 'Product questions',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class Core_Application_Service_EShop_ProductQuestions extends Application_Module
{
	abstract public function getQuestionCount( Product_EShopData $product ) : int;
	abstract public function renderQuestions( Product_EShopData $product ): string;
	
}
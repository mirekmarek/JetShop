<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Form;
use Jet\Application_Service_MetaInfo;
use JetApplication\Application_Service_Admin;
use JetApplication\ProductFilter;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: true,
	name: 'Product Filter',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_ProductFilter extends Application_Module
{
	abstract public function init( ProductFilter $filter ) : Form;
	
	abstract public function renderFilterForm() : string;
	
	abstract public function handleFilterForm() : bool;
	
	abstract public function getFilter() : ProductFilter;
	
}
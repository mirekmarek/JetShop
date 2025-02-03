<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Form;
use JetApplication\Manager_MetaInfo;
use JetApplication\ProductFilter;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Product Filter',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_ProductFilter extends Application_Module
{
	abstract public function init( ProductFilter $filter ) : Form;
	
	abstract public function renderFilterForm() : string;
	
	abstract public function handleFilterForm() : bool;
	
	abstract public function getFilter() : ProductFilter;
	
}
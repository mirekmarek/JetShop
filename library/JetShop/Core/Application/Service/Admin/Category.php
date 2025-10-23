<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form_Field_Hidden;
use JetApplication\Admin_EntityManager_Module;
use Jet\Application_Service_MetaInfo;
use JetApplication\Application_Service_Admin;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: true,
	name: 'Catalog - Categories',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_Category extends Admin_EntityManager_Module
{
	abstract public function renderSelectWidget( string $on_select,
	                                            int $selected_category_id=0,
	                                            ?bool $only_active_filter=null,
	                                            string $name='select_category' ) : string;
	
	abstract public function renderSelectCategoriesWidget( Form_Field_Hidden $input ) : string;
}
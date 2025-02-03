<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use JetApplication\Admin_EntityManager_Module;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - property',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_Property extends Admin_EntityManager_Module
{
	
	abstract public function showType( string $type ) : string;
	
	abstract public function renderSelectWidget( string $on_select,
	                                                   int $selected_property_id=0,
	                                                   ?string $only_type_filter=null,
	                                                   ?bool $only_active_filter=null,
	                                                   string $name='select_property' ) : string;
	
	abstract public function renderProductPropertyEditFormField(
		Form $form,
		int $property_id,
		string $form_field_name_prefix=''
	) : string;
}
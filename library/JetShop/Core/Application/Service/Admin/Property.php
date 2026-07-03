<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use JetApplication\Admin_EntityManager_Module;
use Jet\Application_Service_MetaInfo;
use JetApplication\Application_Service_Admin;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: true,
	name: 'Catalog - property',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_Property extends Admin_EntityManager_Module
{
	
	abstract public function showType( string $type ) : string;
	
	abstract public function renderProductPropertyEditFormField(
		Form $form,
		int $property_id,
		string $form_field_name_prefix=''
	) : string;
}
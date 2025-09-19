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

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: true,
	name: 'UI',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_UI extends Application_Module
{
	abstract public function handleCurrentPreferredShop();
	
	abstract public function renderSelectEntityWidget(
		string $name,
		string $caption,
		string $on_select,
		string $entity_type,
		string|array|null $object_type_filter,
		?bool $object_is_active_filter,
		?string $selected_entity_title,
		?string $selected_entity_edit_URL
	) : string;
	
	
	abstract public function renderEntityToolbar( Form $form, ?callable $buttons_renderer=null ) : string;
}
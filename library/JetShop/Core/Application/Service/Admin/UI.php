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
use Jet\Form_Field_Hidden;
use JetApplication\Application_Service_Admin;
use JetApplication\EShopEntity_Basic;

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
		string|EShopEntity_Basic $entity_class,
		string $name,
		string $on_select,
		string|EShopEntity_Basic|null $selected = null,
		string|array|null $object_type_filter = null,
		?bool $object_is_active_filter = null
	) : string;
	
	abstract public function renderSelectEntitiesWidget(
		string|EShopEntity_Basic $entity_class,
		Form_Field_Hidden $input,
		string|array|null $object_type_filter=null,
		?bool $object_is_active_filter=null,
	) : string;
	
	
	abstract public function renderEntityToolbar( Form $form, ?callable $buttons_renderer=null ) : string;
}
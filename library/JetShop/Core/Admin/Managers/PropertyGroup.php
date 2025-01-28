<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_Interface;

interface Core_Admin_Managers_PropertyGroup extends Admin_EntityManager_Interface
{
	
	public function renderSelectWidget(
                                     string $on_select,
                                     int $selected_property_group_id=0,
                                     ?bool $only_active_filter=null,
                                     string $name='select_property_group' ) : string;
}
<?php
namespace JetApplication;

interface Admin_Managers_PropertyGroup extends Admin_Entity_WithShopData_Manager_Interface
{
	
	public function renderSelectWidget(
                                     string $on_select,
                                     int $selected_property_group_id=0,
                                     ?bool $only_active_filter=null,
                                     string $name='select_property_group' ) : string;
}
<?php
namespace JetApplication;

interface Admin_Managers_PropertyGroup {
	
	public function renderSelectWidget(
                                     string $on_select,
                                     int $selected_property_group_id=0,
                                     ?bool $only_active_filter=null,
                                     string $name='select_property_group' ) : string;
	
	public function showName( int $id ) : string;
}
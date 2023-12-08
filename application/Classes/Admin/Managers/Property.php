<?php
namespace JetApplication;

interface Admin_Managers_Property {
	
	public function renderSelectWidget( string $on_select,
	                                                   int $selected_property_id=0,
	                                                   ?string $only_type_filter=null,
	                                                   ?bool $only_active_filter=null,
	                                                   string $name='select_property' ) : string;
	
	public function showName( int $id ) : string;
	
}
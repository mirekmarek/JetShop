<?php
namespace JetApplication;

use Jet\Form;

interface Admin_Managers_Property extends Admin_Entity_WithShopData_Manager_Interface
{
	
	public function showType( string $type ) : string;
	
	public function renderSelectWidget( string $on_select,
	                                                   int $selected_property_id=0,
	                                                   ?string $only_type_filter=null,
	                                                   ?bool $only_active_filter=null,
	                                                   string $name='select_property' ) : string;
	
	public function renderProductPropertyEditFormField(
		Form $form,
		int $property_id,
		string $form_field_name_prefix=''
	) : string;
}
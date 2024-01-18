<?php
namespace JetApplication;


interface Admin_Managers_Product extends Admin_Entity_WithShopData_Manager_Interface
{
	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_product_id=0,
	                                    ?array $only_type_filter=null,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_product' ) : string;
	
}
<?php
namespace JetApplication;

interface Admin_Managers_KindOfProduct extends Admin_Entity_WithShopData_Manager_Interface
{
	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_kind_of_product_id=0,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_kind_of_product' ) : string;
}
<?php
namespace JetApplication;

interface Admin_Managers_Category extends Admin_Entity_WithShopData_Manager_Interface
{
	public function renderSelectWidget( string $on_select,
	                                            int $selected_category_id=0,
	                                            ?bool $only_active_filter=null,
	                                            string $name='select_category' ) : string;
}
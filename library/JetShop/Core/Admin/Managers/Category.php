<?php
namespace JetShop;

use JetApplication\Admin_EntityManager_WithShopData_Interface;

interface Core_Admin_Managers_Category extends Admin_EntityManager_WithShopData_Interface
{
	public function renderSelectWidget( string $on_select,
	                                            int $selected_category_id=0,
	                                            ?bool $only_active_filter=null,
	                                            string $name='select_category' ) : string;
}
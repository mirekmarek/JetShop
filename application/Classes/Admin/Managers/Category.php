<?php
namespace JetApplication;

interface Admin_Managers_Category
{
	public function renderSelectWidget( string $on_select,
	                                            int $selected_category_id=0,
	                                            ?bool $only_active_filter=null,
	                                            string $name='select_category' ) : string;
	
	public function getName( int $category_id ) : string;
	
}
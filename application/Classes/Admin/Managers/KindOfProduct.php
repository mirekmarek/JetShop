<?php
namespace JetApplication;

interface Admin_Managers_KindOfProduct {
	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_kind_of_product_id=0,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_kind_of_product' ) : string;
	
	public function showName( int $id ) : string;
}
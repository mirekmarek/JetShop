<?php
namespace JetApplication;


interface Admin_Managers_Product
{
	public function renderActiveState( Product $product ): string;
	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_product_id=0,
	                                    ?array $only_type_filter=null,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_product' ) : string;
	
	public function getName( int $id ) : string;
	
	public function showName( int $id ) : string;
	
}
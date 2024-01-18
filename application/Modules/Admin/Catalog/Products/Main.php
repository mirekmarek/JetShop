<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Tr;
use Jet\Application_Module;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Product;
use JetApplication\Admin_Entity_WithShopData_Manager_Trait;
use JetApplication\Entity_WithShopData;


class Main extends Application_Module implements Admin_Managers_Product
{
	use Admin_Entity_WithShopData_Manager_Trait;

	public const ADMIN_MAIN_PAGE = 'products';

	public const ACTION_GET = 'get_product';
	public const ACTION_ADD = 'add_product';
	public const ACTION_UPDATE = 'update_product';
	public const ACTION_DELETE = 'delete_product';


	
	public function renderSelectWidget( string $on_select,
	                                    int $selected_product_id=0,
	                                    ?array $only_type_filter=null,
	                                    ?bool $only_active_filter=null,
	                                    string $name='select_product' ) : string
	{
		
		$selected = $selected_product_id ? Product::get($selected_product_id) : null;
		
		return Admin_Managers::UI()->renderSelectEntityWidget(
			name: $name,
			caption: Tr::_('... select property ...', dictionary: $this->module_manifest->getName()),
			on_select: $on_select,
			object_class: Product::getEntityType(),
			object_type_filter: $only_type_filter,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getAdminTitle(),
			selected_entity_edit_URL: $selected?->getEditURL()
		);
	}
	
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new Product();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'product';
	}
	
}
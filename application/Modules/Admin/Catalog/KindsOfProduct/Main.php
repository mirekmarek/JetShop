<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\KindsOfProduct;

use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_KindOfProduct;
use JetApplication\Admin_Entity_WithShopData_Manager_Trait;
use JetApplication\Entity_WithShopData;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_KindOfProduct
{
	use Admin_Entity_WithShopData_Manager_Trait;
	
	
	public const ADMIN_MAIN_PAGE = 'kind-of-product';

	public const ACTION_GET = 'get_kind_of_product';
	public const ACTION_ADD = 'add_kind_of_product';
	public const ACTION_UPDATE = 'update_kind_of_product';
	public const ACTION_DELETE = 'delete_kind_of_product';
	
	
	public function renderSelectWidget( string $on_select,
                                        int $selected_kind_of_product_id=0,
                                        ?bool $only_active_filter=null,
                                        string $name='select_kind_of_product' ) : string
	{
		$selected = $selected_kind_of_product_id ? KindOfProduct::get($selected_kind_of_product_id) : null;
		
		return Admin_Managers::UI()->renderSelectEntityWidget(
			name: $name,
			caption: Tr::_('... select kind of product ...', dictionary: $this->module_manifest->getName()),
			on_select: $on_select,
			object_class: KindOfProduct::getEntityType(),
			object_type_filter: null,
			object_is_active_filter: $only_active_filter,
			selected_entity_title: $selected?->getInternalName(),
			selected_entity_edit_URL: $selected?->getEditURL()
		);
	}
	
	public static function getEntityInstance(): Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return new KindOfProduct();
	}
	
	public static function getEntityNameReadable() : string
	{
		return 'kind of product';
	}
	
}
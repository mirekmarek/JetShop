<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Tr;
use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Translator;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Product;
use JetApplication\Admin_Managers_Trait;
use JetApplication\Product as Application_Product;


class Main extends Application_Module implements Admin_Managers_Product
{
	use Admin_Managers_Trait;

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

	
	
	public function renderActiveState( Application_Product $product ) : string
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		$view->setVar('product', $product );
		
		return $view->render('active_state');
	}
	
	
	public function getName( int $id ) : string
	{
		$p = Product::get( $id );
		
		return $p?$p->getAdminTitle():'';
	}
	
	public function showName( int $id ): string
	{
		$res = '';
		
		Translator::setCurrentDictionaryTemporary(
			$this->module_manifest->getName(),
			function() use (&$res, $id) {
				$product = Product::get($id);
				
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('id', $id);
				
				if($product) {
					$view->setVar('product', $product);
					$res = $view->render('show-name/known');
				} else {
					$res = $view->render('show-name/unknown');
				}
			}
		);
		
		return $res;
	}
	
}
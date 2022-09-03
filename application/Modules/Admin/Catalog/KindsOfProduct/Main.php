<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Catalog\KindsOfProduct;

use Jet\Application_Module;
use Jet\MVC;
use JetShop\Admin_Module_Trait;
use JetShop\KindOfProduct_ManageModuleInterface;

/**
 *
 */
class Main extends Application_Module implements KindOfProduct_ManageModuleInterface
{
	use Admin_Module_Trait;
	
	
	const ADMIN_MAIN_PAGE = 'kind-of-product';

	const ACTION_GET_KIND_OF_PRODUCT = 'get_kind_of_product';
	const ACTION_ADD_KIND_OF_PRODUCT = 'add_kind_of_product';
	const ACTION_UPDATE_KIND_OF_PRODUCT = 'update_kind_of_product';
	const ACTION_DELETE_KIND_OF_PRODUCT = 'delete_kind_of_product';
	
	public function getKindsOfProductEditUrl( int $id ) : string
	{
		return $this->getEditUrl(
			static::ACTION_GET_KIND_OF_PRODUCT,
			static::ACTION_UPDATE_KIND_OF_PRODUCT,
			static::ADMIN_MAIN_PAGE,
			$id
		);
	}
	
	
	public function getKindOfProductSelectWhispererUrl( $only_active = false ): string
	{
		$page = MVC::getPage( static::ADMIN_MAIN_PAGE );
		if(!$page) {
			return '';
		}
		
		return $page->getURL([], [
			'only_active' => $only_active ? 1:0
		]);
	}
}
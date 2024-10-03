<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Customers;

use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithShopRelation_Trait;
use JetApplication\Admin_Managers_Customer;
use JetApplication\Entity_Address;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops_Shop;

class Main extends Application_Module implements Admin_EntityManager_WithShopRelation_Interface, Admin_Managers_Customer
{
	use Admin_EntityManager_WithShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'customers';

	public const ACTION_GET = 'get_customer';
	public const ACTION_UPDATE = 'update_customer';
	
	
	public static function getName( int $id ): string
	{
		return Customer::get($id)?->getAdminTitle();
	}
	
	public static function showActiveState( int $id ): string
	{
		return '';
	}
	
	public static function getCurrentUserCanEdit(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanCreate(): bool
	{
		return false;
	}
	
	public static function getCurrentUserCanDelete(): bool
	{
		return false;
	}
	
	public static function getEntityInstance(): Entity_WithShopRelation|Admin_Entity_WithShopRelation_Interface
	{
		return new Customer();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Customer';
	}
	
	public function showName( int $id ): string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($id) {
				if($id) {
					return '<a href="'.$this->getEditUrl($id).'">'.$id.'</a>';
				} else {
					return '<b>'.Tr::_('Unregistered customer').'</b>';
				}
			}
		);
	}
	
	public function formatAddress( Shops_Shop $shop, Entity_Address $address ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($shop, $address) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$view->setVar('address', $address);
				$view->setVar('shop', $shop);
				
				return $view->render('address/default');
			}
		);
		
	}
	
}
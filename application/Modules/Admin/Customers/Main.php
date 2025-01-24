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
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Trait;
use JetApplication\Admin_Managers_Customer;
use JetApplication\Entity_Address;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\Customer;
use JetApplication\EShop;

class Main extends Application_Module implements Admin_EntityManager_WithEShopRelation_Interface, Admin_Managers_Customer
{
	use Admin_EntityManager_WithEShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'customers';

	public const ACTION_GET = 'get_customer';
	public const ACTION_UPDATE = 'update_customer';
	
	
	public static function getName( int $id ): string
	{
		return Customer::get($id)?->getAdminTitle();
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
	
	public static function getEntityInstance(): Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface
	{
		return new Customer();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Customer';
	}
	
	public function showName( int|object $id_or_item ): string
	{
		$customer = $id_or_item instanceof Customer ? $id_or_item : Customer::get($id_or_item);
		
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($customer) {
				if($customer) {
					return '<a href="'.$this->getEditUrl($customer).'">'.$customer->getId().'</a>';
				} else {
					return '<b>'.Tr::_('Unregistered customer').'</b>';
				}
			}
		);
	}
	
	public function formatAddress( EShop $eshop, Entity_Address $address ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($eshop, $address) {
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				
				$view->setVar('address', $address);
				$view->setVar('eshop', $eshop);
				
				return $view->render('address/default');
			}
		);
		
	}
	
}
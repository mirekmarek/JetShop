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
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Manager_Interface;
use JetApplication\Customer_Address;
use JetApplication\Entity_Basic;
use JetApplication\Admin_Entity_Common_Manager_Trait;
use JetApplication\Admin_Managers_Customer;
use JetApplication\Shops_Shop;

class Main extends Application_Module implements Admin_Entity_Common_Manager_Interface, Admin_Managers_Customer
{
	use Admin_Entity_Common_Manager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'customers';

	public const ACTION_GET = 'get_customer';
	public const ACTION_ADD = 'add_customer';
	public const ACTION_UPDATE = 'update_customer';
	public const ACTION_DELETE = 'delete_customer';
	
	
	public static function getName( int $id ): string
	{
		return Customer::get($id)?->getAdminTitle();
	}
	
	public static function showName( int $id ): string
	{
		$title = Customer::get($id)?->getAdminTitle();
		
		return '<a href="'.static::getEditUrl($id).'">'.$title.'</a>';
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
	
	public static function getEntityInstance(): Entity_Basic|Admin_Entity_Common_Interface
	{
		return new Customer();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'customer';
	}
	
	public function showLink( int $customer_id ): string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($customer_id) {
				if($customer_id) {
					return '<a href="'.$this->getEditUrl($customer_id).'">'.$customer_id.'</a>';
				} else {
					return '<b>'.Tr::_('Unregistered customer').'</b>';
				}
			}
		);
	}
	
	public function formatAddress( Shops_Shop $shop, Customer_Address $address ) : string
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
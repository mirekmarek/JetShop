<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Customers;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Admin_Managers_Customer;
use JetApplication\EShopEntity_Address;
use JetApplication\EShopEntity_Basic;
use JetApplication\Customer;
use JetApplication\EShop;

class Main extends Admin_Managers_Customer
{
	public const ADMIN_MAIN_PAGE = 'customers';
	
	
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
	
	public static function getEntityInstance(): EShopEntity_Basic
	{
		return new Customer();
	}
	
	public static function getEntityNameReadable(): string
	{
		return 'Customer';
	}
	
	public function showName( int|EShopEntity_Basic $id_or_item ): string
	{
		if(!$id_or_item) {
			$customer = $id_or_item;
		} else {
			$customer = $id_or_item instanceof Customer ? $id_or_item : Customer::get($id_or_item);
		}
		
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
	
	public function formatAddress( EShop $eshop, EShopEntity_Address $address ) : string
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
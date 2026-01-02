<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Http_Request;
use Jet\MVC;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\Template_Property_Param;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Item;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop;
use JetApplication\EShop;

abstract class Core_Order_EMailTemplate extends EMail_Template {
	
	protected Order $order;
	protected Order_Event $event;
	protected string $note_for_customer = '';
	
	public function getOrder(): Order
	{
		return $this->order;
	}
	
	public function setOrder( Order $order ): void
	{
		$this->order = $order;
	}
	
	public function getEvent(): Order_Event
	{
		return $this->event;
	}
	
	public function setEvent( Order_Event $event ): void
	{
		$this->event = $event;
		$this->setOrder( $event->getOrder() );
		$this->note_for_customer = $event->getNoteForCustomer();
	}
	
	public function initTest( EShop $eshop ): void
	{
		if(!($id=Http_Request::GET()->getInt('order_id'))) {
			$ids = Order::dataFetchCol(
				select: ['id'],
				where: $eshop->getWhere(),
				order_by: '-id',
				limit: 1000
			);
			$id_key = array_rand( $ids, 1 );
			$id = $ids[$id_key];
		}
		
		$this->order = Order::get($id);
	}
	
	public function setupEMail( EShop $eshop, EMail $email ): void
	{
		$email->setContext( Order::getEntityType() );
		$email->setContextId( $this->order->getId() );
		$email->setContextCustomerId( $this->order->getCustomerId() );
		$email->setSaveHistoryAfterSend( true );
		$email->setTo( $this->order->getEmail() );
		
	}
	
	protected function initCommonProperties() : void
	{
		$this->addProperty( 'eshop_url', Tr::_( 'e-shop URL' ) )
			->setPropertyValueCreator( function() : string {
				$shop = $this->order->getEshop();
				return MVC::getBase( $shop->getBaseId() )->getHomepage(  $shop->getLocale())->getURL();
			} );
		
		
		$this->addProperty( 'order_number', Tr::_( 'Order number' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getNumber();
			} );
		
		$this->addProperty( 'purchase_date_time', Tr::_( 'Date and time of purchase' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getEshop()->getLocale()->formatDateAndTime( $this->order->getDatePurchased() );
			} );
		
		$this->addProperty( 'total', Tr::_( 'Order total' ) )
			->setPropertyValueCreator( function() : string {
				return Application_Service_EShop::PriceFormatter()->formatWithCurrency($this->order->getTotalAmount_WithVAT(), $this->order->getPricelist());
			} );
		
		
		
		$this->addCondition('billing_company', Tr::_( 'If billing company' ) )
			->setConditionEvaluator( function() : bool {
				return (bool)$this->order->getBillingAddress()->getCompanyName();
			} );
		
		$this->addProperty( 'billing_addr_company_name', Tr::_( 'Billing address - company name' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getCompanyName();
			});
		
		$this->addProperty( 'billing_addr_company_id', Tr::_( 'Billing address - company ID' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getCompanyId();
			});
		
		$this->addProperty( 'billing_addr_company_vat_id', Tr::_( 'Billing address - company VAT ID' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getCompanyVatId();
			});
		
		$this->addProperty( 'billing_addr_first_name', Tr::_( 'Billing address - firs name' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getFirstName();
			});
		
		$this->addProperty( 'billing_addr_surname', Tr::_( 'Billing address - surname' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getSurname();
			});
		
		$this->addProperty( 'billing_addr_street_no', Tr::_( 'Billing address - street and no' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getAddressStreetNo();
			});
		
		$this->addProperty( 'billing_addr_zip', Tr::_( 'Billing address - ZIP' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getAddressZip();
			});
		
		$this->addProperty( 'billing_addr_town', Tr::_( 'Billing address - town' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getBillingAddress()->getAddressTown();
			});
		
		
		
		$this->addCondition('show_delivery_address', Tr::_( 'Show delivery address?' ) )
			->setConditionEvaluator( function() : bool {
				$delivery_method = $this->order->getDeliveryMethod();
				
				if( $delivery_method->getKind()->isEDelivery() ) {
					return false;
				}
				
				return true;
			} );
		
		
		$this->addCondition('delivery_company', Tr::_( 'If delivery company' ) )
			->setConditionEvaluator( function() : bool {
				return (bool)$this->order->getDeliveryAddress()->getCompanyName();
			} );
		
		$this->addProperty( 'delivery_addr_company_name', Tr::_( 'Delivery address - company name' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getCompanyName();
			});
		
		$this->addProperty( 'delivery_addr_company_id', Tr::_( 'Delivery address - company ID' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getCompanyId();
			});
		
		$this->addProperty( 'delivery_addr_company_vat_id', Tr::_( 'Delivery address - company VAT ID' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getCompanyVatId();
			});
		
		$this->addProperty( 'delivery_addr_first_name', Tr::_( 'Delivery address - firs name' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getFirstName();
			});
		
		$this->addProperty( 'delivery_addr_surname', Tr::_( 'Delivery address - surname' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getSurname();
			});
		
		$this->addProperty( 'delivery_addr_street_no', Tr::_( 'Delivery address - street and no' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getAddressStreetNo();
			});
		
		$this->addProperty( 'delivery_addr_zip', Tr::_( 'Delivery address - ZIP' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getAddressZip();
			});
		
		$this->addProperty( 'delivery_addr_town', Tr::_( 'Delivery address - town' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryAddress()->getAddressTown();
			});
		
		
		$this->addProperty( 'delivery_method_title', Tr::_( 'Delivery method title' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryMethod()->getTitle();
			});
		$this->addProperty( 'delivery_method_info', Tr::_( 'Delivery method informations' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getDeliveryMethod()->generateConfirmationEmailInfoText( $this->order );
			});
		
		$this->addProperty( 'payment_method_title', Tr::_( 'Payment method title' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getPaymentMethod()?->getTitle()??'';
			});
		$this->addProperty( 'payment_method_info', Tr::_( 'Payment method informations' ) )
			->setPropertyValueCreator( function() : string {
				return $this->order->getPaymentMethod()?->generateConfirmationEmailInfoText( $this->order )??'';
			});
		
		$this->addCondition( 'customers_special_requirements', Tr::_( 'Customer has special requirements' ) )
			->setConditionEvaluator( function() : bool {
				return (bool)$this->order->getSpecialRequirements();
			});
		
		$this->addProperty( 'customers_special_requirements', Tr::_( 'Customers special requirements' ) )
			->setPropertyValueCreator( function() : string {
				return nl2br( $this->order->getSpecialRequirements() );
			});
		
		
		
		
		
		$items_block = $this->addPropertyBlock('items', Tr::_('Order items'));
			$items_block->setItemListCreator( function() : array {
				return $this->order->getItems();
			} );

			
			
		$img_property = $items_block->addProperty('img', Tr::_('Product image'));
		$img_property->setPropertyValueCreator( function( Order_Item $item, array $params ) : string {
			$url = $this->getProduct( $item )?->getImageThumbnailUrl(
				0,
				$params['max_w'],
				$params['max_h']
			)??'';
			if(!$url) {
				return '';
			}
			
			return '<img src="'.$url.'">';
		} );
		$img_property->addParam( Template_Property_Param::TYPE_INT, 'max_w', Tr::_('Maximal image width') );
		$img_property->addParam( Template_Property_Param::TYPE_INT, 'max_h', Tr::_('Maximal image height') );
		
		
		$items_block->addProperty('name', Tr::_('Order item name'))
			->setPropertyValueCreator( function( Order_Item $item ) : string {
				return $item->getTitle();
			} );
		
		$items_block->addProperty('URL', Tr::_('Order item detail URL'))
			->setPropertyValueCreator( function( Order_Item $item ) : string {
				return $this->getProduct( $item )?->getURL()??'';
			} );
		
		$items_block->addProperty('number_of_units', Tr::_('Number of units'))
			->setPropertyValueCreator( function( Order_Item $item ) : string {
				return $item->getNumberOfUnits();
			} );
		
		$items_block->addProperty('measure_unit', Tr::_('Measure unit'))
			->setPropertyValueCreator( function( Order_Item $item ) : string {
				return $item->getMeasureUnit()?->getName()??'';
			} );
		
		
		$items_block->addProperty('description', Tr::_('Order item description'))
			->setPropertyValueCreator( function( Order_Item $item ) : string {
				return $item->getDescription();
			} );
		
		$items_block->addProperty('unit_price', Tr::_('Order item unit price'))
			->setPropertyValueCreator( function( Order_Item $item ) : string {
				return Application_Service_Admin::PriceFormatter()->formatWithCurrency(
					$this->order->getPricelist(),
					$item->getPricePerUnit()
				);
			} );
		
		$items_block->addProperty('price', Tr::_('Order item price'))
			->setPropertyValueCreator( function( Order_Item $item ) : string {
				return Application_Service_Admin::PriceFormatter()->formatWithCurrency(
					$this->order->getPricelist(),
					$item->getPricePerUnit()*$item->getNumberOfUnits()
				);
			} );
		
		
		
		$items_block->addCondition('has_URL', Tr::_('Order item has URL'))
			->setConditionEvaluator( function( Order_Item $item ) : bool {
				return (bool)$this->getProduct( $item )?->getURL()??'';
			} );
		
		$items_block->addCondition('has_not_URL', Tr::_('Order item has not URL'))
			->setConditionEvaluator( function( Order_Item $item ) : bool {
				return !((bool)$this->getProduct( $item )?->getURL()??'');
			} );
		
		$items_block->addCondition('is_gift', Tr::_('Order item is gift'))
			->setConditionEvaluator( function( Order_Item $item ) : bool {
				if( !$this->getProduct( $item ) ) {
					return false;
				}
				
				return $item->getPricePerUnit()==0;
			} );
		
		$items_block->addCondition('is_not_gift', Tr::_('Order item is not gift'))
			->setConditionEvaluator( function( Order_Item $item ) : bool {
				if( !$this->getProduct( $item ) ) {
					return false;
				}
				
				return $item->getPricePerUnit()>0;
			} );
		
		$items_block->addCondition('is_free', Tr::_('Is free of charge'))
			->setConditionEvaluator( function( Order_Item $item ) : bool {
				return $item->getTotalAmount()==0;
			} );
		
		$items_block->addCondition('is_not_free', Tr::_('Is not free of charge'))
			->setConditionEvaluator( function( Order_Item $item ) : bool {
				return $item->getTotalAmount()>0;
			} );
		
		$this->addProperty('note_for_customer', Tr::_('Note for customer'))
			->setPropertyValueCreator( function() {
				if(!$this->note_for_customer) {
					return '';
				}
				
				return nl2br($this->note_for_customer);
			});
		
		$this->addCondition('has_note_for_customer', Tr::_('Has note for customer'))
			->setConditionEvaluator( function() {
				return (bool)$this->note_for_customer;
			});
		
		
	}
	
	protected array $products = [];
	
	
	public function getProduct( Order_Item $item ) : ?Product_EShopData
	{
		if(!array_key_exists($item->getItemId(), $this->products)) {
			
			if(
				$item->isPhysicalProduct() ||
				$item->isVirtualProduct()
			) {
				$product = $product=Product_EShopData::get( $item->getItemId(), $this->order->getEshop() );
				if($product) {
					$this->products[$item->getItemId()] = $product;
				}
			}
			
			if(!isset($this->products[$item->getItemId()])) {
				$this->products[$item->getItemId()] = false;
			}
		}
		
		return $this->products[$item->getItemId()]?:null;
	}
	
}
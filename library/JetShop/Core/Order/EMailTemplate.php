<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\Template_Property_Param;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Item;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Managers;
use JetApplication\EShop;

abstract class Core_Order_EMailTemplate extends EMail_Template {
	
	protected Order $order;
	protected Order_Event $event;
	
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
	}
	
	public function initTest( EShop $eshop ): void
	{
		$ids = Order::dataFetchCol(
			select: ['id'],
			where: $eshop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
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
		$order_number = $this->addProperty( 'order_number', Tr::_( 'Order number' ) );
		$order_number->setPropertyValueCreator( function() : string {
			return $this->order->getNumber();
		} );
		
		$purchased_date_time_property = $this->addProperty( 'purchase_date_time', Tr::_( 'Date and time of purchase' ) );
		$purchased_date_time_property->setPropertyValueCreator( function() : string {
			return $this->order->getEshop()->getLocale()->formatDateAndTime( $this->order->getDatePurchased() );
		} );
		
		$total_property = $this->addProperty( 'total', Tr::_( 'Order total' ) );
		$total_property->setPropertyValueCreator( function() : string {
			return EShop_Managers::PriceFormatter()->formatWithCurrency($this->order->getTotalAmount_WithVAT(), $this->order->getPricelist());
		} );
		
		
		
		
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
		
		$name_property = $items_block->addProperty('name', Tr::_('Order item name'));
		$name_property->setPropertyValueCreator( function( Order_Item $item ) : string {
			return $item->getTitle();
		} );
		
		
		$number_of_units_property = $items_block->addProperty('number_of_units', Tr::_('Number of units'));
		$number_of_units_property->setPropertyValueCreator( function( Order_Item $item ) : string {
			return $item->getNumberOfUnits();
		} );
		
		$measure_unit_property = $items_block->addProperty('measure_unit', Tr::_('Measure unit'));
		$measure_unit_property->setPropertyValueCreator( function( Order_Item $item ) : string {
			return $item->getMeasureUnit()?->getName()??'';
		} );
		
		
		$description_property = $items_block->addProperty('description', Tr::_('Order item description'));
		$description_property->setPropertyValueCreator( function( Order_Item $item ) : string {
			return $item->getDescription();
		} );
		
		$price_property = $items_block->addProperty('unit_price', Tr::_('Order item unit price'));
		$price_property->setPropertyValueCreator( function( Order_Item $item ) : string {
			return Admin_Managers::PriceFormatter()->formatWithCurrency(
				$this->order->getPricelist(),
				$item->getPricePerUnit()
			);
		} );
		
		$price_property = $items_block->addProperty('price', Tr::_('Order item price'));
		$price_property->setPropertyValueCreator( function( Order_Item $item ) : string {
			return Admin_Managers::PriceFormatter()->formatWithCurrency(
				$this->order->getPricelist(),
				$item->getPricePerUnit()*$item->getNumberOfUnits()
			);
		} );
		
		
	}
	
	protected array $products = [];
	
	
	public function getProduct( Order_Item $item ) : ?Product_EShopData
	{
		if(!array_key_exists($item->getItemId(), $this->products)) {
			
			if( $item->isPhysicalProduct() ) {
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
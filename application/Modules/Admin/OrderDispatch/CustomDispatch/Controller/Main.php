<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\OrderDispatch\CustomDispatch;


use Jet\AJAX;
use Jet\Data_DateTime;
use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;
use Jet\Form_Field_Textarea;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Carrier;
use JetApplication\Carrier_DeliveryPoint;
use JetApplication\Complaint;
use JetApplication\Currencies;
use JetApplication\Delivery_Kind;
use JetApplication\Order;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Item;
use JetApplication\Product;
use JetApplication\Product_EShopData;
use JetApplication\EShops;
use JetApplication\WarehouseManagement_Warehouse;
use Jet\Http_Headers;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$dispatch = new OrderDispatch();
		$dispatch->setIsCustom( true );
		
		$dispatch->setDispatchDate( Data_DateTime::now() );
		foreach( WarehouseManagement_Warehouse::getList() as $wh ) {
			$dispatch->setWarehouse( $wh );
			break;
		}
		
		$form = new Form('add_form', []);
		
		$eshop_field = new Form_Field_Select('eshop', 'e-shop:');
		$eshop_field->setSelectOptions( EShops::getScope() );
		$eshop_field->setDefaultValue( EShops::getCurrent()->getKey() );
		$eshop_field->setFieldValueCatcher( function( string $eshop_key ) use ($dispatch) {
			$dispatch->setEshop( EShops::get( $eshop_key ) );
		} );
		$form->addField($eshop_field);
		
		
		$order_field = new Form_Field_Input('order', 'Order number:');
		$order_field->setErrorMessages([
			'unknown_order_number' => 'Unknown order number',
		]);
		$order_field->setValidator( function() use ($order_field, $eshop_field) : bool {
			$order_number = $order_field->getValue();
			if(!$order_number) {
				return true;
			}
			
			$eshop = EShops::get( $eshop_field->getValue() );
			
			$order = Order::getByNumber( $order_number, $eshop );
			if(!$order) {
				$order_field->setError('unknown_order_number');
			}
			
			return true;
		} );
		$order_field->setFieldValueCatcher( function( string $order_number ) use ($dispatch) {
			if(
				$order_number &&
				($order = Order::getByNumber( $order_number, $dispatch->getEshop() ))
			) {
				$dispatch->setOrderId( $order->getId() );
			} else {
				$dispatch->setOrderId( 0 );
			}
		} );
		$form->addField( $order_field );
		
		
		
		$context_type_field = new Form_Field_Select('context_type', 'Context:');
		$context_type_field->setSelectOptions(OrderDispatch::getContextScope());
		$context_type_field->setFieldValueCatcher( function(string $context) use ($dispatch) : void {
			$dispatch->setContextType( $context );
		} );
		$form->addField( $context_type_field );
		
		
		$context_number_field = new Form_Field_Input('context_number', 'Context number:');
		$context_number_field->setErrorMessages([
			'unknown_context' => 'Unknown context'
		]);
		$context_number_field->setValidator( function() use ($dispatch, $context_type_field, $context_number_field, $eshop_field) : bool {
			$context_type = $context_type_field->getValue();
			$context_number = $context_number_field->getValue();
			$eshop = EShops::get($eshop_field->getValue());
			
			$dispatch->setContextId( 0 );
			$dispatch->setContextNumber( '' );
			
			switch($context_type) {
				case Order::getProvidesContextType():
					if(!$context_number) {
						$context_number_field->setError( Form_Field_Input::ERROR_CODE_EMPTY );
						return false;
					}
					
					$complaint = Order::getByNumber( $context_number, $eshop );
					if(!$complaint) {
						$context_number_field->setError('unknown_context');
						return false;
					}
					return true;
				case Complaint::getProvidesContextType():
					if(!$context_number) {
						$context_number_field->setError( Form_Field_Input::ERROR_CODE_EMPTY );
						return false;
					}
					
					$complaint = Complaint::getByNumber( $context_number, $eshop );
					if(!$complaint) {
						$context_number_field->setError('unknown_context');
						return false;
					}
					break;
			}
			
			return true;
		} );
		$context_number_field->setFieldValueCatcher( function(string $context_number ) use ($dispatch, $context_type_field) : void {
			$context_type = $context_type_field->getValue();
			
			$dispatch->setContextId( 0 );
			$dispatch->setContextNumber( '' );
			
			switch($context_type) {
				case Order::getProvidesContextType():
					if(
						$dispatch->getOrderId() &&
						($oder=$dispatch->getOrder())
					) {
						$dispatch->setContextId( $oder->getId() );
						$dispatch->setContextNumber( $oder->getNumber() );
					}
					
					break;
				case Complaint::getProvidesContextType():
					$complaint = Complaint::getByNumber( $context_number, $dispatch->getEshop() );
					if($complaint) {
						$dispatch->setContextId( $complaint->getId() );
						$dispatch->setContextNumber( $complaint->getNumber() );
					}
					break;
			}
		} );
		$form->addField( $context_number_field );
		
		
		$warehouse_field = new Form_Field_Select('warehouse', 'Warehouse:');
		$warehouse_field->setDefaultValue( $dispatch->getWarehouseId() );
		$warehouse_field->setSelectOptions( WarehouseManagement_Warehouse::getScope() );
		$warehouse_field->setFieldValueCatcher( function( string $warehouse_id ) use ($dispatch) : void {
			$dispatch->setWarehouse(  WarehouseManagement_Warehouse::get( $warehouse_id )  );
		});
		$form->addField( $warehouse_field );
		
		
		$sender_name_field = new Form_Field_Input('sender_name', 'Name:');
		$sender_name_field->setDefaultValue( $dispatch->getSenderName() );
		$sender_name_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setSenderName( $value );
		});
		$form->addField($sender_name_field);
		
		$sender_street_field = new Form_Field_Input('sender_street', 'Street address:');
		$sender_street_field->setDefaultValue( $dispatch->getSenderStreet() );
		$sender_street_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setSenderStreet($value);
		});
		$form->addField($sender_street_field);
		
		$sender_town_field = new Form_Field_Input('sender_town', 'Town:');
		$sender_town_field->setDefaultValue( $dispatch->getSenderTown() );
		$sender_town_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setSenderTown($value);
		});
		$form->addField($sender_town_field);
		
		$sender_zip_field = new Form_Field_Input('sender_zip', 'ZIP:');
		$sender_zip_field->setDefaultValue( $dispatch->getSenderZip() );
		$sender_zip_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setSenderZip($value);
		});
		$form->addField($sender_zip_field);
		
		$sender_country_field = new Form_Field_Input('sender_country', 'Country:');
		$sender_country_field->setDefaultValue( $dispatch->getSenderCountry() );
		$sender_country_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setSenderCountry($value);
		});
		$form->addField($sender_country_field);
		
		$sender_phone_field = new Form_Field_Input('sender_phone', 'Phone:');
		$sender_phone_field->setDefaultValue( $dispatch->getSenderPhone() );
		$sender_phone_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setSenderPhone($value);
		});
		$form->addField($sender_phone_field);
		
		$sender_email_field = new Form_Field_Input('sender_email', 'E-mail:');
		$sender_email_field->setDefaultValue( $dispatch->getSenderEmail() );
		$sender_email_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setSenderEmail($value);
		});
		$form->addField($sender_email_field);
		
		
		
		
		$recipient_country_field = new Form_Field_Input('recipient_company', 'Company:');
		$recipient_country_field->setDefaultValue( $dispatch->getRecipientCompany() );
		$recipient_country_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setRecipientCompany( $value );
		});
		$form->addField($recipient_country_field);
		
		
		$recipient_first_name_field = new Form_Field_Input('recipient_first_name', 'First name:');
		$recipient_first_name_field->setDefaultValue( $dispatch->getRecipientFirstName() );
		$recipient_first_name_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setRecipientFirstName( $value );
		});
		$form->addField($recipient_first_name_field);
		
		$recipient_surname_field = new Form_Field_Input('recipient_surname', 'Surname:');
		$recipient_surname_field->setDefaultValue( $dispatch->getRecipientSurname() );
		$recipient_surname_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setRecipientSurname( $value );
		});
		$form->addField($recipient_surname_field);

		
		$recipient_street_field = new Form_Field_Input('recipient_street', 'Street address:');
		$recipient_street_field->setDefaultValue( $dispatch->getRecipientStreet() );
		$recipient_street_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setRecipientStreet($value);
		});
		$form->addField($recipient_street_field);
		
		$recipient_town_field = new Form_Field_Input('recipient_town', 'Town:');
		$recipient_town_field->setDefaultValue( $dispatch->getRecipientTown() );
		$recipient_town_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setRecipientTown($value);
		});
		$form->addField($recipient_town_field);
		
		$recipient_zip_field = new Form_Field_Input('recipient_zip', 'ZIP:');
		$recipient_zip_field->setDefaultValue( $dispatch->getRecipientZip() );
		$recipient_zip_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setRecipientZip($value);
		});
		$form->addField($recipient_zip_field);
		
		$recipient_country_field = new Form_Field_Input('recipient_country', 'Country:');
		$recipient_country_field->setDefaultValue( $dispatch->getRecipientCountry() );
		$recipient_country_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setRecipientCountry($value);
		});
		$form->addField($recipient_country_field);
		
		$recipient_phone_field = new Form_Field_Input('recipient_phone', 'Phone:');
		$recipient_phone_field->setDefaultValue( $dispatch->getRecipientPhone() );
		$recipient_phone_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setRecipientPhone($value);
		});
		$form->addField($recipient_phone_field);
		
		$recipient_email_field = new Form_Field_Input('recipient_email', 'E-mail:');
		$recipient_email_field->setDefaultValue( $dispatch->getRecipientEmail() );
		$recipient_email_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setRecipientEmail($value);
		});
		$form->addField($recipient_email_field);
		
		
		$carrier_services = [];
		foreach(Carrier::getList() as $carrier ) {
			foreach($carrier->getServices() as $service) {
				$carrier_services[$carrier->getCode().'|'.$service->getCode()] = $carrier->getName().' - '.$service->getName();
			}
		}
		
		$carrier_service_field = new Form_Field_Select('carrier_service', 'Carrier service:');
		$carrier_service_field->setSelectOptions( $carrier_services );
		$carrier_service_field->setFieldValueCatcher( function( string $code ) use ($dispatch) : void {
			[$carrier_code, $service_code] = explode( '|', $code );
			
			$dispatch->setCarrierCode( $carrier_code );
			$dispatch->setCarrierServiceCode( $service_code );
		} );
		$form->addField($carrier_service_field);
		
		
		$delivery_point_code_field = new Form_Field_Input('delivery_point_code', 'Delivery point code:');
		$delivery_point_code_field->setErrorMessages([
			'unknown_delivery_point' => 'Unknown delivery point',
		]);
		$delivery_point_code_field->setDefaultValue( $dispatch->getDeliveryPointCode() );
		$delivery_point_code_field->setValidator( function() use ($delivery_point_code_field, $carrier_service_field ) : bool {
			[$carrier_code, $service_code] = explode( '|', $carrier_service_field->getValue() );
			$carrier = Carrier::get( $carrier_code );
			$service = $carrier->getService( $service_code );
			
			if($service->getCompatibleKindOfDelivery()!=Delivery_Kind::PERSONAL_TAKEOVER_EXTERNAL) {
				return true;
			}
			
			$point_code = $delivery_point_code_field->getValue();
			if(!$point_code) {
				$delivery_point_code_field->setError( Form_Field_Input::ERROR_CODE_EMPTY );
				return false;
			}
			
			$point = Carrier_DeliveryPoint::getPoint( $carrier, $point_code );
			if(!$point) {
				$delivery_point_code_field->setError('unknown_delivery_point');
			}
			
			return true;
		} );
		$delivery_point_code_field->setFieldValueCatcher(function(mixed $value) use ($dispatch) : void {
			$dispatch->setDeliveryPointCode($value);
		});
		$form->addField($delivery_point_code_field);
		
		
		
		
		$currency_field = new Form_Field_Select('currency', 'Currency:');
		$currency_field->setSelectOptions( Currencies::getScope() );
		$currency_field->setFieldValueCatcher( function( string $code ) use ($dispatch) : void {
			$dispatch->setCodCurrency( Currencies::get( $code ) );
		});
		$form->addField($currency_field);
		
		
		$financial_value_field = new Form_Field_Float('financial_value', 'Financial value:');
		$financial_value_field->setDefaultValue( $dispatch->getFinancialValue() );
		$financial_value_field->setFieldValueCatcher(function( float $value ) use ($dispatch) : void {
			$dispatch->setFinancialValue( $value );
		});
		$form->addField($financial_value_field);
		
		
		$cod_field = new Form_Field_Float('cod', 'COD:');
		$cod_field->setDefaultValue( $dispatch->getCod() );
		$cod_field->setFieldValueCatcher(function( float $value ) use ($dispatch) : void {
			$dispatch->setCod( $value );
		});
		$form->addField($cod_field);
		
		$our_note = new Form_Field_Textarea('our_note', 'Note:');
		$our_note->setFieldValueCatcher(function(string $value ) use ($dispatch) : void {
			$dispatch->setOurNote($value);
		});
		$form->addField($our_note);
		
		$items_field = new Form_Field_Hidden('items', 'Items:');
		$items_field->setValidator(function() use ($items_field, $form) : bool {
			$items = $items_field->getValueRaw();
			
			if(
				!$items ||
				!is_array($items=json_decode($items,true)) ||
				!count($items)
			) {
				
				$form->setCommonMessage( UI_messages::createDanger( Tr::_('Please add some item') ) );
				return false;
			}
			
			foreach($items as $product_id=>$qty) {
				$product_id = (int)$product_id;
				$qty = (int)$qty;
				if(
					!Product::exists($product_id) ||
					$qty<0
				) {
					$form->setCommonMessage( UI_messages::createDanger( Tr::_('Invalid item specification') ) );
					return false;
				}
			}
			
			return true;
		});
		$items_field->setFieldValueCatcher( function( string $value ) use ($eshop_field, $dispatch, $items_field) : void {
			
			$eshop = EShops::get( $eshop_field->getValue() );
			
			$items = $items_field->getValueRaw();
			$items = json_decode($items,true);
			
			foreach($items as $product_id=>$qty) {
				$product_id = (int)$product_id;
				$qty = (int)$qty;
				
				$product = Product_EShopData::get( $product_id, $eshop );
				
				$dispatch_item = new OrderDispatch_Item();
				$dispatch_item->setProductId( $product->getId() );
				$dispatch_item->setTitle( $product->getName() );
				$dispatch_item->setNumberOfUnits( $qty, $product->getKind()?->getMeasureUnit() );
				$dispatch_item->setInternalCode( $product->getInternalCode() );
				$dispatch_item->setEAN( $product->getEan() );

				$dispatch->addItem( $dispatch_item );
			}

		} );
		$form->addField( $items_field );
		
		
		$GET = Http_Request::GET();
		
		switch($GET->getString('action')) {
			case 'get_warehouse_address':
				$wh = WarehouseManagement_Warehouse::get( $GET->getInt('warehouse') );
				if($wh) {
					AJAX::commonResponse( [
						'address_name'      => $wh->getAddressName(),
						'address_street_no' => $wh->getAddressStreetNo(),
						'address_town'      => $wh->getAddressTown(),
						'address_zip'       => $wh->getAddressZip(),
						'address_country'   => $wh->getAddressCountry(),
						'phone' => $wh->getPublicPhone(),
						'email' => $wh->getPublicEmail()
					] );
				}
				break;
			case 'get_context_info':
				$eshop = EShops::get($GET->getString('eshop'));
				$context_type = $GET->getString('context_type');
				$context_number = $GET->getString('context_number');
				$context = null;
				$order_number = null;
				
				switch($context_type) {
					case Order::getProvidesContextType():
						$context = Order::getByNumber( $context_number, $eshop );
						$order_number = $context?->getNumber();
						break;
					case Complaint::getProvidesContextType():
						$context = Complaint::getByNumber( $context_number, $eshop );
						
						$order_number = $context?->getOrderNumber();
						break;
						
				}
				
				if($context) {
					AJAX::commonResponse([
						'id' => $context->getId(),
						'number' => $context->getNumber(),
						
						'order_number'     => $order_number,
						
						'address_company'   => $context->getDeliveryCompanyName(),
						'address_first_name'=> $context->getDeliveryFirstName(),
						'address_surname'   => $context->getDeliverySurname(),
						'address_street_no' => $context->getDeliveryAddressStreetNo(),
						'address_town'      => $context->getDeliveryAddressTown(),
						'address_zip'       => $context->getDeliveryAddressZip(),
						'address_country'   => $context->getDeliveryAddressCountry(),
						'phone' => $context->getPhone(),
						'email' => $context->getEmail()
					]);
				}
				
				
				AJAX::commonResponse([]);
				break;
			case 'render_items':
				$this->view->setVar('items', $GET->getRaw('items'));
				AJAX::snippetResponse( $this->view->render('items') );
				break;
		}

		
		if( $form->catch() ) {
			$dispatch->save();
			UI_messages::success( Tr::_('Custom order dispatch has been created') );
			Http_Headers::movedTemporary(
				$dispatch->getEditUrl()
			);

		}
		
		
		
		$this->view->setVar('form', $form);
		$this->view->setVar('dispatch', $dispatch);
		
		$this->output('default');
	}
}
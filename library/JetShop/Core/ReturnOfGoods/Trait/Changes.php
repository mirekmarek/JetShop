<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



use Jet\Auth;
use Jet\Data_DateTime;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_ChangeHistory;
use JetApplication\Customer;
use JetApplication\Customer_Address;
use JetApplication\Delivery_Method;

trait Core_ReturnOfGoods_Trait_Changes {
	
	public function startChange() : ReturnOfGoods_ChangeHistory
	{
		$change = new ReturnOfGoods_ChangeHistory();
		/**
		 * @var ReturnOfGoods $this
		 */
		$change->setReturnOfGoods( $this );
		$change->setDateAdded( Data_DateTime::now() );
		
		$admin = Auth::getCurrentUser();
		if($admin) {
			$change->setAdministrator( $admin->getName() );
			$change->setAdministratorId( $admin->getId() );
		}
		
		return $change;
	}
	
	
	public function changeDeliveryMethod( Delivery_Method $new_delivery_method, string $personal_takeover_delivery_point_code ) : ReturnOfGoods_ChangeHistory
	{
		$change = $this->startChange();
		
		if( $this->delivery_method_id != $new_delivery_method->getId() ) {
			$change->addChange(
				'delivery_method',
				$this->delivery_method_id,
				$new_delivery_method->getId()
			);
			$this->delivery_method_id = $new_delivery_method->getId();
		}
		
		if($this->delivery_personal_takeover_delivery_point_code!=$personal_takeover_delivery_point_code) {
			$change->addChange(
				'delivery_personal_takeover_delivery_point_code',
				$this->delivery_personal_takeover_delivery_point_code,
				$personal_takeover_delivery_point_code
			);
			
			$this->delivery_personal_takeover_delivery_point_code = $personal_takeover_delivery_point_code;
			
			if( $this->delivery_personal_takeover_delivery_point_code ) {
				$place = $new_delivery_method->getPersonalTakeoverDeliveryPoint( $personal_takeover_delivery_point_code );
				if($place) {
					$billing_address = $this->getDeliveryAddress();
					
					$delivery_address = new Customer_Address();
					$delivery_address->setFirstName( $billing_address->getFirstName() );
					$delivery_address->setSurname( $billing_address->getSurname() );
					$delivery_address->setCompanyName( $place->getName() );
					$delivery_address->setAddressStreetNo( $place->getStreet() );
					$delivery_address->setAddressTown( $place->getTown() );
					$delivery_address->setAddressZip( $place->getZip() );
					$delivery_address->setAddressCountry( $place->getCountry() );
					
					$this->updateDeliveryAddress( $delivery_address );
					
				}
			}
			
		}
		
		
		
		if($change->hasChange()) {
			
			$this->save();
			$change->save();
			
			$this->updated( $change );
		}
		
		
		
		return $change;
	}
	
	public function updateDeliveryAddress( Customer_Address $address ) : ReturnOfGoods_ChangeHistory
	{
		$map = [
			'delivery_company_name'      => 'CompanyName',
			//'delivery_company_id'        => 'CompanyId',
			//'delivery_company_vat_id'    => 'CompanyVatId',
			'delivery_first_name'        => 'FirstName',
			'delivery_surname'           => 'Surname',
			'delivery_address_street_no' => 'AddressStreetNo',
			'delivery_address_town'      => 'AddressTown',
			'delivery_address_zip'       => 'AddressZip',
			'delivery_address_country'   => 'AddressCountry',
		];
		
		$change = $this->startChange();
		foreach($map as $property=>$gs) {
			
			$address_getter = 'get'.$gs;
			$complaint_getter = 'getDelivery'.$gs;
			$complaint_setter = 'setDelivery'.$gs;
			
			if($this->{$complaint_getter}() != $address->{$address_getter}()) {
				$change->addChange(
					$property,
					$this->{$complaint_getter}(),
					$address->{$address_getter}()
				);
				$this->{$complaint_setter}( $address->{$address_getter}() );
			}
		}
		
		
		
		if($change->hasChange()) {
			$change->save();
			$this->save();
			$this->updated( $change );
		}
		
		return $change;
		
	}
	
	public function updateEmailAndPhone( ?string $new_email=null, ?string $new_phone=null, bool $update_customer_account=true ) : ReturnOfGoods_ChangeHistory
	{
		if($new_email===null) {
			$new_email = $this->getEmail();
		}
		if($new_phone===null) {
			$new_phone = $this->getPhone();
		}
		
		$change = $this->startChange();
		$email_updated = false;
		
		if($new_email!=$this->getEmail()) {
			
			$change->addChange('email', $this->getEmail(), $new_email);
			$this->setEmail( $new_email );
			$email_updated = true;
		}
		
		if($new_phone!=$this->getPhone()) {
			
			$change->addChange('phone', $this->getPhone(), $new_phone);
			$this->setPhone( $new_phone );
			
		}
		
		if($change->hasChange()) {
			$this->save();
			$change->save();
			$this->updated( $change );
			
			$customer = Customer::get( $this->getCustomerId() );
			
			if($customer) {
				if($customer->getEmail()!=$new_email) {
					$customer->changeEmail( $new_email, 'order:'.$this->getNumber() );
				}
				
				if($customer->getPhoneNumber()!=$new_phone) {
					$customer->setPhoneNumber( $new_phone );
				}
				
				$customer->save();
			}
			
		}
		
		return $change;
	}
	
	
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EMailMarketing\EComail;

use Jet\Db;
use JetApplication\Customer;
use JetApplication\EShop;

class Ecomail
{
	protected EShop $eshop;
	protected Ecomail_Client|null $client = null;
	
	public function __construct( Config_PerShop $config )
	{
		$this->eshop = $config->getEshop();
		$this->client = new Ecomail_Client(
			$config->getApiUrl(),
			$config->getAPIKey(),
			$config->getListId()
		);
	}
	
	public function getSubscriberData( string $email_address ) : array
	{
		$first_name = '';
		$last_name = '';
		
		$city = '';
		$street = '';
		$zip = '';
		$phone = '';
		
		$country = '';
		
		$customer = Customer::getByEmail( $email_address, $this->eshop );
		
		if($customer) {
			$first_name = $customer->getFirstName();
			$last_name = $customer->getSurname();
			$phone = $customer->getPhoneNumber();
			
			$addr = $customer->getDefaultAddress();
			
			if($addr) {
				$zip = $addr->getAddressZip();
				$city = $addr->getAddressTown();
				$street = $addr->getAddressStreetNo();
				$country = $addr->getAddressCountry();
			}
		}
		
		
		
		
		$data = [
			'subscriber_data' => [
				"name" => $first_name,
				"surname" => $last_name,
				"email" => $email_address,
				"city" => $city,
				"street" => $street,
				"zip" => $zip,
				"country" => $country,
				"phone" => $phone,
				"source" => "API",
			],
			
			'trigger_autoresponders' => true,
			'update_existing' => true,
			'resubscribe' => true
		];
		
		if(
			$country == 'CZ' &&
			$zip
		) {
			$region = Db::get()->fetchRow("SELECT DISTINCT region_name,district_name FROM `cz_address` WHERE zip='".addslashes($zip)."'");
			if($region) {
				$data['subscriber_data']['custom_fields'] = [
					'KRAJ' => $region['region_name'],
					'OKRES' => $region['district_name'],
				];
			}
		}
		
		return $data;
	}
	
	public function subscribe( string $email_address ) : bool
	{
		$data = $this->getSubscriberData( $email_address );
		
		if($this->client->addSubscriber( $data )) {
			$this->getID( $email_address );
			
			EmailMap::subscribe(
				$this->eshop,
				$email_address
			);
			
			return true;
		}
		
		return false;
	}
	
	public function unsubscribe( string $email_address ) : bool
	{
		EmailMap::unsubscribe( $this->eshop, $email_address );
		
		return (bool)$this->client->removeSubscriber( [
			'email' => $email_address
		] );
	}
	
	
	public function update( string $email_address ) : bool
	{
		$data = $this->getSubscriberData( $email_address );
		
		
		if($this->client->updateSubscriber( $email_address, $data )) {
			return true;
		}
		
		return false;
		
	}
	
	public function changeMail( string $old_email_address, string $new_mail_address ): void
	{
		$this->delete( $old_email_address );
		$this->subscribe( $new_mail_address );
	}

	public function getID( string $email_address ) : string
	{
		$id = EmailMap::getSubscriberId( $this->eshop, $email_address );
		if($id) {
			return $id;
		}
		
		$email_info = $this->getSubscriberInfo( $email_address );
		
		$id = '';
		if($email_info) {
			$id = $email_info['id'];
		}
		
		EmailMap::set( $this->eshop, $email_address, $id );
		
		return $id;
	}
	
	public function getEmail( string $id ) : string
	{
		$email = EmailMap::getSubscriberEmail( $this->eshop, $id );
		
		return $email?'':$email;
	}
	
	public function delete( string $email_address ) : bool
	{
		EmailMap::deleteSubscriber( $this->eshop, $email_address );
		
		$this->client->deleteSubscriber( $email_address );
		
		return false;
	}
	
	
	public function getSubscriberInfo( string $email_address ) : false|array
	{
		$res = $this->client->getSubscriber( $email_address );
		
		if(
			!$res ||
			!isset($res['subscriber'])
		) {
			return false;
		}
		
		return $res['subscriber'];
	}
	

	public function addEvent(array $data) : array
	{
		return $this->client->addEvent( $data );
	}
	
	
}
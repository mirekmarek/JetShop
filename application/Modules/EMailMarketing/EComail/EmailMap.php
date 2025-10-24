<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EMailMarketing\EComail;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Locale;
use JetApplication\EShop;
use JetApplication\EShops;

#[DataModel_Definition(
	name: 'ecomail_email_map',
	database_table_name: 'ecomail_email_map',
	id_controller_class: DataModel_IDController_Passive::class
)]
class EmailMap extends DataModel
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		is_id: true,
	)]
	protected string $eshop_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
		is_id: true,
	)]
	protected ?Locale $locale = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
		is_id: true,
	)]
	protected string $email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $removed = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_of_registration = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $waiting_for_approvement = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $last_check_sent_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $last_approve_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $last_approve_ip = '';
	
	public function getEShopKey() : string
	{
		return $this->eshop_code.'_'.$this->locale;
	}
	
	public function setEShop( EShop $eshop ): void
	{
		$this->eshop_code = $eshop->getCode();
		$this->locale = $eshop->getLocale();
	}
	
	public function getEShop() : EShop
	{
		return EShops::get( $this->getEShopKey() );
	}
	
	public function getEmail(): string
	{
		return $this->email;
	}
	
	public function setEmail( string $email ): void
	{
		$this->email = $email;
	}
	
	public function getId(): string
	{
		return $this->id;
	}
	
	public function setId( string $id ): void
	{
		$this->id = $id;
	}
	
	public function isRemoved(): bool
	{
		return $this->removed;
	}
	
	public function setRemoved( bool $removed ): void
	{
		$this->removed = $removed;
	}
	
	public function getDateOfRegistration(): ?Data_DateTime
	{
		return $this->date_of_registration;
	}
	
	public function setDateOfRegistration( null|Data_DateTime|string $date_of_registration ): void
	{
		$this->date_of_registration = Data_DateTime::catchDateTime( $date_of_registration );
	}
	
	public function getWaitingForApprovement(): bool
	{
		return $this->waiting_for_approvement;
	}
	
	public function setWaitingForApprovement( bool $waiting_for_approvement ): void
	{
		$this->waiting_for_approvement = $waiting_for_approvement;
	}
	
	public function getLastCheckSentDate(): ?Data_DateTime
	{
		return $this->last_check_sent_date;
	}
	
	public function setLastCheckSentDate( null|Data_DateTime|string $last_check_sent_date ): void
	{
		$this->last_check_sent_date = Data_DateTime::catchDateTime( $last_check_sent_date );
	}
	
	public function getLastApproveDateTime(): ?Data_DateTime
	{
		return $this->last_approve_date_time;
	}
	
	public function setLastApproveDateTime( null|Data_DateTime|string $last_approve_date_time ): void
	{
		$this->last_approve_date_time = Data_DateTime::catchDateTime( $last_approve_date_time );
	}
	
	public function getLastApproveIp(): string
	{
		return $this->last_approve_ip;
	}
	
	public function setLastApproveIp( string $last_approve_ip ): void
	{
		$this->last_approve_ip = $last_approve_ip;
	}
	
	
	
	public static function set( EShop $eshop, string $email, string $id ) : void
	{
		$rec = static::load([
			$eshop->getWhere(),
			'AND',
			'email' => $email
		]);
		if(!$rec) {
			$rec = new static();
			$rec->setEShop( $eshop );
			$rec->setEmail( $email );
		}
		$rec->setId( $id );
		$rec->setRemoved( false );
		$rec->setDateOfRegistration( Data_DateTime::now() );
		$rec->save();
	}
	
	public static function subscribe( EShop $eshop, string $email ): void
	{
		static::updateData(
			data: [
				'removed' => false
			],
			where: [
				$eshop->getWhere(),
				'AND',
				'email' => $email,
			]
		);
		
	}
	
	
	public static function unsubscribe( EShop $eshop, string $email ): void
	{
		static::updateData(
			data: [
				'removed' => true
			],
			where: [
				$eshop->getWhere(),
				'AND',
				'email' => $email,
			]
		);
		
	}
	
	public static function getSubscriberId( EShop $eshop, string $email ) : false|string
	{
		$id = static::dataFetchOne(
			select: ['id'],
			where: [
				$eshop->getWhere(),
				'AND',
				'email' => $email
			]
		);
		
		return $id?false:$id;
	}
	
	public static function getSubscriberEmail( EShop $eshop, string $id ) : false|string
	{
		$email = static::dataFetchOne(
			select: ['email'],
			where: [
				$eshop->getWhere(),
				'AND',
				'id' => $id
			]
		);
		
		return $email?false:$email;
	}
	
	public static function deleteSubscriber( EShop $eshop, string $email ) : void
	{
		static::dataDelete([
			$eshop->getWhere(),
			'AND',
			'email' => $email,
		]);
		
	}
	
	
}
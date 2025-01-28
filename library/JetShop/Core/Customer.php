<?php
namespace JetShop;

use Jet\Auth;
use Jet\Auth_User_Interface;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Definition;
use Jet\Form_Field_Input;
use Jet\Data_DateTime;
use Jet\DataModel_Query;

use JetApplication\Entity_Admin_Interface;
use JetApplication\Entity_Admin_Trait;
use JetApplication\Admin_Managers_Customer;
use JetApplication\Customer_Address;
use JetApplication\Customer;
use JetApplication\EMailMarketing;
use JetApplication\Entity_HasGet_Interface;
use JetApplication\Entity_HasGet_Trait;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\EShops;
use JetApplication\EMailMarketing_Subscribe;
use JetApplication\EShop;
use JetApplication\Entity_Definition;


#[DataModel_Definition(
	name: 'customer',
	database_table_name: 'customers',
	relation: [
		'related_to_class_name' => Customer_Address::class,
		'join_by_properties' => [
			'id' => 'customer_id'
		],
		'join_type' => DataModel_Query::JOIN_TYPE_LEFT_JOIN
	]
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_Customer::class
)]
abstract class Core_Customer extends Entity_WithEShopRelation implements
	Auth_User_Interface,
	Entity_Admin_Interface,
	Entity_HasGet_Interface
{
	use Entity_Admin_Trait;
	use Entity_HasGet_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		do_not_export: true,
		max_len: 255,
	)]
	protected string $password = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'E-mail',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter e-mail address',
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter e-mail address',
			'is_registered' => 'Sorry, but e-mail %EMAIL% is registered.',
		]
	)]
	protected string $email = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'First name',
	)]
	protected string $first_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Surname'
	)]
	protected string $surname = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description'
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Password is valid',
	)]
	protected bool $password_is_valid = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE_TIME,
		label: 'Password is valid till',
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Invalid date format'
		]
	)]
	protected ?Data_DateTime $password_is_valid_till = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'User is blocked',
	)]
	protected bool $user_is_blocked = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE_TIME,
		label: 'User is blocked till',
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Invalid date format'
		]
	)]
	protected ?Data_DateTime $user_is_blocked_till = null;

	/**
	 * @var ?Data_DateTime
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $registration_date_time = null;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	protected string $registration_IP = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 20,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEL,
		label: 'Phone number:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter phone number',
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter phone number'
		]
	)]
	protected string $phone_number = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $loyalty_program_points = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $oauth_service = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $oauth_key = '';

	/**
	 * @var Customer_Address[]
	 */
	protected ?array $_addresses = null;



	public function setPassword( string $password, bool $encrypt_password=true ): void
	{
		if( $password ) {
			$this->password = $encrypt_password ? $this->encryptPassword( $password ) : $password;
		}
	}


	public function setEncryptedPassword( string $password ) : void
	{
		if($password) {
			$this->password = $password;
		}
	}

	public function encryptPassword( string $plain_password ) : string
	{
		return password_hash( $plain_password, PASSWORD_DEFAULT );
	}
	
	/**
	 * @param string|null $role_id
	 * @param string $search
	 * @return static[]
	 */
	public static function getList( string|null $role_id = null, string $search='' ) : iterable
	{
		$where = [];

		if( $role_id ) {
			$where = [
				'Auth_Role.id' => $role_id,
			];
		}

		if( $search ) {
			if( $where ) {
				$where [] = 'AND';
			}

			$search = '%'.$search.'%';
			$where[] = [
				'first_name *' => $search,
				'OR',
				'surname *'    => $search,
				'OR',
				'email *'      => $search,
			];
		}


		$list = static::fetchInstances( $where );
		$list->getQuery()->setOrderBy( 'email' );

		return $list;

	}

	public static function getByIdentity( string $username, string $password ) : static|null
	{
		$user = static::load(
			[
				'email' => $username,
			]
		);

		if( !$user ) {
			return null;
		}

		if( !$user->verifyPassword( $password ) ) {
			return null;
		}

		return $user;
	}

	public function verifyPassword( string $plain_password ) : bool
	{
		return password_verify( $plain_password, $this->password );
	}

	public static function getGetByUsername( string $username ) : static|null
	{
		return static::load(
			[
				'email' => $username,
			]
		);
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function getUsername() : string
	{
		return $this->email;
	}

	public function setUsername( string $username ) : void
	{
		$this->email = $username;
	}

	public function getEmail() : string
	{
		return $this->email;
	}

	public function setEmail( string $email ) : void
	{

		if($this->getIsNew()) {
			$this->email = $email;
		}

	}

	public function changeEmail( string $new_email, string $source, string $comment='' ) : void
	{
		if($this->getIsNew()) {
			return;
		}

		if($new_email==$this->email) {
			return;
		}

		$old_email = $this->email;

		$this->email = $new_email;
		$this->save();

		EMailMarketing::SubscriptionManager()->changeMail(
			$this->getEshop(), $old_email, $new_email, $source, $comment
		);
	}



	public function getName() : string
	{
		return $this->first_name.' '.$this->surname;
	}


	public function getFirstName() : string
	{
		return $this->first_name;
	}

	public function setFirstName( string $first_name ) : void
	{
		if($first_name==$this->first_name) {
			return;
		}
		$this->first_name = $first_name;
		
		if(!$this->getIsNew()) {
			$this->save();
		}
	}

	public function getSurname() : string
	{
		return $this->surname;
	}

	public function setSurname( string $surname ) : void
	{
		if($surname==$this->surname) {
			return;
		}
		
		$this->surname = $surname;
		if(!$this->getIsNew()) {
			$this->save();
		}
	}

	public function getDescription() : string
	{
		return $this->description;
	}


	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getPasswordIsValid() : bool
	{
		return $this->password_is_valid;
	}

	public function setPasswordIsValid( bool $password_is_valid ) : void
	{
		$this->password_is_valid = $password_is_valid;
	}

	public function getPasswordIsValidTill() : Data_DateTime|null
	{
		return $this->password_is_valid_till;
	}

	public function setPasswordIsValidTill( Data_DateTime|string|null $password_is_valid_till ) : void
	{
		if( !$password_is_valid_till ) {
			$this->password_is_valid_till = null;
		} else {
			if($password_is_valid_till instanceof Data_DateTime) {
				$this->password_is_valid_till = $password_is_valid_till;
			} else {
				$this->password_is_valid_till = new Data_DateTime( $password_is_valid_till );
			}
		}
	}

	public function isBlocked() : bool
	{
		return $this->user_is_blocked;
	}

	public function isBlockedTill() : null|Data_DateTime
	{
		return $this->user_is_blocked_till;
	}

	public function block( string|Data_DateTime|null $till = null ) : void
	{
		$this->user_is_blocked = true;
		if( !$till ) {
			$this->user_is_blocked_till = null;
		} else {
			if($till instanceof Data_DateTime) {
				$this->user_is_blocked_till = $till;
			} else {
				$this->user_is_blocked_till = new Data_DateTime( $till );
			}
		}
	}

	public function unBlock() : void
	{
		$this->user_is_blocked = false;
		$this->user_is_blocked_till = null;
	}

	public function isActivated() : bool
	{
		return true;
	}

	public function getRoles() : array
	{
		return [];
	}


	public function setRoles( array $role_ids ) : void
	{
	}

	public function hasRole( int|string $role_id ) : bool
	{
		return false;
	}

	public function hasPrivilege( string $privilege, mixed $value=null ): bool
	{
		return false;
	}

	public function getPrivilegeValues( string $privilege ) : array
	{
		return [];
	}

	public static function usernameExists( string $username ) : bool
	{
		return (bool)static::getBackendInstance()->getCount( static::createQuery( [
			'username' => $username,
		] ) );
	}
	
	public static function generatePassword() : string
	{
		srand();
		$password = '';
		$length = rand( 5, 9 );

		$chars = [
			0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
			'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
			'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
		];

		for( $l = 0; $l<$length; $l++ ) {
			$password .= $chars[rand( 1, count( $chars ) )-1];
		}

		return $password;
	}

	public function _getForm() : Form
	{

		$form = $this->createForm('');

		$form->getField( 'email' )->setValidator(
			function( Form_Field_Input $field ) {
				$email = $field->getValue();

				if( $this->usernameExists( $email ) ) {
					$field->setError( 'is_registered', [ 'EMAIL' => $email ] );

					return false;
				}

				return true;
			}
		);


		return $form;
	}
	
	
	/**
	 * @param Data_DateTime|string|null $value
	 */
	public function setRegistrationDateTime( Data_DateTime|string|null $value ) : void
	{
		if( $value===null ) {
			$this->registration_date_time = null;
			return;
		}
		
		if( !( $value instanceof Data_DateTime ) ) {
			$value = new Data_DateTime( (string)$value );
		}
		
		$this->registration_date_time = $value;
	}

	/**
	 * @return Data_DateTime|null
	 */
	public function getRegistrationDateTime() : Data_DateTime|null
	{
		return $this->registration_date_time;
	}

	/**
	 * @param string $value
	 */
	public function setRegistrationIp( string $value ) : void
	{
		$this->registration_IP = $value;
	}

	/**
	 * @return string
	 */
	public function getRegistrationIp() : string
	{
		return $this->registration_IP;
	}

	/**
	 * @param string $value
	 */
	public function setPhoneNumber( string $value ) : void
	{
		if($value==$this->phone_number) {
			return;
		}
		
		$this->phone_number = $value;
		if(!$this->getIsNew()) {
			$this->save();
		}
	}

	/**
	 * @return string
	 */
	public function getPhoneNumber() : string
	{
		return $this->phone_number;
	}
	

	/**
	 * @return bool
	 */
	public function getMailingSubscribed() : bool
	{
		return (bool)EMailMarketing_Subscribe::get( $this->getEshop(), $this->email );
	}

	/**
	 * @param int $value
	 */
	public function setLoyaltyProgramPoints( int $value ) : void
	{
		$this->loyalty_program_points = $value;
	}

	/**
	 * @return int
	 */
	public function getLoyaltyProgramPoints() : int
	{
		return $this->loyalty_program_points;
	}

	/**
	 * @param string $value
	 */
	public function setOauthService( string $value ) : void
	{
		$this->oauth_service = $value;
	}

	/**
	 * @return string
	 */
	public function getOauthService() : string
	{
		return $this->oauth_service;
	}

	/**
	 * @param string $value
	 */
	public function setOauthKey( string $value ) : void
	{
		$this->oauth_key = $value;
	}

	/**
	 * @return string
	 */
	public function getOauthKey() : string
	{
		return $this->oauth_key;
	}

	public static function getCurrentCustomer() : ?Customer
	{
		/**
		 * @var ?Customer $user
		 */
		$user = Auth::getCurrentUser();
		if(!$user) {
			return null;
		}

		return $user;
	}

	public static function getByEmail( string $email_address, ?EShop $eshop=null ) : ?static
	{
		if(!$eshop) {
			$eshop = EShops::getCurrent();
		}

		$customers = static::fetch([
			'customer' => [
				'email' => $email_address,
				'AND',
				$eshop->getWhere()
			]
		]);

		if(count($customers)!=1) {
			return null;
		}

		return $customers[0];
	}
	
	/**
	 * @return Customer_Address[]
	 */
	public function getAddresses() : iterable
	{
		if($this->_addresses===null) {
			$this->_addresses = [];

			foreach( Customer_Address::getListForCustomer( $this->id ) as $a) {
				$this->_addresses[$a->getId()] = $a;
			}
		}

		return $this->_addresses;
	}

	public function getAddress( int $id ) : ?Customer_Address
	{
		$this->getAddresses();
		
		return $this->_addresses[$id]??null;
	}

	public function hasAddress( Customer_Address $address ) : bool
	{
		$address->generateHash();
		$a_hash = $address->getHash();
		
		foreach($this->getAddresses() as $a ) {
			if($a->getHash()==$a_hash) {
				return true;
			}
		}

		return false;
	}

	public function addAddress( Customer_Address $address ) : void
	{
		if($this->hasAddress($address)) {
			return;
		}
		
		$address->setId( 0 );
		$address->setCustomerId( $this->id );

		$address->save();
		
		$this->_addresses = null;
		
		if(!$this->getDefaultAddress()) {
			$address->setIsDefault();
		}
		
	}
	
	public function getDefaultAddress() : ?Customer_Address
	{
		foreach($this->getAddresses() as $address) {
			if($address->isDefault()) {
				return $address;
			}
		}

		return null;
	}
	
	public function getAdminTitle(): string
	{
		return $this->getName();
	}
	
	public static function verifyPasswordStrength( string $password ): bool
	{
		return true;
	}
	
	public static function getByOAuth( string $oauth_service, string $oauth_key ): static|null
	{
		return static::load( [
			'oauth_service' => $oauth_service,
			'AND',
			'oauth_key' => $oauth_key
		] );
	}
	
	public function isEditable(): bool
	{
		return false;
	}
	
	public function setEditable( bool $editable ): void
	{
	}
	
	
	public function getAddForm(): Form
	{
		return new Form('', []);
	}
	
	public function catchAddForm(): bool
	{
		return false;
	}
	
	public function getEditForm(): Form
	{
		return new Form('', []);
	}
	
	public function catchEditForm(): bool
	{
		return false;
	}
	
}
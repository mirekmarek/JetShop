<?php
namespace JetShop;

use Jet\Auth_User_Interface;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Form_Field_RegistrationPassword;
use Jet\Data_DateTime;
use Jet\Locale;
use Jet\Mailing_Email;
use Jet\Tr;
use Jet\Form_Field_Tel;
use Jet\DataModel_Query;
use Jet\Form_Field_DateTime;


#[DataModel_Definition(
	name: 'customer',
	database_table_name: 'customers',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	],
	relation: [
		'related_to_class_name' => Core_Customer_Address::class,
		'join_by_properties' => [
			'id' => 'custommer_id'
		],
		'join_type' => DataModel_Query::JOIN_TYPE_LEFT_JOIN
	]
)]
abstract class Core_Customer extends DataModel implements Auth_User_Interface
{

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
		form_field_type: false
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_is_required: true,
		is_key: true,
		is_unique: true,
		form_field_label: 'Username',
		form_field_error_messages: [
			Form_Field_Input::ERROR_CODE_EMPTY=>'Please enter username'
		]
	)]
	protected string $username = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		do_not_export: true,
		max_len: 255,
		form_field_type: false
	)]
	protected string $password = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_label: 'E-mail',
		form_field_is_required: true,
		form_field_error_messages: [
			Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter e-mail address',
			Form_Field_Input::ERROR_CODE_INVALID_FORMAT => 'Please enter e-mail address'
		]
	)]
	protected string $email = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		form_field_type: false
	)]
	protected string $shop_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_label: 'First name'
	)]
	protected string $first_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_label: 'Surname'
	)]
	protected string $surname = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
		form_field_label: 'Description'
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		default_value: true,
		form_field_label: 'Password is valid'
	)]
	protected bool $password_is_valid = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		default_value: null,
		form_field_label: 'Password is valid till',
		form_field_error_messages: [
			Form_Field_Input::ERROR_CODE_INVALID_FORMAT => 'Invalid date format'
		]
	)]
	protected ?Data_DateTime $password_is_valid_till = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		default_value: false,
		form_field_label: 'User is blocked'
	)]
	protected bool $user_is_blocked = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		default_value: null,
		form_field_label: 'User is blocked till',
		form_field_error_messages: [
			Form_Field_Input::ERROR_CODE_INVALID_FORMAT => 'Invalid date format'
		]
	)]
	protected ?Data_DateTime $user_is_blocked_till = null;

	/**
	 * @var ?Data_DateTime
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		form_field_type: false
	)]
	protected ?Data_DateTime $registration_date_time = null;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_type: false
	)]
	protected string $registration_IP = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $default_address_id = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 20,
		form_field_type: 'Tel',
		form_field_label: 'Phone number:',
		form_field_error_messages: [
			Form_Field_Tel::ERROR_CODE_EMPTY => 'Please enter phone number',
			Form_Field_Tel::ERROR_CODE_INVALID_FORMAT => 'Please enter phone number'
		]
	)]
	protected string $phone_number = '';

	/**
	 * @var bool
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_type: false
	)]
	protected bool $mailing_accepted = false;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		form_field_type: false
	)]
	protected int $loyalty_program_points = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
		form_field_type: false
	)]
	protected string $oauth_service = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
		form_field_type: false
	)]
	protected string $oauth_key = '';



	protected ?Form $_form_add = null;

	protected ?Form $_form_edit = null;


	public function __construct( ?string $username = null, ?string $password = null )
	{

		if( $username!==null ) {
			$this->setUsername( $username );
		}
		if( $password!==null ) {
			$this->setPassword( $password );
		}

		parent::__construct();
	}

	public function setPassword( string $password ) : void
	{
		if( $password ) {
			$this->password = $this->encryptPassword( $password );
		}
	}

	public function encryptPassword( string $password ) : string
	{
		return password_hash( $password, PASSWORD_DEFAULT );
	}

	public static function get( string|int $id ) : static|null
	{
		return static::load( $id );
	}

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
				'username *'   => $search,
				'OR',
				'first_name *' => $search,
				'OR',
				'surname *'    => $search,
				'OR',
				'email *'      => $search,
			];
		}


		$list = static::fetchInstances( $where );
		$list->setLoadFilter(
			[
				'id',
				'username',
				'first_name',
				'surname',
				'locale',
			]
		);
		$list->getQuery()->setOrderBy( 'username' );

		return $list;

	}

	public static function getByIdentity( string $username, string $password ) : static|null
	{
		$user = static::load(
			[
				'username' => $username,
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
				'username' => $username,
			]
		);
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function getUsername() : string
	{
		return $this->username;
	}

	public function setUsername( string $username ) : void
	{
		$this->username = $username;
	}

	public function getEmail() : string
	{
		return $this->email;
	}

	public function setEmail( string $email ) : void
	{
		$this->email = $email;
	}


	public function getShopId(): string
	{
		return $this->shop_id;
	}

	public function setShopId( string $shop_id ): void
	{
		$this->shop_id = $shop_id;
	}



	public function getFirstName() : string
	{
		return $this->first_name;
	}

	public function setFirstName( string $first_name ) : void
	{
		$this->first_name = $first_name;
	}

	public function getSurname() : string
	{
		return $this->surname;
	}

	public function setSurname( string $surname ) : void
	{
		$this->surname = $surname;
	}

	public function getName() : string
	{
		return $this->first_name.' '.$this->surname;
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

	public function hasPrivilege( string $privilege, mixed $value ) : bool
	{
		return false;
	}

	public function getPrivilegeValues( string $privilege ) : array
	{
		return [];
	}

	public function usernameExists( string $username ) : bool
	{
		if( $this->getIsNew() ) {
			$q = [
				'username' => $username,
			];
		} else {
			$q = [
				'username' => $username,
				'AND',
				'id!='     => $this->id,
			];
		}

		return (bool)static::getBackendInstance()->getCount( static::createQuery( $q ) );
	}

	public function resetPassword() : void
	{

		$password = static::generatePassword();

		$this->setPassword( $password );
		$this->setPasswordIsValid( false );
		$this->save();

		$shop = Shops::get($this->getShopId());

		$email = new Mailing_Email(
			'user_password_reset',
			$shop->getLocale(),
			$shop->getSiteId()
		);

		$email->setVar('user', $this);
		$email->setVar('password', $password);

		$email->send( $this->getEmail() );

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

	public function verifyPasswordStrength( string $password ) : bool
	{
		if( strlen( $password )<5 ) {
			return false;
		}

		return true;
	}

	public function _getForm() : Form
	{

		$form = $this->getCommonForm();

		$form->getField( 'username' )->setValidator(
			function( Form_Field_Input $field ) {
				$username = $field->getValue();

				if( $this->usernameExists( $username ) ) {
					$field->setCustomError(
						Tr::_(
							'Sorry, but username %USERNAME% is registered.', [ 'USERNAME' => $username ]
						)
					);

					return false;
				}

				return true;
			}
		);


		return $form;
	}

	public function getRegistrationForm() : Form
	{
		$form = $this->_getForm();
		$form->setName('register_user');

		foreach( $form->getFields() as $field ) {
			if( !in_array( $field->getName(), [ 'username', 'locale', 'password', 'email' ] ) ) {
				$form->removeField( $field->getName() );
			}
		}

		$form->getField( 'locale' )->setDefaultValue( Locale::getCurrentLocale() );

		/**
		 * @var Form_Field_RegistrationPassword $pwd
		 */
		$pwd = $form->getField( 'password' );
		$pwd->setPasswordConfirmationLabel('Confirm password');

		return $form;
	}

	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$form = $this->_getForm();
			$form->setName('_user');

			if( $form->fieldExists( 'password' ) ) {
				$form->removeField( 'password' );
			}



			$this->_form_edit = $form;
		}

		return $this->_form_edit;
	}

	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}

	public function getAddForm() : Form
	{
		if(!$this->_form_add) {

			$form = $this->_getForm();
			$form->setName('add_user');

			if( $form->fieldExists( 'password' ) ) {
				$form->removeField( 'password' );
			}


			$this->_form_add = $form;


		}

		return $this->_form_add;
	}

	public function catchAddForm() : bool
	{
		return $this->catchForm( $this->getAddForm() );
	}


	public function sendWelcomeEmail( string $password ) : void
	{
		$shop = Shops::get($this->getShopId());

		$email = new Mailing_Email(
			'user_welcome',
			$shop->getLocale(),
			$shop->getSiteId()
		);

		$email->setVar('user', $this);
		$email->setVar('password', $password);

		$email->send( $this->getEmail() );
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
	 * @param int $value
	 */
	public function setDefaultAddressId( int $value ) : void
	{
		$this->default_address_id = $value;
	}

	/**
	 * @return int
	 */
	public function getDefaultAddressId() : int
	{
		return $this->default_address_id;
	}

	/**
	 * @param string $value
	 */
	public function setPhoneNumber( string $value ) : void
	{
		$this->phone_number = $value;
	}

	/**
	 * @return string
	 */
	public function getPhoneNumber() : string
	{
		return $this->phone_number;
	}

	/**
	 * @param bool $value
	 */
	public function setMailingAccepted( bool $value ) : void
	{
		$this->mailing_accepted = (bool)$value;
	}

	/**
	 * @return bool
	 */
	public function getMailingAccepted() : bool
	{
		return $this->mailing_accepted;
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


}
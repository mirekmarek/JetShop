<?php
namespace JetShop;

use Jet\Auth_User_Interface;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Form_Field_RegistrationPassword;
use Jet\Form_Field_Select;
use Jet\Data_DateTime;
use Jet\Locale;
use Jet\Mailing_Email;
use Jet\Tr;


//TODO: shop ID ...
//TODO: locale dle shop id
#[DataModel_Definition(name: 'customer')]
#[DataModel_Definition(database_table_name: 'customers')]
#[DataModel_Definition(id_controller_class: DataModel_IDController_AutoIncrement::class)]
#[DataModel_Definition(id_controller_options: ['id_property_name'=>'id'])]
abstract class Core_Customer extends DataModel implements Auth_User_Interface
{

	#[DataModel_Definition(type: DataModel::TYPE_ID_AUTOINCREMENT)]
	#[DataModel_Definition(is_id: true)]
	#[DataModel_Definition(form_field_type: false)]
	protected int $id = 0;

	#[DataModel_Definition(type: DataModel::TYPE_STRING)]
	#[DataModel_Definition(max_len: 100)]
	#[DataModel_Definition(form_field_is_required: true)]
	#[DataModel_Definition(is_key: true)]
	#[DataModel_Definition(is_unique: true)]
	#[DataModel_Definition(form_field_label: 'Username')]
	#[DataModel_Definition(form_field_error_messages: [Form_Field_Input::ERROR_CODE_EMPTY=>'Please enter username'])]
	protected string $username = '';

	#[DataModel_Definition(type: DataModel::TYPE_STRING)]
	#[DataModel_Definition(do_not_export: true)]
	#[DataModel_Definition(max_len: 255)]
	#[DataModel_Definition(form_field_type: false)]
	protected string $password = '';

	#[DataModel_Definition(type: DataModel::TYPE_STRING)]
	#[DataModel_Definition(max_len: 255)]
	#[DataModel_Definition(form_field_label: 'E-mail')]
	#[DataModel_Definition(form_field_is_required: true)]
	#[DataModel_Definition(form_field_error_messages: [Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter e-mail address',Form_Field_Input::ERROR_CODE_INVALID_FORMAT => 'Please enter e-mail address'])]
	protected string $email = '';

	#[DataModel_Definition(type: DataModel::TYPE_LOCALE)]
	#[DataModel_Definition(form_field_label: 'Locale')]
	#[DataModel_Definition(form_field_is_required: true)]
	#[DataModel_Definition(form_field_error_messages: [Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select locale',Form_Field_Select::ERROR_CODE_EMPTY => 'Please select locale'])]
	#[DataModel_Definition(form_field_get_select_options_callback: [self::class, 'getLocales'])]
	protected ?Locale $locale = null;

	#[DataModel_Definition(type: DataModel::TYPE_STRING)]
	#[DataModel_Definition(max_len: 100)]
	#[DataModel_Definition(form_field_label: 'First name')]
	protected string $first_name = '';

	#[DataModel_Definition(type: DataModel::TYPE_STRING)]
	#[DataModel_Definition(max_len: 100)]
	#[DataModel_Definition(form_field_label: 'Surname')]
	protected string $surname = '';

	#[DataModel_Definition(type: DataModel::TYPE_STRING)]
	#[DataModel_Definition(max_len: 65536)]
	#[DataModel_Definition(form_field_label: 'Description')]
	protected string $description = '';

	#[DataModel_Definition(type: DataModel::TYPE_BOOL)]
	#[DataModel_Definition(default_value: true)]
	#[DataModel_Definition(form_field_label: 'Password is valid')]
	protected bool $password_is_valid = true;

	#[DataModel_Definition(type: DataModel::TYPE_DATE_TIME)]
	#[DataModel_Definition(default_value: null)]
	#[DataModel_Definition(form_field_label: 'Password is valid till')]
	#[DataModel_Definition(form_field_error_messages: [Form_Field_Input::ERROR_CODE_INVALID_FORMAT => 'Invalid date format'])]
	protected ?Data_DateTime $password_is_valid_till = null;

	#[DataModel_Definition(type: DataModel::TYPE_BOOL)]
	#[DataModel_Definition(default_value: false)]
	#[DataModel_Definition(form_field_label: 'User is blocked')]
	protected bool $user_is_blocked = false;

	#[DataModel_Definition(type: DataModel::TYPE_DATE_TIME)]
	#[DataModel_Definition(default_value: null)]
	#[DataModel_Definition(form_field_label: 'User is blocked till')]
	#[DataModel_Definition(form_field_error_messages: [Form_Field_Input::ERROR_CODE_INVALID_FORMAT => 'Invalid date format'])]
	protected ?Data_DateTime $user_is_blocked_till = null;

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

	public function getLocale() : Locale
	{
		return $this->locale;
	}

	public function setLocale( string|Locale $locale ) : void
	{
		if( !( $locale instanceof Locale ) ) {
			$locale = new Locale( $locale );
		}
		$this->locale = $locale;
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

		$email = new Mailing_Email(
			'user_password_reset',
			$this->getLocale(),
			Application_Web::getSiteId()
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

	public static function getLocales() : array
	{
		$locales = [];

		foreach( Application_Shop::getSite()->getLocales() as $locale_str=>$locale ) {
			$locales[$locale_str] = $locale->getName();
		}

		return $locales;
	}

	public function sendWelcomeEmail( string $password ) : void
	{
		$email = new Mailing_Email(
			'user_welcome',
			$this->getLocale(),
			Application_Shop::getSiteId()
		);

		$email->setVar('user', $this);
		$email->setVar('password', $password);

		$email->send( $this->getEmail() );
	}


}
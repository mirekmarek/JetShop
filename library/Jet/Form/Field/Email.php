<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace Jet;

/**
 *
 */
class Form_Field_Email extends Form_Field_Input
{
	/**
	 * @var string
	 */
	protected string $_type = Form_Field::TYPE_EMAIL;
	
	protected static ?Form_Field_Email_Validator $email_validator = null;
	
	/**
	 * @var array<string,string>
	 */
	protected array $error_messages = [
		Form_Field::ERROR_CODE_EMPTY          => 'Please enter a value',
		Form_Field::ERROR_CODE_INVALID_FORMAT => 'Invalid value',
	];
	
	public static function getEmailValidator(): ?Form_Field_Email_Validator
	{
		if(!static::$email_validator) {
			static::$email_validator = new Form_Field_Email_Validator();
		}
		
		return static::$email_validator;
	}
	
	public static function setEmailValidator( ?Form_Field_Email_Validator $email_validator ): void
	{
		static::$email_validator = $email_validator;
	}
	
	
	
	/**
	 * @return bool
	 */
	protected function validate_email() : bool
	{
		return static::getEmailValidator()->validate( $this, $this->_value );
	}
	
	/**
	 * validate value
	 *
	 * @return bool
	 */
	public function validate(): bool
	{
		
		if(
			!$this->validate_required() ||
			!$this->validate_email() ||
			!$this->validate_validator()
		) {
			return false;
		}
		
		$this->setIsValid();
		
		return true;
	}


	/**
	 * @return array<string>
	 */
	public function getRequiredErrorCodes(): array
	{
		$codes = [];

		if( $this->is_required ) {
			$codes[] = Form_Field::ERROR_CODE_EMPTY;
		}
		$codes[] = Form_Field::ERROR_CODE_INVALID_FORMAT;

		return $codes;
	}
}
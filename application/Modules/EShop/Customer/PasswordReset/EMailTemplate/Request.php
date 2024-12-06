<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\EShop\Customer\PasswordReset;

use Jet\Tr;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\EShop;

class EMailTemplate_Request extends EMail_Template {
	
	protected string $code;
	
	public function init() : void
	{
		$this->setInternalName(Tr::_('Customer password reset - email with code'));
		$this->setInternalNotes('');
		
		$code_property = $this->addProperty( 'code', Tr::_("Authorization code that will be sent to the user.") );
		$code_property->setPropertyValueCreator( function() : string {
			return $this->code;
		} );
	}
	
	public function initTest( EShop $eshop ): void
	{
		$this->code = '123456';
	}
	
	
	public function getCode(): string
	{
		return $this->code;
	}
	
	public function setCode( string $code ): void
	{
		$this->code = $code;
	}
	
	public function setupEMail( EShop $eshop, EMail $email ) : void
	{
	
	}
}
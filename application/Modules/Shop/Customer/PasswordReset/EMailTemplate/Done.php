<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\Customer\PasswordReset;

use Jet\Tr;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\Shops_Shop;

class EMailTemplate_Done extends EMail_Template {
	
	public function init() : void
	{
		$this->setInternalName(Tr::_('Customer password reset - password reset confirmation'));
		$this->setInternalNotes('');
		
	}
	
	public function setupEMail( Shops_Shop $shop, EMail $email ) : void
	{
	
	}
	
}
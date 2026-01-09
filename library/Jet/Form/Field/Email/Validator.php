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
class Form_Field_Email_Validator
{
	public function validate( Form_Field_Email $input, string $value ) : bool
	{
		if(
			$value &&
			!filter_var( $value, FILTER_VALIDATE_EMAIL )
		) {
			$input->setError( Form_Field::ERROR_CODE_INVALID_FORMAT );
			
			return false;
		}
		
		$domain = explode('@', $value)[1];
		
		
		if( !$this->checkDomain($domain) ) {
			$input->setError( Form_Field::ERROR_CODE_INVALID_FORMAT );
			
			return false;
		}
		
		return true;
	}
	
	protected function checkDomain( string $domain ) : bool
	{
		if(!filter_var($domain, FILTER_VALIDATE_DOMAIN )) {
			return false;
		}
		
		
		if( !checkdnsrr($domain, 'MX') ) {
			return false;
		}
		
		return true;
		
	}
}
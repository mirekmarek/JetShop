<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Login;


use Jet\Application_Module;

use Jet\Auth;

use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Form_Field_Password;
use Jet\Form_Field;


use JetApplication\Auth_Administrator_User as Administrator;



class Main extends Application_Module
{
	
	/**
	 *
	 * @return Form
	 */
	public function getLoginForm() : Form
	{
		$username_field = new Form_Field_Input( 'username', 'Username: ' );
		$username_field->setErrorMessages(
			[
				Form_Field::ERROR_CODE_EMPTY => 'Please enter username',
			]
		);
		$password_field = new Form_Field_Password( 'password', 'Password:' );
		$password_field->setErrorMessages(
			[
				Form_Field::ERROR_CODE_EMPTY => 'Please enter password',
			]
		);
		
		$form = new Form(
			'login', [
				$username_field,
				$password_field,
			]
		);
		
		$form->getField( 'username' )->setIsRequired( true );
		/**
		 * @var Form_Field_Password $password
		 */
		$password = $form->getField( 'password' );
		$password->setIsRequired( true );
		
		return $form;
	}
	
	/**
	 * @return Form
	 */
	public function getChangePasswordForm() : Form
	{
		$current_password = new Form_Field_Password( 'current_password', 'Current password' );
		$current_password->setIsRequired( true );
		$current_password->setErrorMessages(
			[
				Form_Field::ERROR_CODE_EMPTY => 'Please enter new password',
			
			]
		);
		
		$new_password = new Form_Field_Password( 'password', 'New password' );
		$new_password->setIsRequired( true );
		$new_password->setErrorMessages(
			[
				Form_Field::ERROR_CODE_EMPTY         => 'Please enter new password',
				Form_Field::ERROR_CODE_WEAK_PASSWORD => 'Password is not strong enough',
			]
		);
		
		$new_password->setValidator( function( Form_Field_Password $field ) : bool {
			if(!Administrator::verifyPasswordStrength($field->getValue())) {
				$field->setError( Form_Field::ERROR_CODE_WEAK_PASSWORD);
				return false;
			}
			
			return true;
		} );
		
		
		$new_password_check = $new_password->generateCheckField(
			field_name: 'password_check',
			field_label: 'Confirm new password',
			error_message_empty: 'Please confirm new password',
			error_message_not_match: 'Password confirmation do not match'
		);
		
		
		return new Form(
			'change_password', [
				$current_password,
				$new_password,
				$new_password_check,
			]
		);
	}
	
	/**
	 * @return Form
	 */
	public function getMustChangePasswordForm() : Form
	{
		$password = new Form_Field_Password( 'password', 'New password: ' );
		$password->setIsRequired( true );
		$password->setErrorMessages(
			[
				Form_Field::ERROR_CODE_EMPTY         => 'Please enter new password',
				Form_Field::ERROR_CODE_WEAK_PASSWORD => 'Password is not strong enough',
				'current_password_used'              => 'Please enter <strong>new</strong> password',
			]
		);
		
		$password->setValidator( function( Form_Field_Password $field ) : bool {
			if(!Administrator::verifyPasswordStrength($field->getValue())) {
				$field->setError( Form_Field::ERROR_CODE_WEAK_PASSWORD);
				return false;
			}
			
			if(Auth::getCurrentUser()->verifyPassword($field->getValue())) {
				$field->setError('current_password_used');
				return false;
			}
			
			return true;
		} );
		
		
		$password_check = $password->generateCheckField(
			field_name: 'password_check',
			field_label: 'Confirm new password',
			error_message_empty: 'Please confirm new password',
			error_message_not_match: 'Password confirmation do not match'
		);
		
		
		return new Form(
			'change_password', [
				$password,
				$password_check
			]
		);
	}
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */


namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\IO_Dir;
use Jet\IO_File;
use Jet\SysConf_Path;
use JetApplication\EShops;

class Session_Initializer {
	protected string $enc_method = 'AES-256-CBC';
	
	public function __construct()
	{
	}
	
	public function init() : Session
	{
		if(!isset($_COOKIE[Session::getVisitorCookieName()])) {
			$_COOKIE[Session::getVisitorCookieName()] = md5(time().uniqid().uniqid().uniqid());
		}
		
		if(!headers_sent()) {
			setcookie(
				name: Session::getVisitorCookieName(),
				value: $_COOKIE[Session::getVisitorCookieName()],
				expires_or_options: time() + (10 * 365 * 24 * 60 * 60),
				path: '/',
				secure: true,
				httponly: true
			);
		}

		
		if(!isset($_COOKIE[Session::getSessionCookieName()])) {
			return $this->startSession();
		}
		
		$session_id = $this->decryptData( $_COOKIE[Session::getSessionCookieName()] );
		
		return Session::load( $session_id ) ? : $this->startSession();
	}
	
	protected function startSession() : Session
	{
		$session = new Session();
		$session->newSessionStarted();
		
		$session_id = $this->encryptData( $session->getId() );
		
		$_COOKIE[Session::getSessionCookieName()] = $session_id;
		setcookie(
			name: Session::getSessionCookieName(),
			value: $session_id,
			expires_or_options: 0,
			path: '/',
			secure: true,
			httponly: true
		);
		
		return $session;
	}
	
	
	
	protected function encryptData( string $data ) : string {
		
		$key = $this->getSessionKey();
		
		$iv_length = openssl_cipher_iv_length( $this->enc_method );
		$iv = openssl_random_pseudo_bytes($iv_length);
		
		$encrypted = openssl_encrypt($data, $this->enc_method, $key, OPENSSL_RAW_DATA, $iv);
		
		return base64_encode( $iv . $encrypted );
	}
	
	protected function decryptData( string $data ) : string {
		$data = base64_decode($data);
		
		$key = $this->getSessionKey();
		
		$iv_length = openssl_cipher_iv_length($this->enc_method);
		$iv = substr($data, 0, $iv_length);
		$encrypted = substr($data, $iv_length);
		
		return openssl_decrypt($encrypted, $this->enc_method, $key, OPENSSL_RAW_DATA, $iv);
	}
	
	
	
	protected function getSessionKey() : string
	{
		$dir = SysConf_Path::getData().'ja_keys/';
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		$path = $dir.EShops::getCurrentKey().'.txt';
		
		if(
			IO_File::isReadable($path) &&
			($key = IO_File::read($path))
		) {
			return $key;
		}
		
		$key =  hash_pbkdf2(
			algo: 'sha256',
			password: md5(uniqid().uniqid().uniqid()),
			salt: md5(uniqid().uniqid().uniqid()),
			iterations: 1000
		);
		
		IO_File::write( $path, $key );
		
		return $key;
	}
}
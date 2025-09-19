<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

/** @noinspection SpellCheckingInspection */
namespace JetApplicationModule\MarketplaceIntegration\Heureka;


use Jet\IO_File;
use Jet\IO_File_Exception;

class HeurekaOvereno
{
	protected string $API_URL = '';
	protected string $API_key = '';
	
	protected int $order_id = 0;
	
	protected string $email = '';
	
	/**
	 * @var array<int,string>
	 */
	protected array $products = [];
	
	public function __construct( Config_PerShop $config )
	{
		$this->API_URL = $config->getOverenoAPIURL();
		$this->API_key = $config->getOverenoAPIKey();
	}
	
	public function setEmail( string $email) : void
	{
		$this->email = $email;
	}
	
	public function setOrderId( int $orderId) : void
	{
		$this->order_id = $orderId;
	}
	
	public function addProduct( int $product_id, string $product_name ) : void
	{
		$this->products[$product_id] = $product_name;
	}
	
	public function generateRequestURL() : string
	{
		$url = $this->API_URL . '?id=' . $this->API_key . '&email=' . urlencode($this->email);
		foreach ($this->products as $product_id=>$product_name) {
			$url .= '&itemId[]=' . $product_id;
			$url .= '&produkt[]=' . urlencode( $product_name );
		}
		
		if( $this->order_id ) {
			$url .= '&orderid=' . urlencode($this->order_id);
		}
		
		return $url;
	}
	
	public function send() : void
	{
		if( !$this->email ) {
			throw new HeurekaOvereno_Exception('Customer email address not set');
		}
		
		try {
			IO_File::read( $this->generateRequestURL() );
		} catch(IO_File_Exception $e) {
			throw new HeurekaOvereno_Exception($e->getMessage());
		}
	}
}
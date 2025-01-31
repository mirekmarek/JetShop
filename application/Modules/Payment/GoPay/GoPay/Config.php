<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Payment\GoPay;


class GoPay_Config {
	protected string $API_URL;
	protected string $client_ID;
	protected string $client_secret;
	protected string $go_ID;
	
	public function getAPIUrl(): string
	{
		return $this->API_URL;
	}
	
	public function setAPIUrl( string $API_URL ): void
	{
		$this->API_URL = $API_URL;
	}

	public function getClientID(): string
	{
		return $this->client_ID;
	}
	
	public function setClientID( string $client_ID ): void
	{
		$this->client_ID = $client_ID;
	}
	
	public function getClientSecret(): string
	{
		return $this->client_secret;
	}
	
	public function setClientSecret( string $client_secret ): void
	{
		$this->client_secret = $client_secret;
	}

	public function getGoID(): string
	{
		return $this->go_ID;
	}

	public function setGoID( string $go_ID ): void
	{
		$this->go_ID = $go_ID;
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\MarketplaceIntegration\Mall;


use Jet\Data_DateTime;

class ActiveLabel
{
	protected string $id = '';
	
	protected ?Data_DateTime $from = null;
	
	protected ?Data_DateTime $till = null;
	

	public function getId(): string
	{
		return $this->id;
	}
	

	public function setId( string $id ): void
	{
		$this->id = $id;
	}
	
	public function getFrom(): ?Data_DateTime
	{
		return $this->from;
	}
	
	public function setFrom( ?Data_DateTime $from ): void
	{
		$this->from = $from;
	}
	
	public function getTill(): ?Data_DateTime
	{
		return $this->till;
	}
	

	public function setTill( ?Data_DateTime $till ): void
	{
		$this->till = $till;
	}
	
	public function expired() : bool
	{
		$now = Data_DateTime::now();
		
		if(
			!$this->till ||
			$this->till>$now
		) {
			return false;
		}
		
		return true;
	}
	
	
}
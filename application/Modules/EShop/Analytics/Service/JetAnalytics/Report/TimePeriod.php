<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Data_DateTime;
use Jet\Tr;

abstract class Report_TimePeriod
{
	public const KEY = null;
	
	protected ?string $title = null;
	
	protected bool $is_default = false;
	
	protected Data_DateTime $from;
	protected Data_DateTime $to;
	
	protected function setupFrom( string $from ) : void
	{
		$this->from = new Data_DateTime( date('Y-m-d', strtotime( $from )) );
		$this->from->setTime(0, 0, 0);
	}
	
	protected function setupTo( string $to ) : void
	{
		$this->to = new Data_DateTime( date('Y-m-d', strtotime( $to )) );
		$this->to->setTime(23, 59, 59);
	}
	
	public static function getKey() : string
	{
		return static::KEY;
	}
	
	public function getTitle() : string
	{
		return Tr::_($this->title);
	}
	
	abstract public function __construct();
	
	public function isDefault(): bool
	{
		return $this->is_default;
	}
	
	public function getFrom(): Data_DateTime
	{
		return $this->from;
	}
	
	public function getTo(): Data_DateTime
	{
		return $this->to;
	}
	
	
	
	
}
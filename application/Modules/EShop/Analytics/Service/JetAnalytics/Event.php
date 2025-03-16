<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */


namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Data_DateTime;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;


#[DataModel_Definition(
	name: 'ja_event',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
abstract class Event extends DataModel implements EShopEntity_HasEShopRelation_Interface
{
	use EShopEntity_HasEShopRelation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $session_id = 0;
	
	protected Session $session;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_time = null;
	
	public static function getEventType() : string
	{
		return str_replace( Event::class.'_', '', static::class );
	}
	
	public static function create() : static
	{
		$session = Session::getCurrent();
		
		$event = new static();
		$event->session_id = $session->getId();
		$event->session = $session;
		$event->date_time = Data_DateTime::now();
		$event->setEshop( $session->getEshop() );
		
		return $event;
	}
	
	public function getDateTime(): ?Data_DateTime
	{
		return $this->date_time;
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function getSessionId(): int
	{
		return $this->session_id;
	}
	
	
	
	abstract public function cancelDefaultEvent() : bool;

}

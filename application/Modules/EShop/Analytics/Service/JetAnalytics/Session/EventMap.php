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

#[DataModel_Definition(
	name: 'ja_session_event_map',
	database_table_name: 'ja_session_event_map',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Session_EventMap extends DataModel
{
	
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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $event_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $event_id = 0;
	
	
	public static function create( Session $session, Event $event ) : static
	{
		$item = new static();
		$item->session_id = $session->getId();
		$item->event_type = $event::getEventType();
		$item->date_time = $event->getDateTime();
		$item->event_id = $event->getId();
		
		$item->save();
		return $item;
	}
	
	/**
	 * @return static[]
	 */
	public static function getMap( Session $session ) : array
	{
		return static::fetch(
			[''=>[
				'session_id' => $session->getId(),
			]],
			order_by: '-id'
		);
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function getSessionId(): int
	{
		return $this->session_id;
	}
	
	public function getSession(): Session
	{
		return $this->session;
	}
	
	public function getDateTime(): ?Data_DateTime
	{
		return $this->date_time;
	}
	
	public function getEventType(): string
	{
		return $this->event_type;
	}
	
	public function getEventId(): int
	{
		return $this->event_id;
	}
	
	public function getEvent(): Event
	{
		/**
		 * @var Event $class
		 */
		$class = Event::class.'_'.$this->getEventType();
		
		return $class::load( $this->event_id );
	}
	
}
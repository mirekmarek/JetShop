<?php
namespace JetShop;

use Exception;
use Jet\BaseObject_Interface_Serializable_JSON;
use Jet\Data_DateTime;
use Jet\Tr;
use JetApplication\Availabilities;
use JetApplication\Availability;
use JetApplication\Calendar;
use JetApplication\DeliveryTerm;
use JetApplication\EShops;
use JetApplication\EShop;

abstract class Core_DeliveryTerm_Info implements BaseObject_Interface_Serializable_JSON {
	
	protected Availability $availability;
	
	protected EShop $eshop;
	
	protected bool $is_virtual_product = false;
	
	protected float $number_of_units_available = 0.0;
	
	protected int $length_of_delivery = 0;
	
	protected bool $allow_to_order_more = true;
	
	protected string $situation = '';
	
	protected string $delivery_info_text = '';
	
	protected ?Data_DateTime $available_from_date = null;
	
	
	public function __construct() {
	}
	
	public function getAvailability(): Availability
	{
		return $this->availability;
	}
	
	public function setAvailability( Availability $availability ): void
	{
		$this->availability = $availability;
	}
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	public function setEshop( EShop $eshop ): void
	{
		$this->eshop = $eshop;
	}
	
	
	
	public function getIsVirtualProduct(): bool
	{
		return $this->is_virtual_product;
	}
	
	public function setIsVirtualProduct( bool $is_virtual_product ): void
	{
		$this->is_virtual_product = $is_virtual_product;
	}
	
	
	
	public function getNumberOfUnitsAvailable(): float
	{
		return $this->number_of_units_available;
	}
	
	public function setNumberOfUnitsAvailable( float $number_of_units_available ): void
	{
		$this->number_of_units_available = $number_of_units_available;
	}
	
	public function getLengthOfDelivery(): int
	{
		return $this->length_of_delivery;
	}
	
	public function setLengthOfDelivery( int $length_of_delivery ): void
	{
		$this->length_of_delivery = $length_of_delivery;
	}
	
	public function getSituation(): string
	{
		return $this->situation;
	}
	
	public function setSituation( string $situation ): void
	{
		$this->situation = $situation;
	}
	
	public function getDeliveryInfoText(): string
	{
		return $this->delivery_info_text;
	}
	
	public function setDeliveryInfoText( string $delivery_info_text ): void
	{
		$this->delivery_info_text = $delivery_info_text;
	}
	
	
	public function getDeliveryInfoTextTranslated(): string
	{
		$text =  $this->delivery_info_text;
		$locale = $this->eshop->getLocale();
		
		return Tr::_(
			text: $text,
			data: [
				'date' => $locale->formatDate( $this->available_from_date )
			],
			dictionary: DeliveryTerm::getManager()->getModuleManifest()->getName(),
			locale: $locale
		);
	}

	
	public function getAvailableFromDate(): ?Data_DateTime
	{
		return $this->available_from_date;
	}
	
	public function setAvailableFromDate( ?Data_DateTime $available_from_date ): void
	{
		$this->available_from_date = $available_from_date;
	}
	
	public function getAllowToOrderMore(): bool
	{
		return $this->allow_to_order_more;
	}
	
	public function setAllowToOrderMore( bool $allow_to_order_more ): void
	{
		$this->allow_to_order_more = $allow_to_order_more;
	}
	
	
	public function getEstimatedArrivalDateByDeliveryMethod( int $length_of_delivery ): ?Data_DateTime
	{
		if( $this->is_virtual_product ) {
			return null;
		}
		
		$length_of_delivery += $this->length_of_delivery;
		
		$available_from = $this->available_from_date;
		if(!$available_from) {
			$available_from = 'now';
		}
		
		return Calendar::getNextBusinessDate(
			eshop: $this->eshop,
			number_of_working_days: $length_of_delivery,
			start_date: $available_from
		);
	}
	
	public function allowToOrder( float $units_to_order=1 ) : bool
	{
		if($units_to_order<=$this->number_of_units_available) {
			return true;
		}
		
		return $this->allow_to_order_more;
	}
	
	public static function fromJSON( string $json ) : static
	{
		$data = json_decode( $json, true );
		
		$item = new static();

		try {
			$item->is_virtual_product = $data['is_virtual_product'];
			$item->number_of_units_available = $data['number_of_units_available'];
			$item->length_of_delivery = $data['length_of_delivery'];
			$item->allow_to_order_more = $data['allow_to_order_more'];
			$item->situation = $data['situation'];
			$item->delivery_info_text = $data['delivery_info_text'];
			$item->available_from_date = Data_DateTime::catchDate( $data['available_from_date'] );
			$item->availability = Availabilities::get( $data['availability'] );
			$item->eshop = EShops::get( $data['eshop']??EShops::getDefault()->getKey() );
		} catch( Exception $e ) {
		
		}
		
		return $item;
	}
	
	public function toJSON(): string
	{
		return json_encode( $this->jsonSerialize() );
	}
	
	public function jsonSerialize(): array
	{
		$data = get_object_vars( $this );
		
		$data['eshop'] = $this->eshop->getKey();
		$data['availability'] = $this->availability->getCode();
		
		return $data;
	}
}
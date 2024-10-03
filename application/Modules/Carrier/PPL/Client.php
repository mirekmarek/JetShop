<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Carrier\PPL;

use JetApplication\Carrier_DeliveryPoint;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

class Client {
	protected Main $carrier;
	
	public function __construct( Main $carrier )
	{
		$this->carrier = $carrier;
	}
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 */
	public function downloadUpToDateDeliveryPointsList(): array
	{
		$res = [];
		
		foreach(Shops::getList() as $shop) {
			foreach( $this->_downloadUpToDateDeliveryPointsList( $shop ) as $item ) {
				$res[$item->getKey()] = $item;
			}
		}
		
		return $res;
	}
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 */
	public function _downloadUpToDateDeliveryPointsList( Shops_Shop $shop ): array
	{
		//TODO:
		return [];
	}
	
}
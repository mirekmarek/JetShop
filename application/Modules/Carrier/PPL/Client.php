<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Carrier\PPL;

use JetApplication\Carrier_DeliveryPoint;
use JetApplication\EShops;
use JetApplication\EShop;

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
		
		foreach( EShops::getList() as $eshop) {
			foreach( $this->_downloadUpToDateDeliveryPointsList( $eshop ) as $item ) {
				$res[$item->getKey()] = $item;
			}
		}
		
		return $res;
	}
	
	/**
	 * @return Carrier_DeliveryPoint[]
	 */
	public function _downloadUpToDateDeliveryPointsList( EShop $eshop ): array
	{
		//TODO:
		return [];
	}
	
}
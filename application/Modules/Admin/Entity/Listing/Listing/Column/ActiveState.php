<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops;

class Listing_Column_ActiveState extends Listing_Column_Abstract
{
	public const KEY = 'active_state';
	
	protected ?bool $multi_shop_mode = null;
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function isMultiShopMode(): bool
	{
		if($this->multi_shop_mode===null) {
			/**
			 * @var Listing $listing
			 */
			$listing = $this->listing;
			
			$entity = $listing->getEntity();
			
			$this->multi_shop_mode = ($entity instanceof Entity_WithShopData);
			
			if(
				$this->multi_shop_mode &&
				!Shops::isMultiShopMode()
			)  {
				$this->multi_shop_mode = false;
			}
		}
		
		return $this->multi_shop_mode;
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Is active');
	}
	
	public function getDisallowSort(): bool
	{
		return true;
	}
	
	
	public function initializer( UI_dataGrid_column $column ) : void
	{
		$column->addCustomCssStyle( 'width:120px;' );
	}
	
	public function getExportHeader(): array
	{
		$titles = ['general'=> Tr::_('Is active')];
		
		if($this->isMultiShopMode()) {
			$shops = Shops::getListSorted();
			foreach($shops as $shop) {
				$titles[$shop->getKey()] = Tr::_('Is active - %what%', ['what'=>$shop->getShopName()]);
			}
		}
		
		return $titles;
	}
	
	public function getExportData( mixed $item ): array
	{
		/**
		 * @var Entity_WithShopData $item
		 */
		
		$data = [
			'general' => $item->isActive() ? 1 : 0
		];
		
		
		if($this->isMultiShopMode()) {
			$shops = Shops::getListSorted();
			foreach($shops as $shop) {
				$data[$shop->getKey()] = $item->getShopData($shop)->isActiveForShop() ? 1 : 0;
			}
		}
		
		return $data;
	}
	
	
}
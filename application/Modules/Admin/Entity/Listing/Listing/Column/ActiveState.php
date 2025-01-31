<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;


use Jet\Tr;
use Jet\UI_dataGrid_column;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShops;

class Listing_Column_ActiveState extends Listing_Column_Abstract
{
	public const KEY = 'active_state';
	
	protected ?bool $multi_eshop_mode = null;
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function isMultiEShopMode(): bool
	{
		if($this->multi_eshop_mode===null) {
			/**
			 * @var Listing $listing
			 */
			$listing = $this->listing;
			
			$entity = $listing->getEntity();
			
			$this->multi_eshop_mode = ($entity instanceof EShopEntity_WithEShopData);
			
			if(
				$this->multi_eshop_mode &&
				!EShops::isMultiEShopMode()
			)  {
				$this->multi_eshop_mode = false;
			}
		}
		
		return $this->multi_eshop_mode;
	}
	
	
	public function getTitle(): string
	{
		return Tr::_('Is active', dictionary: Tr::COMMON_DICTIONARY);
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
		$titles = ['general'=> Tr::_('Is active', dictionary: Tr::COMMON_DICTIONARY)];
		
		if($this->isMultiEShopMode()) {
			$eshops = EShops::getListSorted();
			foreach($eshops as $eshop) {
				$titles[$eshop->getKey()] = Tr::_('Is active - %what%', ['what'=>$eshop->getName()], dictionary: Tr::COMMON_DICTIONARY);
			}
		}
		
		return $titles;
	}
	
	public function getExportData( mixed $item ): array
	{
		/**
		 * @var EShopEntity_WithEShopData $item
		 */
		
		$data = [
			'general' => $item->isActive() ? 1 : 0
		];
		
		
		if($this->isMultiEShopMode()) {
			$eshops = EShops::getListSorted();
			foreach($eshops as $eshop) {
				$data[$eshop->getKey()] = $item->getEshopData($eshop)->isActiveForShop() ? 1 : 0;
			}
		}
		
		return $data;
	}
	
	
}
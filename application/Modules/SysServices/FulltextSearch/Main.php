<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\FulltextSearch;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Brand;
use JetApplication\Category;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\KindOfProduct;
use JetApplication\Product;
use JetApplication\Property;
use JetApplication\PropertyGroup;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;



class Main extends Application_Module implements SysServices_Provider_Interface
{
	
	public function getSysServicesDefinitions(): array
	{
		$entities = [
			Category::class,
			Brand::class,
			KindOfProduct::class,
			Property::class,
			PropertyGroup::class,
			Product::class,
		];
		
		$services = [];
		
		foreach( $entities as $class ) {
			/**
			 * @var EShopEntity_WithEShopData $class
			 */
			$et = $class::getEntityType();
			
			$actualize_index = new SysServices_Definition(
				module: $this,
				name: Tr::_('Actualize fulltext search indexes - '.$et.' - all'),
				description: Tr::_('Ad hoc service for updating of full-text indexes'),
				service_code: 'actualize_fulltext:'.$et.':all',
				service: function() use ($class) {
					$this->updateIndex( class: $class, active: true, non_active: true );
				}
			);
			$actualize_index->setIsPeriodicallyTriggeredService( false );
			
			$services[] = $actualize_index;
			
			
			$actualize_index = new SysServices_Definition(
				module: $this,
				name: Tr::_('Actualize fulltext search indexes - '.$et.' - only active items'),
				description: Tr::_('Ad hoc service for updating of full-text indexes'),
				service_code: 'actualize_fulltext:'.$et.':active-only',
				service: function() use ($class) {
					$this->updateIndex( class: $class, active: true, non_active: false );
				}
			);
			$actualize_index->setIsPeriodicallyTriggeredService( false );
			
			$services[] = $actualize_index;
			
		}
		
		return $services;
	}
	
	public function updateIndex( string $class, bool $active, bool $non_active ) : void
	{
		/**
		 * @var EShopEntity_WithEShopData $class
		 */
		$et = $class::getEntityType();
		
		
		if($active) {
			$ids = $class::dataFetchCol(['id'], where: ['is_active'=>true], raw_mode: true);
			$count = count($ids);
			$i = 0;
			
			foreach( $ids as $id ) {
				$i++;
				echo "Active $et: [{$i}/{$count}] {$id}\n";
				
				$item = $class::get($id);
				$item->updateFulltextSearchIndex();
			}
		}
		
		
		if($non_active) {
			$ids = $class::dataFetchCol(['id'], where: ['is_active'=>false], raw_mode: true);
			$count = count($ids);
			$i = 0;
			
			foreach( $ids as $id ) {
				$i++;
				echo "Non-active $et: [{$i}/{$count}] {$id}\n";
				
				$item = $class::get($id);
				$item->updateFulltextSearchIndex();
			}
		}
		
	}
}
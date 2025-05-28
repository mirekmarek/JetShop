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
		$actualize_index = new SysServices_Definition(
			module: $this,
			name: Tr::_('Actualize fulltext search indexes'),
			description: Tr::_('Ad hoc service for complete updating of full-text indexes'),
			service_code: 'actualize_fulltext',
			service: function() {
				$this->updateIndexes();
			}
		);
		
		$actualize_index->setIsPeriodicallyTriggeredService( false );
		
		return [
			$actualize_index
		];
	}
	
	protected function updateIndexes() : void
	{
		$this->updateIndex( Category::class );
		$this->updateIndex( Brand::class );
		$this->updateIndex( KindOfProduct::class );
		$this->updateIndex( Property::class );
		$this->updateIndex( PropertyGroup::class );
		$this->updateIndex( Product::class );
	}
	
	public function updateIndex( string $class ) : void
	{
		/**
		 * @var EShopEntity_WithEShopData $class
		 */
		$et = $class::getEntityType();
		
		$ids = $class::dataFetchCol(['id'], where: ['is_active'=>true], raw_mode: true);
		$count = count($ids);
		$i = 0;
		
		foreach( $ids as $id ) {
			$i++;
			echo "Active $et: [{$i}/{$count}] {$id}\n";
			
			$item = $class::get($id);
			$item->updateFulltextSearchIndex();
		}
		
		
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
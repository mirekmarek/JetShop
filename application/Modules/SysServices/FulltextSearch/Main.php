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
		
		$page = 0;
		$limit = 1000;
		$i = 0;
		do {
			$i++;
			$offset = $page * $limit;
			$items = $class::fetchInstances();
			$items->getQuery()->setLimit( $limit, $offset );
			
			$end = true;
			foreach( $items as $c ) {
				
				$index = ($page*$limit)+$i;
				
				$i++;
				echo "$et: [{$index}] {$c->getId()}\n";
				$c->updateFulltextSearchIndex();
				$end = false;
			}
			
			if($end) {
				break;
			}
			$page++;
			
		} while(true);
		
	}
}
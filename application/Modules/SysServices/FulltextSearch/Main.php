<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\FulltextSearch;


use Jet\Application_Module;
use Jet\Db;
use Jet\Locale;
use Jet\Tr;
use JetApplication\Brand;
use JetApplication\Category;
use JetApplication\Content_Article;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\FulltextSearch_Dictionary;
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
			Content_Article::class,
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
		
		
		$import_dictionary = new SysServices_Definition(
			module: $this,
			name: Tr::_('Import old dictionary - CZ'),
			description: Tr::_('Import old dictionary - CZ'),
			service_code: 'import_old_dictionary:cz',
			service: function() use ($class) {
				$this->importOldDictionary(
					new Locale('cs_CZ'),
					'fulltext_dictionary'
				);
			}
		);
		$import_dictionary->setIsPeriodicallyTriggeredService( false );
		$services[] = $import_dictionary;
		
		$import_dictionary = new SysServices_Definition(
			module: $this,
			name: Tr::_('Import old dictionary - SK'),
			description: Tr::_('Import old dictionary - SK'),
			service_code: 'import_old_dictionary:sk',
			service: function() use ($class) {
				$this->importOldDictionary(
					new Locale('sk_SK'),
					'sk_fulltext_dictionary'
				);

			}
		);
		$import_dictionary->setIsPeriodicallyTriggeredService( false );
		$services[] = $import_dictionary;
		
		
		return $services;
	}
	
	public function importOldDictionary( Locale $locale, string $old_table ) : void
	{
		FulltextSearch_Dictionary::dataDelete(where: [
			'locale' => $locale,
		]);
		
		$old = Db::get('old')->fetchAll("SELECT note, words FROM {$old_table}");
		foreach( $old as $o ) {
			$rec = new FulltextSearch_Dictionary();
			$rec->setLocale( $locale );
			$rec->setWords( $o['words'] );
			$rec->setNote( $o['note'] );
			$rec->save();
		}
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
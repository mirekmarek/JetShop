<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;

use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Admin_Managers;
use JetApplication\Product;


class Controller_Main extends Admin_EntityManager_Controller
{
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorName() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorEmail() );
		$this->listing_manager->addColumn( new Listing_Column_Created() );
		$this->listing_manager->addColumn( new Listing_Column_Source() );
		
		$this->listing_manager->setSearchWhereCreator(  function( string $search ) : array {
			
			$q['author_name *'] = '%'.$search.'%';
			
			$q[] = 'OR';
			$q['author_email *'] = '%'.$search.'%';
			
			$products = Admin_Managers::FulltextSearch()->search( Product::getEntityType(), $search );
			if($products) {
				$q[] = 'OR';
				$q['product_id'] = $products;
			}
			
			return $q;
			
		} );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'eshop',
			'status',
			'product_id',
			'created',
			'author_name',
			'author_email',
			'source'
		]);
	}
	
	protected function edit_main_initPlugins(): void
	{
		Plugin::initPlugins( $this->view, $this->current_item );
		$this->getEditorManager()->setPlugins( Plugin::getPlugins() );
		
		if(Main::getCurrentUserCanEdit()) {
			Plugin::handlePlugins();
		}
	}
	
}
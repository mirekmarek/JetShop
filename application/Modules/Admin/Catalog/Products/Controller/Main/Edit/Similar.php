<?php
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Product;


/**
 *
 */
trait Controller_Main_Edit_Similar
{
	
	
	public function edit_similar_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Similar') );
		
		/**
		 * @var Product $product
		 */
		$product = $this->current_item;
		
		$GET = Http_Request::GET();
		
		if($GET->exists('add')) {
			$product->addSimilar( explode(',', $GET->getString('add')) );
			Http_Headers::reload([], ['add']);
		}
		
		if($GET->exists('sort')) {
			$product->sortSimilar( explode(',', $GET->getString('sort')) );
			Http_Headers::reload([], ['sort']);
		}
		
		if($GET->exists('delete')) {
			$product->deleteSimilar( $GET->getInt('delete') );
			Http_Headers::reload([], ['delete']);
		}

		$this->view->setVar('item', $product);
		$this->output( 'edit/similar' );
	}
	
	
}
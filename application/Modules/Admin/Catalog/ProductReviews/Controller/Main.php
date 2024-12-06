<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\ProductReviews;

use Jet\Http_Headers;
use JetApplication\Admin_EntityManager_WithEShopRelation_Controller;


class Controller_Main extends Admin_EntityManager_WithEShopRelation_Controller
{
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('approve', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action) {
				return ($action=='approve' && $this->current_item);
			});
		
		$this->router->addAction('reject', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action) {
				return ($action=='reject' && $this->current_item);
			});

	}
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_Rank() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorName() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorEmail() );
		$this->listing_manager->addColumn( new Listing_Column_Approved() );
		$this->listing_manager->addColumn( new Listing_Column_Created() );
		$this->listing_manager->addColumn( new Listing_Column_Source() );
		
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'eshop',
			'product_id',
			'created',
			'author_name',
			'author_email',
			'rank',
			'approved',
			'source'
		]);
	}
	
	public function approve_Action() : void
	{
		/**
		 * @var ProductReview $review
		 */
		$review = $this->current_item;
		$review->approve();
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
	public function reject_Action() : void
	{
		/**
		 * @var ProductReview $review
		 */
		$review = $this->current_item;
		$review->reject();
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
}
<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\ProductReviewsAssessing;

use Jet\AJAX;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use JetApplication\ProductReview;
use JetApplication\ProductReview_Status_New;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$reviews = ProductReview::fetch(
			[''=> [
				'status' => ProductReview_Status_New::CODE
			]],
			order_by: ['-id'],
			item_key_generator: function( ProductReview $review ) {
				return $review->getId();
			}
		);
		
		$POST = Http_Request::POST();
		
		if($POST->getString('operation')) {
			$item = $reviews[$POST->getInt('id')];
			
			switch($POST->getString('operation')) {
				case 'save':
					$value = $POST->getString('value');
					
					switch($POST->getString('what')) {
						case 'positive_characteristics': $item->setPositiveCharacteristics( $value ); break;
						case 'negative_characteristics': $item->setNegativeCharacteristics( $value ); break;
						case 'summary': $item->setSummary( $value ); break;
						case 'our_comments': $item->setOurComments( $value ); break;
					}
					$item->save();
					
					AJAX::operationResponse(true);
					break;
				case 'reject':
					$item->approve();
					Http_Headers::reload();
					break;
				case 'approve':
					$item->reject();
					Http_Headers::reload();
					break;
			}
			
		}
		
		$this->view->setVar('reviews', $reviews);
		
		$this->output('default');
	}
}
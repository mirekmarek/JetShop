<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;

use Jet\Http_Headers;
use JetApplication\Admin_EntityManager_WithEShopRelation_Controller;
use JetApplication\ProductQuestion;


class Controller_Main extends Admin_EntityManager_WithEShopRelation_Controller
{
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('answer_display', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action) {
				return ($action=='answer_display' && $this->current_item);
			});
		
		$this->router->addAction('answer_do_not_display', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action) {
				return ($action=='answer_do_not_display' && $this->current_item);
			});
		
		$this->router->addAction('not_answered', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action) {
				return ($action=='not_answered' && $this->current_item);
			});
		
	}
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorName() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorEmail() );
		$this->listing_manager->addColumn( new Listing_Column_Answered() );
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
			'answered',
			'source'
		]);
	}
	
	public function answer_display_Action() : void
	{
		/**
		 * @var ProductQuestion $question
		 */
		$question = $this->current_item;
		$question->answerAndDisplay();
		
		$this->sendAnswerByEmail();
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
	public function answer_do_not_display_Action() : void
	{
		/**
		 * @var ProductQuestion $question
		 */
		$question = $this->current_item;
		$question->answerAndDoNotDisplay();
		
		$this->sendAnswerByEmail();
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
	public function not_answered_Action() : void
	{
		/**
		 * @var ProductQuestion $review
		 */
		$review = $this->current_item;
		$review->isNotAnswered();
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
	protected function sendAnswerByEmail() : void
	{
		$email_template = new EmailTemplate_Answer();
		
		$email_template->setQuestion( $this->current_item );
		$email = $email_template->createEmail( $this->current_item->getEshop() );
		$email->send();
	}
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ProductQuestions;


use Jet\AJAX;
use Jet\Application_Module;
use Jet\Form;
use Jet\Form_Field_Email;
use Jet\Form_Field_Input;
use Jet\Form_Field_Textarea;
use Jet\Translator;
use JetApplication\Product_EShopData;
use JetApplication\ProductQuestion;
use JetApplication\EShop_Managers_ProductQuestions;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;
use JetApplication\EShops;
use JetApplication\Customer;


class Main extends Application_Module implements EShop_Managers_ProductQuestions, EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	protected ProductQuestion $new_question;
	protected ?Form $new_question_form=null;
	
	public function renderQuestions( Product_EShopData $product ): string
	{
		
		return Translator::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($product) {
				$view = $this->getView();
				
				$form = $this->getNewQuestionForm( $product );
				
				$view->setVar('form', $form );
				
				if( $form->catchInput() ) {
					if(!$form->validate()) {
						AJAX::operationResponse(
							false,
							[
								'new_question_form' => $view->render('new_question_form')
							]
						);
					} else {
						$form->catchFieldValues();
						
						$this->new_question->save();
						
						AJAX::operationResponse(
							false,
							[
								'new_question_form' => $view->render('question_saved')
							]
						);
						
					}
				}
				
				
				$questions = ProductQuestion::getQuestions( $product );
				
				$view->setVar('questions', $questions);
			
				return $view->render('default');
			}
		);
	}
	
	protected function getNewQuestionForm( Product_EShopData $product ) : Form
	{
		if(!$this->new_question_form) {
			$customer = Customer::getCurrentCustomer();
			
			$this->new_question = new ProductQuestion();
			$this->new_question->setEshop( EShops::getCurrent() );
			$this->new_question->setProductId( $product->getId() );
			
			if($customer) {
				$this->new_question->setAuthorEmail( $customer->getEmail() );
				$this->new_question->setAuthorName( $customer->getName() );
				$this->new_question->setCustomerId( $customer->getId() );
			}
			
			$this->new_question_form = new Form('new_question',[]);
			
			$question = new Form_Field_Textarea('question', 'Question:');
			$question->setFieldValueCatcher( function( $value ) {
				$this->new_question->setQuestion( $value );
			} );
			$question->setIsRequired( true );
			$question->setErrorMessages([
				Form_Field_Textarea::ERROR_CODE_EMPTY => 'Please enter question'
			]);
			$this->new_question_form->addField( $question );
			
			if(!$customer) {
				$author_name = new Form_Field_Input('author_name', 'Your name:');
				$author_name->setFieldValueCatcher( function( $value ) {
					$this->new_question->setAuthorName( $value );
				} );
				$this->new_question_form->addField( $author_name );
				
				$author_email = new Form_Field_Email('author_email', 'Your e-mail:');
				$author_email->setFieldValueCatcher( function( $value ) {
					$this->new_question->setAuthorEmail( $value );
				} );
				$author_email->setIsRequired( true );
				$author_email->setErrorMessages([
					Form_Field_Email::ERROR_CODE_EMPTY => 'Please enter your e-mail',
					Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Please enter your e-mail'
				]);
				
				$this->new_question_form->addField( $author_email );
				
			}
			
		}
		
		return $this->new_question_form;
	}
}
<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;

use Jet\Tr;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\Product_EShopData;
use JetApplication\EShop;
use JetApplication\Product;

class EmailTemplate_Answer extends EMail_Template
{
	
	protected ProductQuestion $question;
	
	public function initTest( EShop $eshop ): void
	{
		$products = Product::dataFetchCol(['id'], limit: 1000, raw_mode: true);
		$id_key = array_rand( $products, 1 );
		
		$this->question = new ProductQuestion();
		$this->question->setEshop( $eshop );
		$this->question->setProductId( $products[$id_key] );
		$this->question->setQuestion('Test question');
		$this->question->setAnswer('Test answer');
	}
	
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Product question answer'));
		$this->setInternalNotes('');
		
		$product_URL_property = $this->addProperty(
			'product_URL',
			Tr::_('URL of product')
		);
		$product_URL_property->setPropertyValueCreator(function() : string {
			$product = Product_EShopData::get( $this->question->getProductId(), $this->getQuestion()->getEshop() );
			return $product?->getURL()??'';
		});
		
		$product_name_property = $this->addProperty(
			'product_name',
			Tr::_('Name of product')
		);
		$product_name_property->setPropertyValueCreator( function() : string {
			$product = Product_EShopData::get( $this->question->getProductId(), $this->getQuestion()->getEshop() );
			return $product?->getName()??'';
		} );
		
		$question_property = $this->addProperty(
			'question',
			Tr::_('Question')
		);
		$question_property->setPropertyValueCreator( function() : string {
			return nl2br($this->question->getQuestion());
		} );
		
		$answer_property = $this->addProperty(
			'answer',
			Tr::_('Answer')
		);
		$answer_property->setPropertyValueCreator( function() : string {
			return nl2br($this->question->getAnswer());
		} );
		
	}
	
	public function getQuestion(): ProductQuestion
	{
		return $this->question;
	}

	public function setQuestion( ProductQuestion $question ): void
	{
		$this->question = $question;
	}
	
	public function setupEMail( EShop $eshop, EMail $email ) : void
	{
		$email->setContext('product_question');
		$email->setContextId( $this->question->getId() );
		$email->setContextCustomerId( $this->question->getCustomerId() );
		$email->setSaveHistoryAfterSend( true );
		$email->setTo( $this->question->getAuthorEmail() );
	}
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\ProductQuestion\AnsweredNotDisplayed;


use Jet\Tr;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\EShop;
use JetApplication\Product_EShopData;
use JetApplication\ProductQuestion;

class EMailTemplate extends EMail_Template
{
	protected ProductQuestion $question;
	
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Product question - answer - not displayed'));
		
		
		$this->addProperty('product_url', Tr::_('Product - URL'))
			->setPropertyValueCreator( function() : string {
				$p = Product_EShopData::get(
					$this->question->getProductId(),
					$this->question->getEshop()
				);
				
				return $p?->getURL()??'';
			} );
		
		$this->addProperty('product_name', Tr::_('Product - name'))
			->setPropertyValueCreator( function() : string {
				$p = Product_EShopData::get(
					$this->question->getProductId(),
					$this->question->getEshop()
				);
				
				return $p?->getName()??'';
			} );
		
		
		$this->addProperty('question', Tr::_('Question'))
			->setPropertyValueCreator( function() : string {
				return nl2br($this->question->getQuestion());
			} );
		
		$this->addProperty('answer', Tr::_('Answer'))
			->setPropertyValueCreator( function() : string {
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
	
	public function setupEMail( EShop $eshop, EMail $email ): void
	{
		$email->setContext( ProductQuestion::getEntityType() );
		$email->setContextId( $this->question->getId() );

		$email->setSaveHistoryAfterSend( true );
		$email->setTo( $this->question->getAuthorEmail() );
	}
	
	public function initTest( EShop $eshop ): void
	{
		$ids = ProductQuestion::dataFetchCol(
			select: ['id'],
			where: $eshop->getWhere(),
			order_by: '-id',
			limit: 1000
		);
		$id_key = array_rand( $ids, 1 );
		$id = $ids[$id_key];
		
		$this->question = ProductQuestion::get($id);
	}
}
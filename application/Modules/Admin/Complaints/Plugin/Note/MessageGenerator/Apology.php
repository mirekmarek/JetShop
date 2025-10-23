<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;


use Jet\Data_DateTime;
use Jet\Tr;
use JetApplication\Discounts_Code;
use JetApplication\Discounts_Discount;

class Plugin_Note_MessageGenerator_Apology extends Plugin_Note_MessageGenerator
{
	public const KEY = 'apology';
	
	public function getTitle(): string
	{
		return Tr::_('Apology');
	}
	
	public function generateSubject(): string
	{
		return $this->renderSubject();
	}
	
	public function generateText(): string
	{
		
		$discounts_code = Discounts_Code::generate(
			eshop: $this->complaint->getEshop(),
			prefix: 'SR-',
			length: 8,
			setup: function( Discounts_Code $discounts_code ) {
				$discounts_code->setMinimalOrderAmount( 0 );
				$discounts_code->setNumberOfCodesAvailable( 1 );
				$discounts_code->setDiscountType( Discounts_Discount::DISCOUNT_TYPE_PRODUCTS_AMOUNT );
				$discounts_code->setDiscount( (float)$this->view->render('discount-amount') );
				$discounts_code->setActiveTill( new Data_DateTime( date('Y-m-d 23:59:59', strtotime('+6 months')) ) );
				$discounts_code->setInternalNotes( 'Apology about the complaint '.$this->complaint->getNumber() );
			}
		);
		
		$this->view->setVar('discounts_code', $discounts_code );
		
		return $this->renderText( true );
	}
	
}
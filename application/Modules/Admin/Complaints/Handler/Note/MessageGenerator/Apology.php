<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Complaints;

use Jet\Data_DateTime;
use Jet\Tr;
use JetApplication\Discounts_Code;
use JetApplication\Discounts_Discount;

class Handler_Note_MessageGenerator_Apology extends Handler_Note_MessageGenerator
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
		
		do {
			$d_code = 'SR-';
			$length = 8;
			
			/** @noinspection SpellCheckingInspection */
			$characters = '0123456789ABCDEFGHJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			
			for ($i = 0; $i < $length; $i++) {
				$d_code .= $characters[random_int(0, $charactersLength - 1)];
			}
			
			
		} while( Discounts_Code::getByCode( $d_code, $this->complaint->getEshop() ) );
		
		$discounts_code = new Discounts_Code();
		$discounts_code->setEshop( $this->complaint->getEshop() );
		$discounts_code->setMinimalOrderAmount(0);
		$discounts_code->setNumberOfCodesAvailable( 1 );
		$discounts_code->setDiscountType( Discounts_Discount::DISCOUNT_TYPE_PRODUCTS_AMOUNT );
		$discounts_code->setCode( $d_code );
		$discounts_code->setActiveTill( new Data_DateTime( date('Y-m-d 23:59:59', strtotime('+6 months')) ) );
		$discounts_code->setInternalNotes( 'Apology about the complaint '.$this->complaint->getNumber());
		
		
		$this->view->setVar('discounts_code', $discounts_code );
		
		return $this->renderText( true );
	}
	
}
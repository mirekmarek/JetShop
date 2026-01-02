<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\VirtualProductHandler\Vouchers;

use Jet\Data_DateTime;
use JetApplication\EShop;
use JetApplication\PDF;
use JetApplication\PDF_Template;

class PDFTemplate extends PDF_Template {
	
	protected Data_DateTime $valid_till;
	protected string $coupon_image_path;
	
	protected function init(): void
	{
		$this->setInternalName('Dárkový poukaz');
		$this->setInternalNotes('');
		
		$this->addProperty('VALID_TILL', '')
			->setPropertyValueCreator(function() {
				return $this->eshop->getLocale()->formatDate( $this->valid_till );
			});
		
		$this->addProperty('coupon_image_path', '')
			->setPropertyValueCreator(function() {
				return $this->coupon_image_path;
			});
	}
	
	public function initTest( EShop $eshop ): void
	{
		$this->valid_till = Data_DateTime::now();
	}
	
	public function getValidTill(): Data_DateTime
	{
		return $this->valid_till;
	}
	
	public function setValidTill( Data_DateTime $valid_till ): void
	{
		$this->valid_till = $valid_till;
	}
	
	public function getCouponImagePath(): string
	{
		return $this->coupon_image_path;
	}
	
	public function setCouponImagePath( string $coupon_image_path ): void
	{
		$this->coupon_image_path = $coupon_image_path;
	}
	
	
	
	
	public function setupPDF( EShop $eshop, PDF $pdf ): void
	{
	}
}
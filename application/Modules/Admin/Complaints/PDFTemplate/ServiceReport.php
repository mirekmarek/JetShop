<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;

use Jet\Data_DateTime;
use Jet\Tr;
use JetApplication\Complaint;
use JetApplication\Complaint_ComplaintType_WarrantyClaim;
use JetApplication\EShop;
use JetApplication\PDF;
use JetApplication\PDF_Template;
use JetApplication\Product;


class PDFTemplate_ServiceReport extends PDF_Template
{
	protected Complaint $complaint;
	
	public function getComplaint(): Complaint
	{
		return $this->complaint;
	}
	
	public function setComplaint( Complaint $complaint ): void
	{
		$this->complaint = $complaint;
	}
	
	
	
	protected function init(): void
	{
		$this->addProperty('complaint_number', Tr::_('Complaint number'))
			->setPropertyValueCreator( function() {
				return $this->complaint->getNumber();
			});
		
		$this->addProperty('complaint_type', Tr::_('Complaint type'))
			->setPropertyValueCreator( function() {
				return $this->complaint->getComplaintType()->getTitle( $this->complaint->getEshop()->getLocale() );
			});
		
		$this->addProperty('order_number', Tr::_('Order number'))
			->setPropertyValueCreator( function() {
				return $this->complaint->getOrderNumber();
			});
		
		$this->addProperty('complaint_date_time', Tr::_('Complaint date and time'))
			->setPropertyValueCreator( function() {
				return $this->complaint->getEshop()->getLocale()->dateAndTime( $this->complaint->getDateStarted() );
			});
		
		
		
		$this->addProperty('customer_name', Tr::_('Customer - name'))
			->setPropertyValueCreator( function() {
				if( $this->complaint->getDeliveryCompanyName() ) {
					return $this->complaint->getDeliveryCompanyName();
				}
				
				return $this->complaint->getDeliveryFirstName().' '.$this->complaint->getDeliverySurname();
			});
		
		
		$this->addProperty('customer_address', Tr::_('Customer - address'))
			->setPropertyValueCreator( function() {
				return $this->complaint->getDeliveryAddressStreetNo().'<br>'.
					$this->complaint->getDeliveryAddressZip().' '.$this->complaint->getDeliveryAddressTown();
			});
		
		
		$this->addProperty('customer_phone', Tr::_('Customer - phone'))
			->setPropertyValueCreator( function() {
				return $this->complaint->getPhone();
			});
		
		$this->addProperty('customer_email', Tr::_('Customer - e-mail'))
			->setPropertyValueCreator( function() {
				return $this->complaint->getEmail();
			});
		
		$this->addProperty('product_name', Tr::_('Product name'))
			->setPropertyValueCreator( function() {
				$p = Product::get( $this->complaint->getProductId() );
				return $this->complaint->getProduct()->getName().' / '.$p->getInternalCode();
			});
		
		$this->addProperty('problem_description', Tr::_('Problem description'))
			->setPropertyValueCreator( function() {
				return nl2br($this->complaint->getProblemDescription());
			});
		
		$this->addProperty('service_report', Tr::_('Service report'))
			->setPropertyValueCreator( function() {
				return nl2br($this->complaint->getServiceReport());
			});

	}
	
	public function initTest( EShop $eshop ): void
	{
		$complaint = new Complaint();
		$complaint->setEshop( $eshop );
		
		$complaint->setNumber('123456789');
		$complaint->setComplaintTypeCode( Complaint_ComplaintType_WarrantyClaim::getCode() );
		$complaint->setOrderNumber( '987654321' );
		$complaint->setDateStarted( Data_DateTime::now() );
		$complaint->setDeliveryFirstName('Josef');
		$complaint->setDeliverySurname('Novák');
		$complaint->setDeliveryAddressStreetNo( 'Kulaté náměstí 123/99' );
		$complaint->setDeliveryAddressZip( '12345' );
		$complaint->setDeliveryAddressTown( 'Kocourkov' );
		$complaint->setPhone('+420 321 321 321');
		$complaint->setEmail('josef@novakov.no');
		
		$product_ids = Product::dataFetchCol(
			select: ['id'],
			where: ['is_active'=>true],
			raw_mode: true
		);
		
		shuffle($product_ids);
		
		$complaint->setProductId( $product_ids[0] );
		
		$complaint->setProblemDescription( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus quis neque quis justo ornare dictum. Etiam non odio ligula. Nullam vitae vehicula lorem. Duis bibendum eros urna, ut lobortis urna suscipit quis. Proin ut luctus turpis, gravida imperdiet velit. Curabitur malesuada nunc non urna facilisis, in dignissim dui eleifend. Curabitur bibendum felis id lacinia ultricies. Integer tincidunt mauris sem, ut lacinia erat commodo et. Fusce pretium leo a sem auctor cursus. Vestibulum sed ex sit amet justo pharetra pulvinar in id nibh. Donec a aliquam magna. Quisque rutrum orci tellus, sit amet congue urna ultricies id. Praesent vel leo vel massa dapibus dignissim. Suspendisse lobortis ornare odio, eu sagittis sapien interdum in.');
		$complaint->setServiceReport( 'Cras mattis leo nec turpis tincidunt, id imperdiet felis gravida. Praesent viverra lectus eu risus euismod porta. In euismod massa neque, a cursus felis euismod ac. Donec mollis est interdum, tempus dui a, dictum felis. Sed tincidunt porttitor lorem nec pellentesque. Sed sed tortor id est efficitur finibus. Phasellus quam nulla, mattis non posuere eu, rutrum sed erat.' );
		
		$this->complaint = $complaint;
	}
	
	public function setupPDF( EShop $eshop, PDF $pdf ): void
	{
	}
}

<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Complaints;

use Jet\Locale;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\Complaint;
use JetApplication\Complaint_Image;
use JetApplication\EMail;
use JetApplication\EMail_Template;
use JetApplication\EShop;
use JetApplication\EShops;
use JetApplication\Product;

class EMailTemplate_NewImageNotification extends EMail_Template {
	
	protected Complaint $complaint;
	/**
	 * @var Complaint_Image[]
	 */
	protected array $images;
	
	protected function init(): void
	{
		$this->setInternalName('Complaint - new image/file notification');
		
		Tr::setCurrentDictionaryTemporary(
			dictionary: 'EShop.Complaints',
			action: function() {
				$this->addProperty('complaint_number', Tr::_('Complaint number'))
					->setPropertyValueCreator( function() {
						return $this->complaint->getNumber();
					});
				
				$this->addProperty('complaint_type', Tr::_('Complaint type'))
					->setPropertyValueCreator( function() {
						return Tr::setCurrentDictionaryTemporary(
							dictionary: 'Admin.Complaints',
							action: function() {
								return $this->complaint->getComplaintType()?->getTitle( $this->complaint->getEshop()->getLocale() )??'';
							}
						);
					});
				
				
				$this->addProperty('prefered_solution', Tr::_('Prefered solution'))
					->setPropertyValueCreator( function() {
						return Tr::setCurrentDictionaryTemporary(
							dictionary: 'Admin.Complaints',
							action: function() {
								return $this->complaint->getPreferredSolution()?->getTitle( $this->complaint->getEshop()->getLocale() )??'';
							}
						);
					});
				
				
				$this->addProperty('delivery_of_claimed_goods', Tr::_('Delivery of claimed goods'))
					->setPropertyValueCreator( function() {
						return Tr::setCurrentDictionaryTemporary(
							dictionary: 'Admin.Complaints',
							action: function() {
								return $this->complaint->getDeliveryOfClaimedGoods()?->getTitle( $this->complaint->getEshop()->getLocale() )??'';
							}
						);
					});
				
				
				$this->addProperty('order_number', Tr::_('Order number'))
					->setPropertyValueCreator( function() {
						return $this->complaint->getOrderNumber()?:'--';
					});
				
				$this->addProperty('bill_number', Tr::_('Bill number'))
					->setPropertyValueCreator( function() {
						return $this->complaint->getBillNumber()?:'--';
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
				
				$this->addProperty('url_admin', Tr::_('URL - Admin'))
					->setPropertyValueCreator( function() {
						return Application_Service_Admin::Complaint()::getEditUrl( $this->complaint );
					});
				
				$this->addProperty('url_detail', Tr::_('URL - detail'))
					->setPropertyValueCreator( function() {
						return $this->complaint->getURL();
					});
				
				$block = $this->addPropertyBlock('images', Tr::_('Images'));
				$block->setItemListCreator( function() {
					return $this->images;
				});
				
				$file_url = $block->addProperty('file_url', Tr::_('File URL'));
				$file_url->setPropertyValueCreator(function( Complaint_Image $file ) {
					return $file->getUrl();
				});
				
				$file_url = $block->addProperty('file_thb', Tr::_('File URL'));
				$file_url->setPropertyValueCreator(function( Complaint_Image $file ) {
					$thb_url = $file->getThbURL();
					if(!$thb_url) {
						return '';
					}
					
					return '<img src="'.$thb_url.'" style="max-width:100px;height:auto;">';
				});
				
				
				$file_url = $block->addProperty('file_name', Tr::_('File URL'));
				$file_url->setPropertyValueCreator(function( Complaint_Image $file ) {
					return $file->getName();
				});
				
				
				$file_url = $block->addProperty('file_size', Tr::_('File URL'));
				$file_url->setPropertyValueCreator(function( Complaint_Image $file ) {
					return Locale::size($file->getSize());
				});

				
				
			}
		);
		
	}
	
	public function setupEMail( EShop $eshop, EMail $email ): void
	{
	}
	
	public function initTest( EShop $eshop ): void
	{
		$complaint_ids = Complaint_Image::dataFetchCol(
			select: ['complaint_id']
		);
		
		$complaint_ids = array_unique($complaint_ids);
		shuffle($complaint_ids);
		
		$complaint_id = Complaint::dataFetchOne(
			select: ['id'],
			where: [
				EShops::getCurrent()->getWhere(),
				'AND',
				'id' => $complaint_ids
			],
			limit: 1
		);
		
		$complaint = Complaint::get( $complaint_id );
		$this->setComplaint( $complaint );
		
		$this->setImages( $complaint->getImages() );
	}
	
	public function setComplaint( Complaint $complaint ): void
	{
		$this->complaint = $complaint;
	}
	
	/**
	 * @param array<Complaint_Image> $images
	 * @return void
	 */
	public function setImages( array $images ): void
	{
		$this->images = $images;
	}
	
	
}
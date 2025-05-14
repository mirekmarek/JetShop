<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI;
use JetApplication\Admin_Listing_Operation;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasActivation_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShops;


class Listing_Operation_Deactivate extends Admin_Listing_Operation
{
	public const KEY = 'deactivate';
	protected string $title = 'Deactivate';
	
	public function getForm(): Form
	{
		if(!$this->form) {
			/**
			 * @var Listing $listing
			 */
			$listing = $this->listing;
			$entity = $listing->getEntity();
			
			$this->form = $this->createStdForm();
			
			$master = new Form_Field_Checkbox('master', Tr::_('Master switch', dictionary: Tr::COMMON_DICTIONARY));
			$master->setDoNotTranslateLabel( true );
			$master->setDefaultValue(true);
			$this->form->addField($master);
			
			if($entity instanceof EShopEntity_WithEShopData) {
				if(EShops::isMultiEShopMode()) {
					foreach(EShops::getListSorted() as $eshop) {
						$per_eshop = new Form_Field_Checkbox( $eshop->getKey(),  UI::flag($eshop->getLocale()).' '.$eshop->getName() );
						$per_eshop->setDoNotTranslateLabel( true );
						$per_eshop->setDefaultValue( true );
						$this->form->addField( $per_eshop );
					}
				}
			}
		}
		
		return $this->form;
	}
	
	public function canBeHandled(): bool
	{
		return true;
	}
	
	
	public function perform(): void
	{
		$form = $this->getForm();
		
		if( $form->catch() ) {
			
			foreach($this->getIds() as $id ) {
				/**
				 * @var Listing $listing
				 */
				$listing = $this->listing;
				$entity = $listing->getEntity();
				
				/**
				 * @var EShopEntity_Basic|EShopEntity_HasActivation_Interface|EShopEntity_WithEShopData $item
				 */
				$item = $entity::get($id);
				if(!$item) {
					continue;
				}
				
				if($form->field('master')->getValue()) {
					$item->deactivate();
				}
				
				if($entity instanceof EShopEntity_WithEShopData) {
					if(EShops::isMultiEShopMode()) {
						foreach(EShops::getList() as $eshop) {
							if($form->field($eshop->getKey())->getValue()) {
								$item->deactivateEShopData( $eshop );
							}
						}
					} else {
						foreach(EShops::getList() as $eshop) {
							$item->deactivateEShopData( $eshop );
						}
					}
				}
			}
			
			Http_Headers::reload();
		}
		
	}
	
}
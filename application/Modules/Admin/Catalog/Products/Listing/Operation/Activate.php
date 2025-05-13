<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI;
use JetApplication\Admin_Listing_Operation;
use JetApplication\EShops;
use JetApplication\Product;


class Listing_Operation_Activate extends Admin_Listing_Operation
{
	public const KEY = 'activate';
	protected string $title = 'Activate';
	
	public function getForm(): Form
	{
		if(!$this->form) {
			$this->form = $this->createStdForm();
			
			$master = new Form_Field_Checkbox('master', Tr::_('Master switch', dictionary: Tr::COMMON_DICTIONARY));
			$master->setDoNotTranslateLabel( true );
			$master->setDefaultValue(true);
			$this->form->addField($master);
			if(EShops::isMultiEShopMode()) {
				foreach(EShops::getList() as $eshop) {
					$per_eshop = new Form_Field_Checkbox( $eshop->getKey(),  UI::flag($eshop->getLocale()).' '.$eshop->getName() );
					$per_eshop->setDoNotTranslateLabel( true );
					$per_eshop->setDefaultValue( true );
					$this->form->addField( $per_eshop );
				}
			}
		}
		
		return $this->form;
	}
	
	public function canBeHandled(): bool
	{
		return Main::getCurrentUserCanEdit();
	}
	
	
	public function perform(): void
	{
		$form = $this->getForm();
		
		if( $form->catch() ) {
			
			foreach($this->getIds() as $id ) {
				$p = Product::get($id);
				if(!$id) {
					continue;
				}
				
				if($form->field('master')) {
					$p->activate();
				}
				
				if(EShops::isMultiEShopMode()) {
					foreach(EShops::getList() as $eshop) {
						if($form->field($eshop->getKey())) {
							$p->activateEShopData( $eshop );
						}
					}
				} else {
					foreach(EShops::getList() as $eshop) {
						$p->activateEShopData( $eshop );
					}
				}
			}
			
			Http_Headers::reload();
		}
		
	}
	
}
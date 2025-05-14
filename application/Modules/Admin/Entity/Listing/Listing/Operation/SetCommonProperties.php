<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\Form;
use Jet\Http_Headers;
use JetApplication\Admin_Listing_Operation;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasActivation_Interface;
use JetApplication\EShopEntity_WithEShopData;


class Listing_Operation_SetCommonProperties extends Admin_Listing_Operation
{
	public const KEY = 'set_common_properties';
	protected string $title = 'Set common properties';
	protected array $properties = [];
	
	public function getForm(): Form
	{
		if(!$this->form) {
			/**
			 * @var Listing $listing
			 */
			$listing = $this->listing;
			$entity = $listing->getEntity();
			
			$this->form = $entity->createListingActionCommonPropertiesEditForm();
			
			foreach($this->createStdForm()->getFields() as $field) {
				$this->form->addField($field);
			}
			
			$def = EShopEntity_Definition::get( $entity );
			foreach($def->getProperties() as $property_name => $property) {
				if($property->isEditableByListingAction()) {
					$this->properties[] = $property_name;
				}
			}
			
		}
		
		return $this->form;
	}
	
	public function canBeHandled(): bool
	{
		return true;
	}
	
	public function getProperties(): array
	{
		return $this->properties;
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

				if($item->catchListingActionCommonPropertiesEditForm()) {
					$item->save();
				}
			}
			
			Http_Headers::reload();
		}
		
	}
	
}
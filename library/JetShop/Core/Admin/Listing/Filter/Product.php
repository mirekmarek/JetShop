<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter;
use JetApplication\Admin_Managers;

abstract class Core_Admin_Listing_Filter_Product extends Admin_Listing_Filter
{
	protected string $label;
	
	protected int $product_id = 0;
	protected Form_Field_Hidden $form_field;
	
	public function catchParams(): void
	{
		$key = $this::getKey();
		
		$this->product_id = Http_Request::GET()->getInt( $key );
		if($this->product_id) {
			$this->listing->setParam($key, $this->product_id);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$key = $this::getKey();
		
		$this->form_field = new Form_Field_Hidden($key, $this->label );
		$this->form_field->setDefaultValue( $this->product_id );
		$form->addField( $this->form_field );
	}
	
	public function catchForm( Form $form ): void
	{
		$this->product_id = (int)$this->form_field->getValue();
		if($this->product_id) {
			$this->listing->setParam( $this::getKey() , $this->product_id);
		} else {
			$this->listing->unsetParam( $this::getKey() );
		}
	}
	
	abstract public function generateWhere(): void;
	
	public function isActive(): bool
	{
		return $this->product_id > 0;
	}
	
	public function renderForm() : string
	{
		return Admin_Managers::EntityListing()->renderListingFilter(
			filter:      $this,
			title:       Tr::_($this->label),
			form_fields: [$this->form_field],
			is_active:   $this->isActive(),
			renderer:    function() {
				?>
				<script>
					function filterProduct_<?=$this::getKey()?>( id ) {
						const input = document.getElementById('<?=$this->form_field->getId()?>');
						input.value = id;
						input['form'].submit();
					}
				</script>
				<div style="width: 400px;">
					<?=Admin_Managers::Product()->renderSelectWidget(
						"filterProduct_{$this::getKey()}(selected_item.id)",
						$this->form_field->getValue()
					)?>
					<?= $this->form_field->input() ?>
				</div>
				<?php
			}
		);
		
	}
	
}
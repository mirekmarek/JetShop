<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;



use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Complaint;

class Plugin_ChangeProduct_Main extends Plugin {
	public const KEY = 'change_product';
	
	protected Form $form;
	
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
		/**
		 * @var Complaint $item
		 */
		$item = $this->item;
		
		$this->form = new Form('change_product_form', []);
		
		
		$product = new Form_Field_Hidden('product', '' );
		$product->setDefaultValue( $item->getProductId() );
		
		$this->form->addField( $product );
		
		$this->view->setVar('change_product_form', $this->form);
		
	}
	
	public function canBeHandled(): bool
	{
		return true;
	}
	
	public function getForm(): Form
	{
		return $this->form;
	}
	
	public function handle() : void
	{
		/**
		 * @var Complaint $item
		 */
		$item = $this->item;
		
		if($this->form->catch()) {
			$product_id = $this->form->field('product')->getValue();
			if($product_id) {
				$item->setProductId( $product_id );
				$item->save();
			}
			
			UI_messages::success( Tr::_( 'Product has been changed' ) );
			
			AJAX::operationResponse(true);
		}
	}
	
	
	
}
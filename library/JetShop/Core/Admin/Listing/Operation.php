<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Autoloader;
use Jet\DataListing_Operation;
use Jet\Factory_MVC;
use Jet\Form;
use Jet\Form_Field_Hidden;
use Jet\Form_Field_Input;
use Jet\Form_Field_RadioButton;
use Jet\MVC_View;
use Jet\Tr;

abstract class Core_Admin_Listing_Operation extends DataListing_Operation
{
	public const AFFECT_ALL_FILTERED = 'all_filtered';
	public const AFFECT_SELECTED = 'selected';
	
	public const KEY = null;
	protected string $title;
	protected ?MVC_View $view = null;
	protected ?Form $form = null;
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function getTitle(): string
	{
		return Tr::_( $this->title );
	}
	
	protected function getView() : MVC_View
	{
		if($this->view === null) {
			$view_dir = dirname( Autoloader::getScriptPath( static::class ), 3 ).'/views/list/operation/';
			
			$this->view = Factory_MVC::getViewInstance( $view_dir );
			$this->view->setVar('operation', $this);
		}
		
		return $this->view;
	}
	
	protected function createStdForm() : Form
	{
		$form = new Form( 'list_operation_form_'.$this->getKey(), [] );
		
		$affect = new Form_Field_RadioButton('affect',  Tr::_( 'What to affect:', dictionary: Tr::COMMON_DICTIONARY));
		$affect->setSelectOptions(
			[
				static::AFFECT_ALL_FILTERED => Tr::_( 'All filtered items', dictionary: Tr::COMMON_DICTIONARY ),
				static::AFFECT_SELECTED     => Tr::_( 'Manually selected items', dictionary: Tr::COMMON_DICTIONARY ),
			]
		);
		$affect->setDefaultValue( static::AFFECT_SELECTED );
		$affect->setDoNotTranslateLabel( true );
		
		$form->addField( $affect );
		
		$selected_ids = new Form_Field_Input('selected_ids');
		$selected_ids->input()->addCustomCssClass('selected_ids');
		$selected_ids->row()->addCustomCssStyle('display: none');
		
		$form->addField( $selected_ids );
		
		
		return $form;
	}
	
	protected function getIds() : array
	{
		
		$form = $this->form;
		$all_ids = $this->listing->getAllIds();
		
		switch($form->field('affect')->getValue()) {
			case static::AFFECT_ALL_FILTERED:
				return $all_ids;

			case static::AFFECT_SELECTED:
				$_ids = $this->form->field('selected_ids')->getValue();
				$_ids = explode( ',', $_ids );
				
				$ids = [];
				foreach( $_ids as $id ) {
					if(in_array( $id, $all_ids )) {
						$ids[] = $id;
					}
				}
				
				return $ids;
		}
		
		return [];
	}
	
	
	public function renderForm(): string
	{
		return $this->getView()->render( $this->getKey() );
	}
	
	abstract public function canBeHandled() : bool;
	
}
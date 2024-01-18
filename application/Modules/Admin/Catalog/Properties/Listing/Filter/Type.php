<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;


class Listing_Filter_Type extends DataListing_Filter
{
	public const KEY = 'type';
	
	protected string $type = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->type = Http_Request::GET()->getString('type', '', array_keys(Property::getTypesScope()));
		if($this->type) {
			$this->listing->setParam('type', $this->type);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + Property::getTypesScope();
		
		$type = new Form_Field_Select('type', 'Type:' );
		$type->setDefaultValue( $this->type );
		$type->setSelectOptions( $options );
		$type->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($type);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->type = $form->field('type')->getValue();
		if($this->type) {
			$this->listing->setParam('type', $this->type);
		} else {
			$this->listing->unsetParam('type');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->type) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'type'   => $this->type,
		]);
	}
	
}
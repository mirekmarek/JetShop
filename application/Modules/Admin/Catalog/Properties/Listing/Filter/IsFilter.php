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


class Listing_Filter_IsFilter extends DataListing_Filter
{
	public const KEY = 'is_filter';
	
	protected string $is_filter = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	protected function getScope() : array
	{
		return [
			'' => Tr::_('- all -'),
			'y' => Tr::_('yes'),
			'n' => Tr::_('no')
		];
	}
	
	public function catchParams(): void
	{
		$this->is_filter = Http_Request::GET()->getString('is_filter', '', array_keys($this->getScope()));
		if($this->is_filter!=='') {
			$this->listing->setParam('is_filter', $this->is_filter);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = $this->getScope();
		
		$is_filter = new Form_Field_Select('is_filter', 'Is filter:' );
		$is_filter->setDefaultValue( $this->is_filter );
		$is_filter->setSelectOptions( $options );
		$is_filter->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($is_filter);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->is_filter = $form->field('is_filter')->getValue();
		if($this->is_filter!=='') {
			$this->listing->setParam('is_filter', $this->is_filter);
		} else {
			$this->listing->unsetParam('is_filter');
		}
	}
	
	public function generateWhere(): void
	{
		if($this->is_filter=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'is_filter'   => $this->is_filter=='y',
		]);
	}
	
}
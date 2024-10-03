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


class Listing_Filter_IsDefaultFilter extends DataListing_Filter
{
	public const KEY = 'is_default_filter';
	
	protected string $is_default_filter = '';
	
	
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
		$this->is_default_filter = Http_Request::GET()->getString('is_default_filter', '', array_keys($this->getScope()));
		if($this->is_default_filter!=='') {
			$this->listing->setParam('is_default_filter', $this->is_default_filter);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = $this->getScope();
		
		$is_default_filter = new Form_Field_Select('is_default_filter', 'Is filter:' );
		$is_default_filter->setDefaultValue( $this->is_default_filter );
		$is_default_filter->setSelectOptions( $options );
		$is_default_filter->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($is_default_filter);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->is_default_filter = $form->field('is_default_filter')->getValue();
		if($this->is_default_filter!=='') {
			$this->listing->setParam('is_default_filter', $this->is_default_filter);
		} else {
			$this->listing->unsetParam('is_default_filter');
		}
	}
	
	public function generateWhere(): void
	{
		if($this->is_default_filter=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'is_default_filter'   => $this->is_default_filter=='y',
		]);
	}
	
}
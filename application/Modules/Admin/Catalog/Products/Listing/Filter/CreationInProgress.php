<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Products;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;


class Listing_Filter_CreationInProgress extends DataListing_Filter
{
	public const KEY = 'creation_in_progress';
	
	protected string $creation_in_progress = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->creation_in_progress = Http_Request::GET()->getString('creation_in_progress', '', ['', '1', '0']);
		if($this->creation_in_progress) {
			$this->listing->setParam('creation_in_progress', $this->creation_in_progress);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [
			''  => Tr::_('- all -', dictionary: Tr::COMMON_DICTIONARY),
			'1' => Tr::_('yes', dictionary: Tr::COMMON_DICTIONARY),
			'0' => Tr::_('no', dictionary: Tr::COMMON_DICTIONARY),
		];
		
		$creation_in_progress = new Form_Field_Select('creation_in_progress', 'Creation in progress:' );
		$creation_in_progress->setDefaultValue( $this->creation_in_progress );
		$creation_in_progress->setSelectOptions( $options );
		
		$form->addField($creation_in_progress);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->creation_in_progress = $form->field('creation_in_progress')->getValue();
		if($this->creation_in_progress) {
			$this->listing->setParam('creation_in_progress', $this->creation_in_progress);
		} else {
			$this->listing->unsetParam('creation_in_progress');
		}
	}
	
	public function generateWhere(): void
	{
		if($this->creation_in_progress=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'creation_in_progress'   => (bool)$this->creation_in_progress,
		]);
	}
	
}
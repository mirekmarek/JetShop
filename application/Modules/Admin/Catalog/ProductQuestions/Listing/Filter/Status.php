<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Catalog\ProductQuestions;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;


class Listing_Filter_Status extends DataListing_Filter
{
	public const KEY = 'status';
	
	protected string $status = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->status = Http_Request::GET()->getString('status', '', ['', 'w', 'as','a']);
		if($this->status) {
			$this->listing->setParam('status', $this->status);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [
			'' => Tr::_(' - all -'),
			'w' => Tr::_('Awaiting answer'),
			'as' => Tr::_('Answered - display'),
			'a' => Tr::_('Answered - do not display'),
		];
		
		$brand = new Form_Field_Select('status', 'Status:' );
		$brand->setDefaultValue( $this->status );
		$brand->setSelectOptions( $options );
		$brand->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($brand);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->status = $form->field('status')->getValue();
		if($this->status) {
			$this->listing->setParam('status', $this->status);
		} else {
			$this->listing->unsetParam('status');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->status) {
			return;
		}
		
		switch($this->status) {
			
			case 'w': //Awaiting answer
				$this->listing->addFilterWhere([
					'answered' => false,
				]);
				break;
			case 'as': //Answered - display
				$this->listing->addFilterWhere([
					'answered' => true,
					'AND',
					'display' => true
				]);
				break;
			case 'a': //Answered - do not display
				$this->listing->addFilterWhere([
					'answered' => true,
					'AND',
					'display' => false
				]);
				break;
			
		}
		
	}
	
}
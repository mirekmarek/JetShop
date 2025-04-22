<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Entity\Listing;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Form_Field_Select_Option;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter;
use JetApplication\EShopEntity_Status;

class Listing_Filter_Status extends Admin_Listing_Filter
{
	public const KEY = 'status';
	
	protected string $status = '';
	
	
	/**
	 * @var EShopEntity_Status[]
	 */
	protected array $status_list;
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->status = Http_Request::GET()->getString('status', '', array_keys($this->getStatusList()));
		if($this->status) {
			$this->listing->setParam('status', $this->status);
		}
	}
	
	public function setStatusList( array $list ) : void
	{
		$this->status_list = $list;
	}
	
	/**
	 * @return EShopEntity_Status[]
	 */
	public function getStatusList() : array
	{
		return $this->status_list;
		
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')];
		foreach( $this->status_list as $status ) {
			$opt = new Form_Field_Select_Option( $status->getTitle() );
			$opt->setSelectOptionCssStyle( $status->getShowAdminCSSStyle() );
			$opt->setSelectOptionCssClass( $status->getShowAdminCSSClass() );
			
			$options[ $status::getCode() ] = $opt;
		}
		
		$source = new Form_Field_Select('status', 'Status:' );
		$source->setDefaultValue( $this->status );
		$source->setSelectOptions( $options );
		$source->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($source);
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
		
		$this->listing->addFilterWhere( $this->status_list[ $this->status ]::getStatusQueryWhere() );
	}
	
	public function isActive(): bool
	{
		return $this->status!='';
	}
}
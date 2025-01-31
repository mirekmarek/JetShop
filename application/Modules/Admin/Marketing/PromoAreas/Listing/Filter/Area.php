<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\PromoAreas;


use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Marketing_PromoAreaDefinition;


class Listing_Filter_Area extends DataListing_Filter {
	
	public const KEY = 'area';
	
	protected int $area = 0;
	
	public function getKey(): string
	{
		return static::KEY;
	}

	public function getArea(): int
	{
		return $this->area;
	}
	
	
	
	public function catchParams(): void
	{
		$this->area = Http_Request::GET()->getInt( 'area' );
		$this->listing->setParam( 'area', $this->area );
	}
	
	public function catchForm( Form $form ): void
	{
		$this->area = $form->field( 'area' )->getValue();
		$this->listing->setParam( 'area', $this->area );
	}
	
	public function generateFormFields( Form $form ): void
	{
		$field = new Form_Field_Select( 'area', 'Area:' );
		$field->setSelectOptions( [0=>Tr::_('- all -')]+Marketing_PromoAreaDefinition::getScope() );
		$field->setDefaultValue( $this->area );
		
		$form->addField( $field );
	}
	
	public function generateWhere(): void
	{
		if( $this->area ) {
			$this->listing->addFilterWhere( [
				'promo_area_id' => $this->area,
			] );
		}
	}
	
}
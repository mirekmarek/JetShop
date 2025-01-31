<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Entity\Listing;



use Closure;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Http_Request;

class Listing_Filter_Search extends Listing_Filter_Abstract {
	
	public const KEY = 'search';
	
	protected ?Closure $where_creator = null;
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	protected string $search = '';
	
	public function catchParams(): void
	{
		$this->search = Http_Request::GET()->getString( 'search' );
		$this->listing->setParam( 'search', $this->search );
	}
	
	public function catchForm( Form $form ): void
	{
		$this->search = $form->field( 'search' )->getValue();
		
		$this->listing->setParam( 'search', $this->search );
	}
	
	public function generateFormFields( Form $form ): void
	{
		$search = new Form_Field_Input( 'search', '' );
		$search->setDefaultValue( $this->search );
		$form->addField( $search );
	}
	
	public function getWhereCreator(): ?Closure
	{
		if(!$this->where_creator) {
			$this->where_creator = function( string $search ) : array {
				$search = '%'.$search.'%';
				
				return [
					'id *'            => $search,
					'OR',
					'internal_name *' => $search,
					'OR',
					'internal_code *' => $search,
				];
			};
		}
		
		return $this->where_creator;
	}
	
	public function setWhereCreator( ?Closure $where_creator ): void
	{
		$this->where_creator = $where_creator;
	}
	
	
	
	public function generateWhere(): void
	{
		if($this->search) {
			$where = $this->getWhereCreator()->call( $this, $this->search );
			
			$this->listing->addFilterWhere( $where );
		}
	}
}
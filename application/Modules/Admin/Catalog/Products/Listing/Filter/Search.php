<?php
namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\Form;
use Jet\Form_Field_Search;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Fulltext_Index_Internal_Product;

class Listing_Filter_Search extends Listing_Filter {
	
	/**
	 * @var string
	 */
	protected string $search = '';
	
	public function getKey(): string
	{
		return static::SEARCH;
	}
	
	/**
	 *
	 */
	public function catchGetParams(): void
	{
		$this->search = Http_Request::GET()->getString( 'search' );
		$this->listing->setGetParam( 'search', $this->search );
	}
	
	/**
	 * @param Form $form
	 */
	public function catchForm( Form $form ): void
	{
		$this->search = $form->field( 'search' )->getValue();
		
		$this->listing->setGetParam( 'search', $this->search );
	}
	
	/**
	 * @param Form $form
	 */
	public function generateFormFields( Form $form ): void
	{
		$search = new Form_Field_Search( 'search', '' );
		$search->setDefaultValue( $this->search );
		$search->setPlaceholder(Tr::_('Search ...'));
		$form->addField( $search );
	}
	
	/**
	 *
	 */
	public function generateWhere(): void
	{
		if($this->search) {
			$ids = Fulltext_Index_Internal_Product::search(
				search_string: $this->search,
				only_ids: true
			);
			
			if(!$ids) {
				$ids = [0];
			}
			
			
			$this->listing->addWhere([
				'id'   => $ids,
			]);
		}
	}
}
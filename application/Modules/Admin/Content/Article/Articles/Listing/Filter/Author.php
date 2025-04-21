<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Articles;

use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_Listing_Filter;
use JetApplication\Content_Article_Author;


class Listing_Filter_Author extends Admin_Listing_Filter
{
	public const KEY = 'author';
	
	protected string $author = '';
	
	public function catchParams(): void
	{
		$this->author = Http_Request::GET()->getString('author', '', array_keys(Content_Article_Author::getScope()));
		if($this->author) {
			$this->listing->setParam('author', $this->author);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + Content_Article_Author::getScope();
		
		$author = new Form_Field_Select('author', 'Author:' );
		$author->setDefaultValue( $this->author );
		$author->setSelectOptions( $options );
		$author->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($author);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->author = $form->field('author')->getValue();
		if($this->author) {
			$this->listing->setParam('author', $this->author);
		} else {
			$this->listing->unsetParam('author');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->author) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'author_id'   => $this->author,
		]);
	}
	
}
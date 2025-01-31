<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Articles;



use Jet\DataListing_Filter;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Content_Article_KindOfArticle;


class Listing_Filter_KindOfArticle extends DataListing_Filter
{
	public const KEY = 'kind_of_article';
	
	protected string $kind_of_article = '';
	
	
	public function getKey(): string
	{
		return static::KEY;
	}
	
	public function catchParams(): void
	{
		$this->kind_of_article = Http_Request::GET()->getString('kind_of_article', '', array_keys(Content_Article_KindOfArticle::getScope()));
		if($this->kind_of_article) {
			$this->listing->setParam('kind_of_article', $this->kind_of_article);
		}
	}
	
	public function generateFormFields( Form $form ): void
	{
		$options = [''=>Tr::_(' - all -')] + Content_Article_KindOfArticle::getScope();
		
		$author = new Form_Field_Select('kind_of_article', 'Kind of article:' );
		$author->setDefaultValue( $this->kind_of_article );
		$author->setSelectOptions( $options );
		$author->setErrorMessages([
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Invalid value'
		]);
		$form->addField($author);
	}
	
	public function catchForm( Form $form ): void
	{
		$this->kind_of_article = $form->field('kind_of_article')->getValue();
		if($this->kind_of_article) {
			$this->listing->setParam('kind_of_article', $this->kind_of_article);
		} else {
			$this->listing->unsetParam('kind_of_article');
		}
	}
	
	public function generateWhere(): void
	{
		if(!$this->kind_of_article) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'kind_id'   => $this->kind_of_article,
		]);
	}
	
}
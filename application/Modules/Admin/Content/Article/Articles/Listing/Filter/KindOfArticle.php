<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Articles;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Content_Article_KindOfArticle;


class Listing_Filter_KindOfArticle extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'kind_of_article';
	protected string $label = 'Kind of article';

	protected function getOptions() : array
	{
		return Content_Article_KindOfArticle::getScope();
	}
	
	
	public function generateWhere(): void
	{
		if(!$this->value) {
			return;
		}
		
		$this->listing->addFilterWhere([
			'kind_id'   => $this->value,
		]);
	}
	
}
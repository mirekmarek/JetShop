<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Articles;

use JetApplication\Admin_Listing_Filter_StdFilter;
use JetApplication\Content_Article_Author;


class Listing_Filter_Author extends Admin_Listing_Filter_StdFilter
{
	public const KEY = 'author';
	protected string $label = 'Author';

	protected function getOptions() : array
	{
		return Content_Article_Author::getScope();
	}
	
	public function generateWhere(): void
	{
		if($this->value=='') {
			return;
		}
		
		$this->listing->addFilterWhere([
			'author_id'   => $this->value,
		]);
	}
	
}
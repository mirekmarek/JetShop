<?php
namespace JetApplicationModule\Shop\MagicTags;

use Jet\Tr;
use JetApplication\Category_ShopData;
use JetApplication\Content_MagicTag_Context;

class MagicTag_CategoryLink extends MagicTag
{
	public const ID = 'category_link';
	
	
	public function generate( array $contexts ) : string
	{
		if(!$contexts['category_id']??'') {
			return '';
		}
		
		return '%CATEGORY_LINK:'.$contexts['category_id'].'%';
	}
	
	/**
	 * @return Content_MagicTag_Context[]
	 */
	public function getContexts() : array
	{
		$page_id = new Content_MagicTag_Context(
			name: 'category_id',
			type: Content_MagicTag_Context::TYPE_CATEGORY,
			description: Tr::_('Category:')
		);
		
		
		return [
			$page_id
		];
	}
	
	
	public function process( string $output ): string
	{
		$output = str_replace('http://%CATEGORY_LINK:', '%PRODUCT_URL:', $output);
		$output = str_replace('https://%CATEGORY_LINK:', '%PRODUCT_URL:', $output);
		
		if(preg_match_all('/%CATEGORY_LINK:([0-9]+)%/', $output, $matches, PREG_SET_ORDER)) {
			foreach( $matches as $m ) {
				$orig_str = $m[0];
				$id = $m[1];
				
				$link = '';
				
				$category = Category_ShopData::get( $id );
				if($category && $category->isActive()) {
					$link = '<a href="'.$category->getURL().'">'.$category->getName().'</a>';
				}
				
				$output = str_replace($orig_str, $link, $output);
			}
		}
		
		
		
		
		return $output;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Category link');
	}
	
	public function getDescription(): string
	{
		return '';
	}
}
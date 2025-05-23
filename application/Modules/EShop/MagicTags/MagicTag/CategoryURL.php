<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\MagicTags;


use Jet\Tr;
use JetApplication\Category_EShopData;
use JetApplication\Content_MagicTag_Context;


class MagicTag_CategoryURL extends MagicTag
{
	public const ID = 'category_URL';
	
	
	public function generate( array $contexts ) : string
	{
		if(!$contexts['category_id']??'') {
			return '';
		}
		
		return '%CATEGORY_URL:'.$contexts['category_id'].'%';
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
		$output = str_replace('http://%CATEGORY_URL:', '%CATEGORY_URL:', $output);
		$output = str_replace('https://%CATEGORY_URL:', '%CATEGORY_URL:', $output);
		
		if(preg_match_all('/%CATEGORY_URL:([0-9]+)%/', $output, $matches, PREG_SET_ORDER)) {
			foreach( $matches as $m ) {
				$orig_str = $m[0];
				$id = $m[1];
				
				$URL = '';
				
				$product = Category_EShopData::get( $id );
				if($product && $product->isActive()) {
					$URL = $product->getURL();
				}
				
				$output = str_replace($orig_str, $URL, $output);
			}
		}
		
		return $output;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Category URL');
	}
	
	public function getDescription(): string
	{
		return '';
	}
}
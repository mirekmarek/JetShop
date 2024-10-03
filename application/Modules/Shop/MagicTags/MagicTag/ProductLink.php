<?php
namespace JetApplicationModule\Shop\MagicTags;

use Jet\Tr;
use JetApplication\Content_MagicTag_Context;
use JetApplication\Product_ShopData;

class MagicTag_ProductLink extends MagicTag
{
	public const ID = 'product_link';
	
	
	public function generate( array $contexts ) : string
	{
		if(!$contexts['product_id']??'') {
			return '';
		}
		
		return '%PRODUCT_LINK:'.$contexts['product_id'].'%';
	}
	
	/**
	 * @return Content_MagicTag_Context[]
	 */
	public function getContexts() : array
	{
		$page_id = new Content_MagicTag_Context(
			name: 'product_id',
			type: Content_MagicTag_Context::TYPE_PRODUCT,
			description: Tr::_('Product:')
		);
		
		
		return [
			$page_id
		];
	}
	
	
	public function process( string $output ): string
	{
		$output = str_replace('http://%PRODUCT_LINK:', '%PRODUCT_URL:', $output);
		$output = str_replace('https://%PRODUCT_LINK:', '%PRODUCT_URL:', $output);
		
		if(preg_match_all('/%PRODUCT_LINK:([0-9]+)%/', $output, $matches, PREG_SET_ORDER)) {
			foreach( $matches as $m ) {
				$orig_str = $m[0];
				$id = $m[1];
				
				$link = '';
				
				$product = Product_ShopData::get( $id );
				if($product && $product->isActive()) {
					$link = '<a href="'.$product->getURL().'">'.$product->getName().'</a>';
				}
				
				$output = str_replace($orig_str, $link, $output);
			}
		}
		
		
		
		
		return $output;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Product link');
	}
	
	public function getDescription(): string
	{
		return '';
	}
}
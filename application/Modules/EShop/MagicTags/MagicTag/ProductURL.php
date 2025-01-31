<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\MagicTags;


use Jet\Tr;
use JetApplication\Content_MagicTag_Context;
use JetApplication\Product_EShopData;


class MagicTag_ProductURL extends MagicTag
{
	public const ID = 'product_URL';
	
	
	public function generate( array $contexts ) : string
	{
		if(!$contexts['product_id']??'') {
			return '';
		}
		
		return '%PRODUCT_URL:'.$contexts['product_id'].'%';
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
		$output = str_replace('http://%PRODUCT_URL:', '%PRODUCT_URL:', $output);
		$output = str_replace('https://%PRODUCT_URL:', '%PRODUCT_URL:', $output);
		
		if(preg_match_all('/%PRODUCT_URL:([0-9]+)%/', $output, $matches, PREG_SET_ORDER)) {
			foreach( $matches as $m ) {
				$orig_str = $m[0];
				$id = $m[1];
				
				$URL = '';
				
				$product = Product_EShopData::get( $id );
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
		return Tr::_('Product URL');
	}
	
	public function getDescription(): string
	{
		return '';
	}
}
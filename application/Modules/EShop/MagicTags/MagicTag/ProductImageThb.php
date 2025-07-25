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


class MagicTag_ProductImageThb extends MagicTag
{
	public const ID = 'product_image_thb';
	
	
	public function generate( array $contexts ) : string
	{
		if(!$contexts['product_id']??'') {
			return '';
		}
		
		return '%PRODUCT_IMG_URL:'.$contexts['product_id'].':'.$contexts['w'].'x'.$contexts['h'].'%';
	}
	
	/**
	 * @return Content_MagicTag_Context[]
	 */
	public function getContexts() : array
	{
		$product_id = new Content_MagicTag_Context(
			name: 'product_id',
			type: Content_MagicTag_Context::TYPE_PRODUCT,
			description: Tr::_('Product:')
		);
		
		$w = new Content_MagicTag_Context(
			name: 'w',
			type: Content_MagicTag_Context::TYPE_INT,
			description: Tr::_('Max width:')
		);
		
		$h = new Content_MagicTag_Context(
			name: 'h',
			type: Content_MagicTag_Context::TYPE_INT,
			description: Tr::_('Max height:')
		);
		
		
		return [
			$product_id,
			$w,
			$h
		];
	}
	
	
	public function process( string $output ): string
	{
		
		if(preg_match_all('/%PRODUCT_IMG_URL:([0-9]+):([0-9]+)x([0-9]+)%/', $output, $matches, PREG_SET_ORDER)) {
			foreach( $matches as $m ) {
				$orig_str = $m[0];
				$id = $m[1];
				$max_width = $m[2];
				$max_height = $m[3];
				
				$URL = '';
				
				$product = Product_EShopData::get( $id );
				if($product && $product->isActive()) {
					$URL = $product->getImageThumbnailUrl(0, $max_width, $max_height);
				}
				
				$output = str_replace($orig_str, $URL, $output);
			}
		}
		
		return $output;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Product image thumbnail URL');
	}
	
	public function getDescription(): string
	{
		return '';
	}
}
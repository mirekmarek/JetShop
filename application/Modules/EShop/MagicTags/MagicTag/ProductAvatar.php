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

class MagicTag_ProductAvatar extends MagicTag
{
	public const ID = 'product_avatar';
	
	
	public function generate( array $contexts ) : string
	{
		if(!$contexts['product_id']??'') {
			return '';
		}
		
		return '%PRODUCT:'.$contexts['product_id'].'%';
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
		
		if(preg_match_all('/%PRODUCT:([0-9]+)%/', $output, $matches, PREG_SET_ORDER)) {
			foreach( $matches as $m ) {
				$orig_str = $m[0];
				$id = $m[1];
				
				$snippet = '';
				
				$product = Product_EShopData::get( $id );
				if($product && $product->isActive()) {
					$this->view->setVar('product', $product);
					
					$snippet = $this->view->render('product');
				}
				
				$output = str_replace($orig_str, $snippet, $output);
			}
		}
		
		
		
		
		return $output;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Product avatar');
	}
	
	public function getDescription(): string
	{
		return '';
	}
}
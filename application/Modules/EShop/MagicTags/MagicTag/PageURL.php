<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\MagicTags;


use Jet\MVC;
use Jet\Tr;
use JetApplication\Content_MagicTag_Context;

class MagicTag_PageURL extends MagicTag
{
	public const ID = 'page_URL';
	
	public function generate( array $contexts ) : string
	{
		if(!$contexts['page_id']??'') {
			return '';
		}
		
		return '%PAGE_URL:'.$contexts['page_id'].'%';
	}
	
	/**
	 * @return Content_MagicTag_Context[]
	 */
	public function getContexts() : array
	{
		$page_id = new Content_MagicTag_Context(
			name: 'page_id',
			type: Content_MagicTag_Context::TYPE_PAGE,
			description: Tr::_('Page:')
		);
		
		
		return [
			$page_id
		];
	}
	
	public function process( string $output ): string
	{
		$output = str_replace('http://%PAGE_URL:', '%URL:', $output);
		$output = str_replace('https://%PAGE_URL:', '%URL:', $output);
		
		if(preg_match_all('/%PAGE_URL:([a-z\-0-9A-Z]+)%/', $output, $matches, PREG_SET_ORDER)) {
			foreach( $matches as $m ) {
				$orig_str = $m[0];
				$page_id = $m[1];
				
				$page = MVC::getPage( $page_id );
				$URL = $page?->getURL()??'';
				
				$output = str_replace($orig_str, $URL, $output);
			}
		}
		
		if(preg_match_all('/%PAGE_URL:([a-z\-0-9A-Z\_]+)\/([a-z\-0-9A-Z]+)%/', $output, $matches, PREG_SET_ORDER)) {
			foreach( $matches as $m ) {
				$orig_str = $m[0];
				$eshop_key = $m[1];
				$page_id = $m[2];
				
				$eshop = EShops::get( $eshop_key );
				
				$URL = '';
				
				if($eshop) {
					$page = MVC::getPage( $page_id, locale: $eshop->getLocale(), base_id: $eshop->getBaseId() );
					$URL = $page?->getURL()??'';
				}
				
				$output = str_replace($orig_str, $URL, $output);
			}
		}
		
		
		return $output;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Page URL');
	}
	
	public function getDescription(): string
	{
		return '';
	}
}
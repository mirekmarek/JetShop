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

class MagicTag_PageLink extends MagicTag
{
	public const ID = 'page_link';
	
	public function generate( array $contexts ) : string
	{
		if(!$contexts['page_id']??'') {
			return '';
		}
		
		return '%PAGE_LINK:'.$contexts['page_id'].'%';
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
		$output = str_replace('http://%PAGE_LINK:', '%URL:', $output);
		$output = str_replace('https://%PAGE_LINK:', '%URL:', $output);
		
		if(!preg_match_all('/%PAGE_LINK:([a-z\-0-9A-Z]+)%/', $output, $matches, PREG_SET_ORDER)) {
			return $output;
		}
		
		foreach( $matches as $m ) {
			$orig_str = $m[0];
			$page_id = $m[1];
			
			$page = MVC::getPage( $page_id );
			
			$link = '';
			if(
				$page &&
				$page->getIsActive()
			) {
				$link = '<a href="'.$page->getUrl().'">'.$page->getTitle().'</a>';
			}
			
			$output = str_replace($orig_str, $link, $output);
		}
		
		return $output;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Page link');
	}
	
	public function getDescription(): string
	{
		return '';
	}
}
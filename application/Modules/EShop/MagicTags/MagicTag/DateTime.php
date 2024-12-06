<?php
namespace JetApplicationModule\EShop\MagicTags;

use Jet\Data_DateTime;
use Jet\Locale;
use Jet\Tr;
use JetApplication\Content_MagicTag_Context;


class MagicTag_DateTime extends MagicTag
{
	public const ID = 'date_time';
	
	
	public function generate( array $contexts ) : string
	{
		if(!$contexts['what']??'') {
			return '';
		}
		
		return '%DATE_TIME:'.$contexts['what'].'%';
	}
	
	/**
	 * @return Content_MagicTag_Context[]
	 */
	public function getContexts() : array
	{
		$what = new Content_MagicTag_Context(
			name: 'what',
			type: Content_MagicTag_Context::TYPE_STRING,
			description: Tr::_('What to display:')
		);
		
		$what->setOptions([
			'this_year' => Tr::_('This year'),
			'next_year' => Tr::_('Next year'),
			'today' => Tr::_('Today - date'),
			'tomorow' => Tr::_('Tomorrow - date'),
		]);
		
		
		return [
			$what
		];
	}
	
	
	public function process( string $output ): string
	{
		
		if(preg_match_all('/%DATE_TIME:(this_year|next_year|today|tomorow)%/', $output, $matches, PREG_SET_ORDER)) {
			foreach( $matches as $m ) {
				$orig_str = $m[0];
				$what = $m[1];
				
				$date_time = '';
				
				switch($what) {
					case 'this_year':
						$date_time = date('Y');
						break;
					case 'next_year':
						$date_time = ((int)date('Y'))+1;
						break;
					case 'today':
						$date_time = Locale::date( Data_DateTime::now() );
						break;
					case 'tomorow':
						$date_time = Locale::date( new Data_DateTime('+1 day') );
						break;
				}
				
				$output = str_replace($orig_str, $date_time, $output);
			}
		}
		
		return $output;
	}
	
	public function getTitle(): string
	{
		return Tr::_('Date and time');
	}
	
	public function getDescription(): string
	{
		return '';
	}
}
<?php
namespace GTClient;


require_once 'Translator/RESTClient.php';
require_once 'Translator/Exception.php';

/**
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

class Translator {
	protected static int $text_length_limit = 1800;
	
	protected static string $gooogle_API_base_URL = 'https://www.googleapis.com/language/translate/v2/';
	
	protected static array $sentence_separators = [
		'. ',
		'! ',
		'? '
	];
	
	protected Translator_RESTClient $client;
	
	protected string $api_key = '';
	
	protected array $html_tags_to_be_translated = ['p', 'td', 'th', 'h1', 'h2', 'h3', 'h4', 'h5', 'li', 'td', 'th'];
	
	protected array $html_tags_to_be_ignored = ['pre', 'code'];
	
	protected array $languages = [];

	public static function getTextLengthLimit(): int
	{
		return self::$text_length_limit;
	}

	public static function setTextLengthLimit( int $text_length_limit ): void
	{
		self::$text_length_limit = $text_length_limit;
	}
	
	public static function getGooogleAPIBaseURL(): string
	{
		return self::$gooogle_API_base_URL;
	}
	
	public static function setGooogleAPIBaseURL( string $gooogle_API_base_URL ): void
	{
		self::$gooogle_API_base_URL = $gooogle_API_base_URL;
	}
	
	public static function getSentenceSeparators(): array
	{
		return self::$sentence_separators;
	}
	
	public static function setSentenceSeparators( array $sentence_separators ): void
	{
		self::$sentence_separators = $sentence_separators;
	}
	
	
	public function __construct( string $api_key )
	{
		$this->api_key = $api_key;
		$this->client = new Translator_RESTClient(
			static::$gooogle_API_base_URL,
			$this->api_key
		);
		
		if(!$this->client->get('languages')) {
			throw new Translator_Exception('Unable to get language list');
		}
		
		$response = $this->client->responseData();
		
		if(
			!isset($response['data']) ||
			!isset($response['data']['languages']) ||
			!is_array($response['data']['languages'])
		) {
			throw new Translator_Exception('Error [2]');
		}
		
		foreach($response['data']['languages'] as $l) {
			$this->languages[] = $l['language'];
		}
	}
	
	
	
	public function translateHtml(
		string $source_language,
		string $target_language,
		string $source_html
	) : string
	{
		if( !in_array( $source_language, $this->languages ) ) {
			throw new Translator_Exception( 'Unknown source language' );
		}
		
		if( !in_array( $target_language, $this->languages ) ) {
			throw new Translator_Exception( 'Unknown target language' );
		}
		
		
		
		$texts = $this->extractTextFromHTML( $source_html );
		
		foreach($texts as $i=>$t) {
			$texts[$i]['translation'] = $this->translateText( $source_language, $target_language, $t['original'] );
		}
		
		uasort($texts, function(array $a, array $b) : int {
			$a = strlen($a['original']);
			$b = strlen($b['original']);
		
			return $a <=> $b;
		});
		
		$html = $source_html;
		
		foreach($texts as $t) {
			$orig = $t['original'];
			$translation = $t['translation'];
			
			$html = str_replace($orig, $translation, $html);
		}
		
		return $html;
	}
	
	public function extractTextFromHTML( string $html ) : array
	{
		
		foreach( $this->html_tags_to_be_ignored as $tag ) {
			$html = preg_replace('/<' . $tag . '>(.*?)<\/' . $tag . '>/is', '', $html);
		}
		
		$texts = [];
		
		foreach( $this->html_tags_to_be_translated as $tag ) {
			if( preg_match_all( '/<' . $tag . '>(.*?)<\/' . $tag . '>/is', $html, $matches, PREG_SET_ORDER ) ) {
				
				foreach( $matches as $match ) {
					
					$text = $this->splitLongText( $match[1] );
					
					foreach($text as $t) {
						$t = trim($t);
						if(!$t) {
							continue;
						}
						
						$texts[] = [
							'original'    => $t,
							'translation' => ''
						];
					}
				}
			}
			
		}
		
		return $texts;
	}
	
	public function splitLongText( string $text ) : array
	{
		$separators = static::getSentenceSeparators();
		
		$texts = [$text];
		foreach( $separators as $separator ) {
			$_texts = [];
			
			foreach($texts as $_text) {
				if(!str_contains($_text, $separator)) {
					$_texts[] = $_text;
					continue;
				}
				
				$_text = explode($separator, $_text);
				
				foreach($_text as $i=>$_t) {
					$i++;
					
					if(strlen($_t)==0) {
						continue;
					}
					
					if($i==count($_text)) {
						$_texts[] = $_t;
					} else {
						$_texts[] = $_t.$separator;
					}
				}
			}
			
			$texts = $_texts;
		}
		
		return $texts;
	}
	
	
	public function translateText(
			string $source_language,
			string $target_language,
			string $text
	) : string
	{
		if(!in_array($source_language, $this->languages)) {
			throw new Translator_Exception('Unknown source language');
		}
		
		if(!in_array($target_language, $this->languages)) {
			throw new Translator_Exception('Unknown target language');
		}
		
		if(strlen($text)>static::$text_length_limit) {
			var_dump($text);
			throw new Translator_Exception('Text is too long. Max length: '.static::$text_length_limit );
		}
		
		
		$params = [
			'source' => $source_language,
			'target' => $target_language,
			'q' => $text
		];
		
		if(!$this->client->get('', get_params: $params)) {
			throw new Translator_Exception('Error [1]');
		}
		
		$response = $this->client->responseData();
		
		if(
			!isset($response['data']) ||
			!isset($response['data']['translations']) ||
			!is_array($response['data']['translations'])
		) {
			throw new Translator_Exception('Error [2]');
		}
		
		return html_entity_decode($response['data']['translations'][0]['translatedText']);
	}
}
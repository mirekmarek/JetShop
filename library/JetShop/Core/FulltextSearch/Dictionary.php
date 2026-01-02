<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Data_Text;
use Jet\Locale;
use JsonSerializable;

#[DataModel_Definition(
	name: 'fulltext_dictionary',
	database_table_name: 'fulltext_dictionary',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_FulltextSearch_Dictionary extends DataModel implements JsonSerializable {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true
	)]
	protected ?Locale $locale = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $note = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 99999999,
	)]
	protected string $words = '';
	
	/**
	 * @var static[][]
	 */
	protected static array $dictionary = [];
	
	protected static array $word_variants = [];
	
	
	/**
	 * @return static[]
	 */
	public static function getDictionary( Locale $locale ) : iterable
	{
		$locale_str = $locale->toString();
		
		if(!array_key_exists($locale_str, static::$dictionary)) {
			static::$dictionary[$locale_str] = [];
			
			foreach(static::fetch([''=>['locale'=>$locale]]) as $dict) {
				static::$dictionary[$locale_str][] = $dict;
			}
		}
		
		return static::$dictionary[$locale_str];
		
	}
	
	public static function findWordVariants( Locale $locale, string $word ) : array
	{
		$locale_str = $locale->toString();
		
		if(!array_key_exists($locale_str, static::$word_variants)) {
			static::$word_variants[$locale_str] = [];
			
			foreach( static::getDictionary($locale) as $rec ) {
				static::$word_variants[$locale_str][] = explode(' ', trim($rec->getWords()));
			}
		}
		
		foreach( static::$word_variants[$locale_str] as $variants ) {
			if(in_array($word, $variants)) {
				return $variants;
			}
		}
		
		return [$word];
	}
	
	public static function prepareText( string $text ) : string
	{
		$text = Data_Text::removeAccents( $text );
		$text = strtolower($text);
		$text = preg_replace( '/([0-9]+)x([0-9]+)/', '$1 $2', $text);
		$text = preg_replace("/[^a-z0-9 ]/", ' ', $text);
		$text = preg_replace('/ {2,}/', ' ', $text);
		
		return $text;
	}
	
	public static function collectWords( ?Locale $locale, array $texts ) : array
	{
		$words = [];
		
		foreach( $texts as $text ) {
			$text = static::prepareText( $text );
			
			$text = explode(' ', $text);
			foreach( $text as $word ) {
				if(!$word) {
					continue;
				}
				
				if($locale) {
					$variants = static::findWordVariants( $locale, $word );
					
					foreach( $variants as $variant ) {
						if(!in_array($variant, $words)) {
							$words[] = $variant;
						}
					}
				} else {
					if(!in_array($word, $words)) {
						$words[] = $word;
					}
				}
			}
		}
		
		return $words;
	}
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function setId( int $id ) : void
	{
		$this->id = $id;
	}
	
	public function getLocale(): ?Locale
	{
		return $this->locale;
	}
	
	public function setLocale( ?Locale $locale ): void
	{
		$this->locale = $locale;
	}
	
	
	
	public function getNote() : string
	{
		return $this->note;
	}
	
	public function setNote( string $note ) : void
	{
		$this->note = $note;
	}
	
	public function getWords() : string
	{
		return $this->words;
	}
	
	public function setWords( string $words ) : void
	{
		$words = static::prepareText( $words );
		
		$words = explode(' ', $words);
		$words = array_unique( $words );
		$words = implode(' ', $words);
		$words = ' '.$words.' ';
		
		
		$this->words = $words;
	}
	
	public function jsonSerialize () : array
	{
		return [
			'id'    => $this->id,
			'note'  => $this->note,
			'words' => $this->words,
		];
	}
	
	
}
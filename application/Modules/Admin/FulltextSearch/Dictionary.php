<?php
namespace JetApplicationModule\Admin\FulltextSearch;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Data_Text;
use JsonSerializable;

#[DataModel_Definition(
	name: 'dictionary',
	database_table_name: 'fulltext_internal_dictionary',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
class Dictionary extends DataModel implements JsonSerializable {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $note = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $words = '';

	/**
	 * @var static[]
	 */
	protected static array $loaded_items = [];

	/**
	 * @var static[]
	 */
	protected static ?array $dictionary = null;

	protected static ?array $word_variants = null;
	

	/**
	 * @return static[]
	 */
	public static function getDictionary() : iterable
	{

		if(static::$dictionary===null) {
			static::$dictionary = [];
			
			foreach(static::fetchInstances() as $dict) {
				static::$dictionary[] = $dict;
			}
		}

		return static::$dictionary;

	}

	public static function findWordVariants( string $word ) : array
	{
		if(static::$word_variants===null) {
			static::$word_variants = [];

			foreach( static::getDictionary() as $rec ) {
				static::$word_variants[] = explode(' ', trim($rec->getWords()));
			}
		}

		foreach( static::$word_variants as $variants ) {
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

	public static function collectWords( array $texts ) : array
	{
		$words = [];

		foreach( $texts as $text ) {
			$text = static::prepareText( $text );

			$text = explode(' ', $text);
			foreach( $text as $word ) {
				if(!$word) {
					continue;
				}

				$variants = Dictionary::findWordVariants( $word );

				foreach( $variants as $variant ) {
					if(!in_array($variant, $words)) {
						$words[] = $variant;
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
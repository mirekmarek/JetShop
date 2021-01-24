<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Data_Text;
use JsonSerializable;

#[DataModel_Definition(
	name: 'fulltext_dictionary',
	database_table_name: 'fulltext_dictionary',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Fulltext_Dictionary extends DataModel implements JsonSerializable {

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $shop_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $note = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $words = '';

	/**
	 * @var Fulltext_Dictionary[]
	 */
	protected static array $loaded_items = [];

	/**
	 * @var Fulltext_Dictionary[][]
	 */
	protected static array $loaded_dictionaries = [];

	protected static array $word_variants = [];


	public static function get( int $id ) : Fulltext_Dictionary|null
	{
		if(isset(static::$loaded_items[$id])) {
			return static::$loaded_items[$id];
		}

		static::$loaded_items[$id] = Fulltext_Dictionary::load( $id );

		return static::$loaded_items[$id];
	}

	/**
	 * @param string $shop_id
	 *
	 * @return Fulltext_Dictionary[]
	 */
	public static function getDictionary( string $shop_id ) : iterable
	{
		if(isset( Fulltext_Dictionary::$loaded_dictionaries[$shop_id])) {
			return Fulltext_Dictionary::$loaded_dictionaries[$shop_id];
		}

		Fulltext_Dictionary::$loaded_dictionaries[$shop_id] = Fulltext_Dictionary::fetchInstances( [
			'shop_id' => $shop_id
		] );

		return Fulltext_Dictionary::$loaded_dictionaries[$shop_id];

	}

	public static function findWordVariants( string $shop_id, string $word ) : array
	{
		if(!isset(Fulltext_Dictionary::$word_variants[$shop_id])) {
			Fulltext_Dictionary::$word_variants[$shop_id] = [];

			foreach( Fulltext_Dictionary::getDictionary( $shop_id ) as $rec ) {
				static::$word_variants[$shop_id][] = explode(' ', trim($rec->getWords()));
			}
		}

		foreach( Fulltext_Dictionary::$word_variants[$shop_id] as $variants ) {
			if(in_array($word, $variants)) {
				return $variants;
			}
		}

		return [$word];
	}

	public static function collectWords( string $shop_id, array $texts ) : array
	{
		$words = [];

		foreach( $texts as $text ) {
			$text = Data_Text::removeAccents( $text );
			$text = strtolower($text);
			$text = preg_replace( '/([0-9]+)x([0-9]+)/', '$1 $2', $text);
			$text = preg_replace("/[^a-z0-9 ]/", ' ', $text);
			$text = preg_replace('/[ ]{2,}/', ' ', $text);

			$text = explode(' ', $text);
			foreach( $text as $word ) {
				if(!$word) {
					continue;
				}

				$variants = Fulltext_Dictionary::findWordVariants( $shop_id, $word );

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
		$this->id = (int)$id;
	}

	public function getShopId() : string
	{
		return $this->shop_id;
	}

	public function setShopId( string $shop_id ) : void
	{
		$this->shop_id = $shop_id;
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
		$words = Data_Text::removeAccents( trim($words) );
		$words = strtolower($words);
		$words = preg_replace( '/([0-9]+)x([0-9]+)/', '$1 $2', $words);
		$words = preg_replace("/[^a-z0-9 ]/", ' ', $words);
		$words = preg_replace('/[ ]{2,}/', ' ', $words);
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
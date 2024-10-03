<?php
namespace JetApplicationModule\Shop\FulltextSearch;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Locale;
use JetApplication\Shops;
use JetApplication\Shops_Shop;


#[DataModel_Definition(
	name: 'word',
	database_table_name: 'fulltext_shop_word',
	id_controller_class: DataModel_IDController_Passive::class,
)]
class Index_Word extends DataModel
{
	protected ?Shops_Shop $_shop = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		is_id: true,
	)]
	protected string $shop_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
		is_id: true,
	)]
	protected ?Locale $locale = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_id: true,
		is_key: true,
		max_len: 50
	)]
	protected string $entity_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $object_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
		is_id: true
	)]
	protected string $word = '';
	
	
	public function setShop( Shops_Shop $shop ) : void
	{
		$this->shop_code = $shop->getShopCode();
		$this->locale = $shop->getLocale();
		$this->_shop = $shop;
	}
	
	public function getShopCode() : string
	{
		return $this->shop_code;
	}
	
	public function getLocale(): ?Locale
	{
		return $this->locale;
	}
	
	public function getShop() : Shops_Shop
	{
		if(!$this->_shop) {
			$this->_shop = Shops::get( $this->getShopKey() );
		}
		
		return $this->_shop;
	}
	
	public function getShopKey() : string
	{
		return $this->shop_code.'_'.$this->locale;
	}
	
	

	public function getObjectId() : int
	{
		return $this->object_id;
	}

	public function setObjectId( int $object_id ) : void
	{
		$this->object_id = $object_id;
	}
	
	public function getEntityType(): string
	{
		return $this->entity_type;
	}
	
	public function setEntityType( string $entity_type ): void
	{
		$this->entity_type = $entity_type;
	}

	public function getWord() : string
	{
		return $this->word;
	}

	public function setWord( string $word ) : void
	{
		$this->word = $word;
	}
}

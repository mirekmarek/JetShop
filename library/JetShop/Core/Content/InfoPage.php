<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;

use JetApplication\Content_InfoPage_ShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;



#[DataModel_Definition(
	name: 'content_info_page',
	database_table_name: 'content_info_page',
)]
abstract class Core_Content_InfoPage extends Entity_WithShopData
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Page id:',
		is_required: true,
		validation_regexp: '/^[a-zA-Z0-9\-]{2,}$/i',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY          => 'Please enter valid page id',
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter valid page id',
			'page_id_is_not_unique'               => 'Page with the identifier already exists',
		]
	)]
	protected string $page_id = '';
	
	/**
	 * @var Content_InfoPage_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Content_InfoPage_ShopData::class
	)]
	protected array $shop_data = [];
	
	
	
	public function afterAdd(): void
	{
		foreach(Shops::getList() as $shop ) {
			$this->getShopData( $shop )->publish();
		}
		
		parent::afterAdd();
	}
	
	public function afterUpdate(): void
	{
		foreach(Shops::getList() as $shop ) {
			$this->getShopData( $shop )->publish();
		}
		
		parent::afterUpdate();
	}
	
	
	
	
	public function setPageId( string $value ): void
	{
		$this->page_id = $value;
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->setPageId( $value );
		}
	}
	
	public function getPageId(): string
	{
		return $this->page_id;
	}
	
	public function getShopData( ?Shops_Shop $shop = null ): Content_InfoPage_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
	}
	
	public function publish() : void
	{
		foreach(Shops::getList() as $shop) {
			$this->getShopData( $shop )->publish();
		}
	}
	
	public function activate(): void
	{
		parent::activate();
		$this->publish();
	}
	
	public function deactivate(): void
	{
		parent::deactivate();
		$this->publish();
	}
	
	public function activateCompletely(): void
	{
		parent::activateCompletely();
		$this->publish();
	}
	
	public function activateShopData( Shops_Shop $shop ): void
	{
		parent::activateShopData( $shop );
		$this->publish();
	}
	
	public function deactivateShopData( Shops_Shop $shop ): void
	{
		parent::deactivateShopData( $shop );
		$this->publish();
	}
	
}
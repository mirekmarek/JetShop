<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;

use JetApplication\Category_Menu_Item;
use JetApplication\CommonEntity_ShopRelationTrait_ShopIsId;

/**
 *
 */
#[DataModel_Definition(
	name: 'category_menu_item',
	database_table_name: 'categories_menu_items',
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Category_Menu_Item extends DataModel
{

	use CommonEntity_ShopRelationTrait_ShopIsId;


	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $category_id = 0;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $parent_category_id = 0;

	/**
	 * @var int
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $priority = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $label = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $URL = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $icon_URL = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $visible_products_count = 0;

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $nested_visible_products_count = 0;

	protected ?Category_Menu_Item $_parent = null;

	/**
	 * @var Category_Menu_Item[]
	 */
	protected array $_children = [];

	/**
	 * @return int
	 */
	public function getParentCategoryId(): int
	{
		return $this->parent_category_id;
	}

	/**
	 * @param int $parent_category_id
	 */
	public function setParentCategoryId( int $parent_category_id ): void
	{
		$this->parent_category_id = $parent_category_id;
	}

	/**
	 * @return int
	 */
	public function getPriority(): int
	{
		return $this->priority;
	}

	/**
	 * @param int $priority
	 */
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
	}


	/**
	 * @param string $value
	 */
	public function setLabel( string $value ) : void
	{
		$this->label = $value;
	}

	/**
	 * @return string
	 */
	public function getLabel() : string
	{
		return $this->label;
	}

	/**
	 * @param string $value
	 */
	public function setUrl( string $value ) : void
	{
		$this->URL = $value;
	}

	/**
	 * @return string
	 */
	public function getUrl() : string
	{
		return $this->URL;
	}

	/**
	 * @param string $value
	 */
	public function setIconUrl( string $value ) : void
	{
		$this->icon_URL = $value;
	}

	/**
	 * @return string
	 */
	public function getIconUrl() : string
	{
		return $this->icon_URL;
	}
	

	/**
	 * @param int $value
	 */
	public function setCategoryId( int $value ) : void
	{
		$this->category_id = $value;
	}

	/**
	 * @return int
	 */
	public function getCategoryId() : int
	{
		return $this->category_id;
	}

	/**
	 * @param int $value
	 */
	public function setVisibleProductsCount( int $value ) : void
	{
		$this->visible_products_count = $value;
	}

	/**
	 * @return int
	 */
	public function getVisibleProductsCount() : int
	{
		return $this->visible_products_count;
	}

	/**
	 * @param int $value
	 */
	public function setNestedVisibleProductsCount( int $value ) : void
	{
		$this->nested_visible_products_count = $value;
	}

	/**
	 * @return int
	 */
	public function getNestedVisibleProductsCount() : int
	{
		return $this->nested_visible_products_count;
	}

	public static function actualize( Category_Menu_Item $current, Category_Menu_Item $new ) : bool
	{
		$new_data = get_object_vars($new);

		$actualized = false;
		foreach( $new_data as $k=> $v) {
			if($k[0]=='_') {
				continue;
			}

			if( $current->{$k}!=$v) {
				$current->{$k} = $v;

				$actualized = true;
			}
		}

		return $actualized;
	}

	public function addChildren( Category_Menu_Item $ch ) : void
	{
		$this->_children[$ch->getCategoryId()] = $ch;
		/**
		 * @var Category_Menu_Item $this
		 */
		$ch->_parent = $this;
	}

	/**
	 * @return Category_Menu_Item[]
	 */
	public function getChildren() : array
	{
		return $this->_children;
	}

	public function getParent() : ?Category_Menu_Item
	{
		return $this->_parent;
	}
}

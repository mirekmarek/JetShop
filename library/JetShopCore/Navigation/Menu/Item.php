<?php
namespace JetShop;


use Jet\BaseObject;

abstract class Core_Navigation_Menu_Item extends BaseObject
{
	protected ?Navigation_Menu $__menu = null;

	protected int $parent_id = 0;

	protected int $id = 0;

	protected string $label = '';

	protected int $items_count = 0;

	protected string $URL = '';

	protected string $icon_URL = '';

	protected array $children_ids = [];

	public function __construct( Navigation_Menu $menu )
	{
		$this->__menu = $menu;
	}

	public function getMenu() : Navigation_Menu
	{
		return $this->__menu;
	}

	public function setMenu( Navigation_Menu $menu ) : void
	{
		$this->__menu = $menu;
	}

	public function getParentId() : int
	{
		return $this->parent_id;
	}

	public function setParentId( int $parent_id ) : void
	{
		$this->parent_id = $parent_id;
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function getLabel() : string
	{
		return $this->label;
	}

	public function setLabel( string $label ) : void
	{
		$this->label = $label;
	}

	public function getItemsCount() : int
	{
		return $this->items_count;
	}

	public function setItemsCount( int $items_count ) : void
	{
		$this->items_count = $items_count;
	}

	public function getURL() : string
	{
		return $this->URL;
	}

	public function setURL( string $URL ) : void
	{
		$this->URL = $URL;
	}

	public function getIconURL() : string
	{
		return $this->icon_URL;
	}

	public function setIconURL( string $icon_URL ) : void
	{
		$this->icon_URL = $icon_URL;
	}

	public function appendChild( Navigation_Menu_Item $child  ) : void
	{
		$child->setParentId( $this->id );
		$this->children_ids[] = $child->getId();
	}

	/**
	 * @return Navigation_Menu_Item[]
	 */
	public function getChildren() : array
	{
		$items = [];

		foreach( $this->__menu->getItems() as $item ) {
			if($item->getParentId()==$this->id) {
				$items[$item->getId()] = $item;
			}
		}

		return $items;
	}

}
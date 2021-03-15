<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShopModule\Exports\Heureka;

use Jet\BaseObject;

class HeurekaCategory extends BaseObject {

	protected string $id = '';

	protected string $parent_id = '';

	protected array $path = [];

	protected string $name = '';

	protected string $full_name = '';

	public function __construct( \SimpleXMLElement $xnl_node, string $parent_id, array $path )
	{
		$id = trim((string)$xnl_node->CATEGORY_ID);
		$this->setId( $id );
		$this->setName( (string)$xnl_node->CATEGORY_NAME );
		$this->setFullName( (string)$xnl_node->CATEGORY_FULLNAME );
		$this->setParentId( $parent_id );
		$this->setPath( $path );
	}

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId( string $id ): void
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getParentId(): string
	{
		return $this->parent_id;
	}

	/**
	 * @param string $parent_id
	 */
	public function setParentId( string $parent_id ): void
	{
		$this->parent_id = $parent_id;
	}

	/**
	 * @return array
	 */
	public function getPath(): array
	{
		return $this->path;
	}

	/**
	 * @param array $path
	 */
	public function setPath( array $path ): void
	{
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName( string $name ): void
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getFullName(): string
	{
		return $this->full_name;
	}

	/**
	 * @param string $full_name
	 */
	public function setFullName( string $full_name ): void
	{
		$this->full_name = $full_name;
	}


}
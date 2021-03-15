<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShopModule\Exports\GoogleShopping;

use Jet\BaseObject;

class GoogleCategory extends BaseObject {

	protected string $id = '';

	protected string $parent_id = '';

	protected array $path = [];

	protected string $name = '';

	protected string $full_name = '';

	public function __construct()
	{
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

	public function setParent( GoogleCategory $parent ) : void
	{
		$this->setParentId( $parent->getId() );

		if($parent->getPath()) {
			$path = $parent->getPath();
		} else {
			$path = [];
		}

		$path[] = $parent->getId();

		$this->setPath( $path );

	}


}
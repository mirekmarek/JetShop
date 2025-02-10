<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Definition;

abstract class Core_EShopEntity_CanNotBeDeletedReason
{
	protected string $entity_class;
	protected string $reason;
	protected array $entity_ids;
	
	public function __construct( string $entity_class, string $reason, array $entity_ids ) {
		$this->entity_class = $entity_class;
		$this->reason = $reason;
		$this->entity_ids = $entity_ids;
	}
	
	public function getEntityClass(): string
	{
		return $this->entity_class;
	}
	
	public function getEntityDefinitio() : EShopEntity_Definition
	{
		return EShopEntity_Definition::get( $this->entity_class );
	}
	
	public function getReason(): string
	{
		return $this->reason;
	}
	
	public function getEntityIds(): array
	{
		return $this->entity_ids;
	}
	
	
}
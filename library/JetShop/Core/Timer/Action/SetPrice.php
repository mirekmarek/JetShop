<?php

namespace JetShop;

use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Entity_WithEShopData;
use JetApplication\Pricelist;
use JetApplication\EShop;
use JetApplication\Timer_Action;

abstract class Core_Timer_Action_SetPrice extends Timer_Action {
	protected Pricelist $pricelist;
	protected float $current_price;
	
	public function __construct( EShop $eshop, Pricelist $pricelist, float $current_price ) {
		$this->setEshop( $eshop );
		$this->pricelist = $pricelist;
		$this->current_price = $current_price;
	}
	
	public function getKey(): string
	{
		return 'set_price:'.$this->eshop->getKey().':'.$this->pricelist->getCode();
	}
	
	public function getTitle(): string
	{
		return Tr::_('Set price %pricelist%', ['pricelist'=>$this->pricelist->getName()]);
	}
	
	public function updateForm( Form $form ): void
	{
		$price = new Form_Field_Float('price', 'Price:');
		$price->setDefaultValue( $this->current_price );
		
		$form->addField( $price );
	}
	
	public function catchActionContextValue( Form $form ) : mixed
	{
		return $form->field('price')->getValue();
	}
	
	public function formatActionContextValue( mixed $action_context ) : string
	{
		return Admin_Managers::PriceFormatter()->formatWithCurrency(
			$this->pricelist, (float)$action_context
		);
	}
	
	abstract public function perform( Entity_WithEShopData $entity, mixed $action_context ): bool;
	
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasPrice_Interface;
use JetApplication\Pricelist;
use JetApplication\Timer_Action;

abstract class Core_Timer_Action_SetPrice extends Timer_Action {
	protected Pricelist $pricelist;
	protected float $current_price;
	
	public function __construct( Pricelist $pricelist, float $current_price ) {
		$this->pricelist = $pricelist;
		$this->current_price = $current_price;
	}
	
	public function getAction() : string
	{
		return 'set_price:'.$this->pricelist->getCode();
	}
	
	public function getTitle(): string
	{
		return Tr::_('Set price %PRICELIST%', ['PRICELIST'=>$this->pricelist->getName()]);
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
		return Application_Service_Admin::PriceFormatter()->formatWithCurrency(
			$this->pricelist, (float)$action_context
		);
	}
	
	abstract public function perform( EShopEntity_Basic|EShopEntity_HasPrice_Interface $entity, mixed $action_context ): bool;
	
}
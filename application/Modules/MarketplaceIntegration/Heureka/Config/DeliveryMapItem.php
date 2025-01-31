<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\MarketplaceIntegration\Heureka;



use Jet\Config;
use Jet\Config_Definition;
use Jet\Config_Section;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use Jet\Tr;
use JetApplication\Delivery_Method;

class Config_DeliveryMapItem extends Config_Section implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Delivery method: ',
		select_options_creator: [
			Delivery_Method::class,
			'getScope'
		],
	)]
	protected int $delivery_method_id = 0;
	
	#[Config_Definition(
		type: Config::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Type: ',
		select_options_creator: [
			self::class,
			'getDeliveryTypesScope'
		],
	)]
	protected int $type = 0;
	
	#[Config_Definition(
		type: Config::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Store type: ',
		select_options_creator: [
			self::class,
			'getStoreTypesScope'
		],
	)]
	protected int $store_type = 0;
	
	#[Config_Definition(
		type: Config::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Store id: '
	)]
	protected int $store_id = 0;
	
	/** @noinspection SpellCheckingInspection */
	public static function getDeliveryTypesScope() : array
	{
		return [
			1 => Tr::_('Osobní odběr'),
			2 => Tr::_('Česká pošta'),
			3 => Tr::_('Spediční služba (PPL, DPD, ...)'),
			4 => Tr::_('Expresní dodání'),
			5 => Tr::_('Speciální doprava'),
			6 => Tr::_('Česká Pošta - Balík Na poštu'),
			9 => Tr::_('Dopravci poskytovaní skrze DepotAPI'),
		];
	}
	
	
	/** @noinspection SpellCheckingInspection */
	public static function getStoreTypesScope() : array
	{
		return [
			0 => '',
			1 => Tr::_('Interní pobočka / výdejní místo obchodu'),
			3 => Tr::_('Výdejní místo dopravce z DepotAPI'),
		];
	}
	
	
	public function __construct( array $data=[] )
	{
		if( $data ) {
			$this->delivery_method_id = (int)$data['delivery_method_id']??0;
			$this->type = (int)$data['type']??0;
			$this->store_type = (int)$data['store_type']??0;
			$this->store_id = (int)$data['store_id']??0;
		}
	}
	
	
	public function getDeliveryMethodId(): int
	{
		return $this->delivery_method_id;
	}
	
	public function setDeliveryMethodId( int $delivery_method_id ): void
	{
		$this->delivery_method_id = $delivery_method_id;
	}
	
	public function getType(): int
	{
		return $this->type;
	}
	
	public function setType( int $type ): void
	{
		$this->type = $type;
	}
	
	public function getStoreType(): int
	{
		return $this->store_type;
	}
	
	public function setStoreType( int $store_type ): void
	{
		$this->store_type = $store_type;
	}
	
	public function getStoreId(): int
	{
		return $this->store_id;
	}
	
	public function setStoreId( int $store_id ): void
	{
		$this->store_id = $store_id;
	}
	
	protected ?Form $edit_form = null;
	protected ?Form $add_form = null;
	
	public function getAddForm() : Form
	{
		if(!$this->add_form) {
			$this->add_form = $this->createForm('delivery_method_add_form');
		}
		
		return $this->add_form;
	}
	
	
	public function getEditForm() : Form
	{
		if(!$this->edit_form) {
			$this->edit_form = $this->createForm('delivery_method_edit_form_'.$this->delivery_method_id);
			$this->edit_form->field('delivery_method_id')->setIsReadonly(true);
		}
		
		return $this->edit_form;
	}
	
}
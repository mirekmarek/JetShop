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
use JetApplication\Payment_Method;

class Config_PaymentMapItem extends Config_Section implements Form_Definition_Interface
{
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Payment method: ',
		select_options_creator: [
			Payment_Method::class,
			'getScope'
		]
	)]
	protected int $payment_method_id = 0;
	
	
	#[Config_Definition(
		type: Config::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Type: ',
		select_options_creator: [
			self::class,
			'getPaymentTypesScope'
		],
	)]
	protected int $type = 0;
	
	
	public function __construct( array $data=[] )
	{
		if( $data ) {
			$this->payment_method_id = (int)$data['payment_method_id']??0;
			$this->type = (int)$data['type']??0;
		}
	}
	
	
	/** @noinspection SpellCheckingInspection */
	public static function getPaymentTypesScope() : array
	{
		return [
			1 => Tr::_('dobírka'),
			2 => Tr::_('hotově při osobním převzetí'),
			3 => Tr::_('platební karta'),
			4 => Tr::_('převod na účet'),
			
		];
	}
	
	public function getPaymentMethodId(): int
	{
		return $this->payment_method_id;
	}
	
	public function setPaymentMethodId( int $payment_method_id ): void
	{
		$this->payment_method_id = $payment_method_id;
	}
	
	public function getType(): int
	{
		return $this->type;
	}
	
	public function setType( int $type ): void
	{
		$this->type = $type;
	}
	
	protected ?Form $edit_form = null;
	protected ?Form $add_form = null;
	
	public function getAddForm() : Form
	{
		if(!$this->add_form) {
			$this->add_form = $this->createForm('payment_method_add_form');
		}
		
		return $this->add_form;
	}
	
	
	public function getEditForm() : Form
	{
		if(!$this->edit_form) {
			$this->edit_form = $this->createForm('payment_method_edit_form_'.$this->payment_method_id);
			$this->edit_form->field('payment_method_id')->setIsReadonly(true);
		}
		
		return $this->edit_form;
	}
	
}
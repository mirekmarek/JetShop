<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

use JetApplication\Delivery_Method;


#[DataModel_Definition(
	name: 'delivery_method_payment_method',
	database_table_name: 'delivery_methods_payment_methods',
	parent_model_class: Delivery_Method::class,
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Delivery_Method_PaymentMethods extends DataModel_Related_1toN
{
	#[DataModel_Definition(related_to: 'main.id')]
	protected int $delivery_method_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
	)]
	protected int $payment_method_id = 0;

	public function getArrayKeyValue(): string
	{
		return $this->payment_method_id;
	}

	public function getDeliveryMethodId(): int
	{
		return $this->delivery_method_id;
	}

	public function setDeliveryMethodId( int $delivery_method_id ): void
	{
		$this->delivery_method_id = $delivery_method_id;
	}

	public function getPaymentMethodId(): int
	{
		return $this->payment_method_id;
	}

	public function setPaymentMethodId( int $payment_method_id ): void
	{
		$this->payment_method_id = $payment_method_id;
	}
}
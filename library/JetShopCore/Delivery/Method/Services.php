<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_method_service',
	database_table_name: 'delivery_methods_services',
	parent_model_class: Delivery_Method::class,
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Delivery_Method_Services extends DataModel_Related_1toN
{
	#[DataModel_Definition(related_to: 'main.code')]
	protected string $delivery_method_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		form_field_type: false
	)]
	protected string $service_code = '';

	protected Services_Service|null|bool $service = null;

	public function getArrayKeyValue(): string
	{
		return $this->service_code;
	}

	/**
	 * @return string
	 */
	public function getDeliveryMethodCode(): string
	{
		return $this->delivery_method_code;
	}

	/**
	 * @param string $delivery_method_code
	 */
	public function setDeliveryMethodCode( string $delivery_method_code ): void
	{
		$this->delivery_method_code = $delivery_method_code;
	}

	/**
	 * @return string
	 */
	public function getServiceCode(): string
	{
		return $this->service_code;
	}

	/**
	 * @param string $service_code
	 */
	public function setServiceCode( string $service_code ): void
	{
		$this->service_code = $service_code;
	}

	public function getService() : ?Services_Service
	{
		if($this->service===null) {
			$this->service = Services_Service::get($this->service_code);
			if(!$this->service) {
				$this->service = false;
			}
		}

		return $this->service ? : null;
	}

}
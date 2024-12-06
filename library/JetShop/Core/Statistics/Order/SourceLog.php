<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Http_Request;
use Jet\Locale;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'order_source_log',
	database_table_name: 'order_source_log',
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Statistics_Order_SourceLog extends DataModel {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $eshop_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE,
		is_key: true,
	)]
	protected ?Locale $locale = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $order_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $http_referer = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $referer_domain = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $utm_source = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $utm_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $utm_medium = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $utm_campaign = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $utm_source_platform = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $utm_term = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $utm_content = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $utm_creative_format = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $utm_marketing_tactic = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $gad_source = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true,
	)]
	protected string $gclid = '';
	
	public function setOrder( Order $order ): void
	{
		$this->order_id = $order->getId();
		$this->eshop_code = $order->getEshopCode();
		$this->locale = $order->getLocale();
	}
	
	public function init() : void
	{
		$GET = Http_Request::GET();
		
		$http_referer = $_SERVER['HTTP_REFERER'] ?? '';
		$referer_domain = '';
		
		$this->http_referer = $http_referer;
		if($http_referer) {
			$referer_domain = parse_url( $http_referer, PHP_URL_HOST );
		}
		
		
		$this->http_referer = $http_referer;
		$this->referer_domain = $referer_domain;
		$this->utm_source = $GET->getString('utm_source');
		$this->utm_id = $GET->getString('utm_id');
		$this->utm_medium = $GET->getString('utm_medium');
		$this->utm_campaign = $GET->getString('utm_campaign');
		$this->utm_source_platform = $GET->getString('utm_source_platform');
		$this->utm_term = $GET->getString('utm_term');
		$this->utm_content = $GET->getString('utm_content');
		$this->utm_creative_format = $GET->getString('utm_creative_format');
		$this->utm_marketing_tactic = $GET->getString('utm_marketing_tactic');
		$this->gad_source = $GET->getString('gad_source');
		$this->gclid = $GET->getString('gclid');
	}
}
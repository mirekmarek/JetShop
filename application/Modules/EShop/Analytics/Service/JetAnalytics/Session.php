<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */


namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Auth;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Http_Request;
use Jet\MVC;
use Jet\Session as Jet_Session;
use Jet\Data_DateTime;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\EShops;


#[DataModel_Definition(
	name: 'ja_session',
	database_table_name: 'ja_session',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Session extends DataModel implements EShopEntity_HasEShopRelation_Interface
{
	use EShopEntity_HasEShopRelation_Trait;
	
	protected static ?Jet_Session $jet_session = null;
	protected static null|false|Session $current = null;
	
	protected array $actualized_properties = [];
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $start_date_time = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $last_activity_date_time = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $IP_address = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 1024
	)]
	protected string $user_agent = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 2048
	)]
	protected string $first_page_URL = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 2048
	)]
	protected string $last_page_URL = '';
	

	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $customer_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $purchased = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $shopping_cart_used = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 1024
	)]
	protected string $http_referer = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $referer_domain = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $utm_source = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $utm_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $utm_medium = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $utm_campaign = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $utm_source_platform = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $utm_term = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $utm_content = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $utm_creative_format = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $utm_marketing_tactic = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $gad_source = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $gclid = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $source = '';
	
	/**
	 * @var Event[]
	 */
	protected array $events = [];
	
	protected ?Event $default_event = null;
	
	protected static array $srouce_GET_prams_to_be_saved = [
		'utm_source',
		'utm_id',
		'utm_medium',
		'utm_campaign',
		'utm_source_platform',
		'utm_term',
		'utm_content',
		'utm_creative_format',
		'utm_marketing_tactic',
		'gad_source',
		'gclid'
	];
	
	
	
	protected static function getJetSession() : Jet_Session
	{
		if(!static::$jet_session) {
			static::$jet_session = new Jet_Session('__jet_session__');
		}
		
		return static::$jet_session;
	}
	
	public static function isRobot() : bool
	{
		return Session_RobotDetector::isRobot();
	}

	public static function getCurrent() : static|false
	{
		if( static::$current===null ) {
			if(static::isRobot()) {
				static::$current = false;
				return false;
			}

			$jet_session = static::getJetSession();
			$session_id = $jet_session->getValue('session_id', 0);
			
			$session = null;
			if($session_id) {
				$session = static::load( $session_id );
			}
			
			if(!$session) {
				$session = new static();
				$session->newSessionStarted();
				
				$session_id = $session->id;
				
				$jet_session->setValue('session_id', $session_id);
			}
			
			static::$current = $session;
			
			if(MVC::getPage()) {
				$default_event = Event_PageView::create();
				$default_event->init();
				$session->setDefaultEvent( $default_event );
			}
			
			register_shutdown_function( function() {
				static::$current->actualizeSeession();
			} );
		}
		
		
		return static::$current;
	}
	
	protected function newSessionStarted() : void
	{
		$this->setEshop( EShops::getCurrent() );
		$this->IP_address = Http_Request::clientIP();
		$this->user_agent = $_SERVER['HTTP_USER_AGENT']??'';
		$this->start_date_time = Data_DateTime::now();
		$this->first_page_URL = Http_Request::currentURL();


		$this->http_referer = $_SERVER['HTTP_REFERER'] ?? '';
		if($this->http_referer) {
			$referer_domain = parse_url( $this->http_referer, PHP_URL_HOST );
			if($referer_domain) {
				$this->referer_domain = $referer_domain;
			}
		}
		
		$GET_params = [
		];
		$GET = Http_Request::GET();
		
		foreach(static::$srouce_GET_prams_to_be_saved as $param) {
			$this->{$param} = $GET->getString($param, default_value: '');
		}
		
		$this->save();
	}

	protected function actualizeSeession() : void
	{
		$this->setLastActivityDateTime( Data_DateTime::now() );
		$this->setLastPageURL( Http_Request::currentURL() );
		if(Auth::getCurrentUser()) {
			$this->setCustomerId( Auth::getCurrentUser()->getId() );
		}
		
		static::updateData(
			$this->actualized_properties,
			[
				'id' => $this->id
			]
		);
		

		if($this->default_event) {
			$this->default_event->save();
			Session_EventMap::create( $this, $this->default_event );
		}

		foreach($this->events as $event) {
			$event->save();
			Session_EventMap::create( $this, $event );
		}
	}
	
	public function getDefaultEvent(): ?Event
	{
		return $this->default_event;
	}
	
	public function setDefaultEvent( ?Event $default_event ): void
	{
		$this->default_event = $default_event;
	}
	
	
	public function addEvent( Event $event ) : void
	{
		if($event->cancelDefaultEvent()) {
			$this->default_event = null;
		}
		
		$this->events[] = $event;
	}
	
	public function getLastPageURL(): string
	{
		return $this->last_page_URL;
	}
	
	public function setLastPageURL( string $last_page_URL ): void
	{
		$this->last_page_URL = $last_page_URL;
		$this->actualized_properties['last_page_URL'] = $this->last_page_URL;
	}
	
	
	
	public function setLastActivityDateTime( Data_DateTime $last_activity_date_time ): void
	{
		$this->last_activity_date_time = $last_activity_date_time;
		$this->actualized_properties['last_activity_date_time'] = $this->last_activity_date_time;
	}
	
	
	
	public function setCustomerId( int $customer_id ): void
	{
		$this->customer_id = $customer_id;
		$this->actualized_properties['customer_id'] = $this->customer_id;
	}
	
	public function setPurchased( bool $purchased ): void
	{
		$this->purchased = $purchased;
		$this->actualized_properties['purchased'] = $this->purchased;
	}
	
	public function setShoppingCartUsed( bool $shopping_cart_used ): void
	{
		$this->shopping_cart_used = $shopping_cart_used;
		$this->actualized_properties['shopping_cart_used'] = $this->shopping_cart_used;
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function getStartDateTime(): ?Data_DateTime
	{
		return $this->start_date_time;
	}
	
	public function getLastActivityDateTime(): ?Data_DateTime
	{
		return $this->last_activity_date_time;
	}
	
	public function getIPAddress(): string
	{
		return $this->IP_address;
	}
	
	public function getUserAgent(): string
	{
		return $this->user_agent;
	}
	
	public function getCustomerId(): int
	{
		return $this->customer_id;
	}
	
	public function isPurchased(): bool
	{
		return $this->purchased;
	}
	
	public function isShoppingCartUsed(): bool
	{
		return $this->shopping_cart_used;
	}
	
	public function getActualizedProperties(): array
	{
		return $this->actualized_properties;
	}
	
	public function getHttpReferer(): string
	{
		return $this->http_referer;
	}
	
	public function getRefererDomain(): string
	{
		return $this->referer_domain;
	}
	
	public function getUtmSource(): string
	{
		return $this->utm_source;
	}
	
	public function getUtmId(): string
	{
		return $this->utm_id;
	}
	
	public function getUtmMedium(): string
	{
		return $this->utm_medium;
	}
	
	public function getUtmCampaign(): string
	{
		return $this->utm_campaign;
	}
	
	public function getUtmSourcePlatform(): string
	{
		return $this->utm_source_platform;
	}
	
	public function getUtmTerm(): string
	{
		return $this->utm_term;
	}
	
	public function getUtmContent(): string
	{
		return $this->utm_content;
	}
	
	public function getUtmCreativeFormat(): string
	{
		return $this->utm_creative_format;
	}
	
	public function getUtmMarketingTactic(): string
	{
		return $this->utm_marketing_tactic;
	}
	
	public function getGadSource(): string
	{
		return $this->gad_source;
	}
	
	public function getGclid(): string
	{
		return $this->gclid;
	}
	
	public function getFirstPageURL(): string
	{
		return $this->first_page_URL;
	}
	
	/**
	 * @return Session_EventMap[]
	 */
	public function getEventMap() : array
	{
		return Session_EventMap::getMap( $this );
	}
	
	public function getSource() : string
	{
		if(!$this->source) {
			$this->source = Session_SourceDetector::detect( $this );
			
			static::updateData(
				data: [
					'source' => $this->source
				],
				where: [
					'id' => $this->id
				]
			);
		}
		
		return $this->source;
	}
	
	public function afterAdd() : void
	{
		$this->getSource();
	}
	
	public static function determineSources() : void
	{
		$session_ids = static::dataFetchCol(
			select: ['id'],
			where: ['source'=>''],
			limit: 50000
		);
		
		foreach($session_ids as $id) {
			echo "{$id}\n";
			static::load( $id )?->getSource();
		}
	}
	
	public function delete() : void
	{
		foreach($this->getEventMap() as $event_map_item) {
			$event_map_item->getEvent()?->delete();
			$event_map_item->delete();
		}
		
		parent::delete();
	}
	
	public static function cleanup() : void
	{
		$ttl = new Data_DateTime( date('Y-m-d H:i:s', strtotime('-12 hours')) );
		
		$ids = static::dataFetchCol(
			select: ['id'],
			where: [
				'start_date_time' => static::getDataModelDefinition()->getProperty('last_activity_date_time'),
				'AND',
				'last_activity_date_time < ' => $ttl
			],
			order_by: ['-id'],
			limit: 10000
		);
		
		foreach($ids as $id) {
			$session = static::load( $id );
			if(!$session) {
				continue;
			}
			echo "{$id} - {$session->getStartDateTime()}\n";
			
			$session->delete();
		}
	}
	
}

/*
<?php
// ==========================================================================
// 1. KONFIGURACE (Doplňte své údaje)
// ==========================================================================
$developerToken = 'ZDE_VLOZTE_DEV_TOKEN';
$customerId     = '1234567890'; // ID Google Ads účtu BEZ pomlček (jen čísla)
$gclid          = 'ZDE_VLOZTE_TESTOVACI_GCLID';

// OAuth 2.0 údaje (získané z Google Cloud Console)
$clientId     = 'ZDE_VLOZTE_CLIENT_ID';
$clientSecret = 'ZDE_VLOZTE_CLIENT_SECRET';
$refreshToken = 'ZDE_VLOZTE_REFRESH_TOKEN';

// Aktuální stabilní verze Google Ads API pro rok 2026
$apiVersion = 'v17';

// ==========================================================================
// 2. ZÍSKÁNÍ AKTUÁLNÍHO ACCESS TOKENU (OAuth 2.0)
// ==========================================================================
$tokenUrl = 'https://googleapis.com';
$tokenPostFields = http_build_query([
    'client_id'     => $clientId,
    'client_secret' => $clientSecret,
    'refresh_token' => $refreshToken,
    'grant_type'    => 'refresh_token'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $tokenPostFields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

$tokenResponse = curl_exec($ch);
if (curl_errno($ch)) {
    die('Chyba při získávání Access Tokenu: ' . curl_error($ch));
}
curl_close($ch);

$tokenData = json_decode($tokenResponse, true);
if (isset($tokenData['error'])) {
    die('OAuth Chyba: ' . $tokenData['error_description']);
}

$accessToken = $tokenData['access_token'];

// ==========================================================================
// 3. VOLÁNÍ GOOGLE ADS API (Získání dat o kliknutí)
// ==========================================================================
$apiUrl = "https://googleapis.com{$apiVersion}/customers/{$customerId}/googleAds:search";

// Definice dotazu v jazyce GAQL
$gaqlQuery = "SELECT
                click_view.gclid,
                campaign.id,
                campaign.name,
                ad_group.id,
                ad_group.name
              FROM click_view
              WHERE click_view.gclid = '{$gclid}'";

$apiPayload = json_encode([
    'query' => $gaqlQuery
]);

$apiHeaders = [
    "Authorization: Bearer {$accessToken}",
    "developer-token: {$developerToken}",
    "Content-Type: application/json"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $apiPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $apiHeaders);

$apiResponse = curl_exec($ch);

if (curl_errno($ch)) {
    die('Chyba při komunikaci s Google Ads API: ' . curl_error($ch));
}
curl_close($ch);

// ==========================================================================
// 4. ZPRACOVÁNÍ VÝSLEDKŮ
// ==========================================================================
$result = json_decode($apiResponse, true);

// Ošetření případné chyby přímo z API
if (isset($result['error'])) {
    echo "API vrátilo chybu:\n";
    print_r($result['error']);
    exit;
}

// Výpis nalezených dat
if (!empty($result['results'])) {
    echo "Data k zadanému GCLID nalezena:\n\n";
    foreach ($result['results'] as $row) {
        $clickView = $row['clickView'] ?? [];
        $campaign  = $row['campaign'] ?? [];
        $adGroup   = $row['adGroup'] ?? [];

        echo "GCLID: " . ($clickView['gclid'] ?? 'N/A') . "\n";
        echo "Kampaň ID: " . ($campaign['id'] ?? 'N/A') . "\n";
        echo "Kampaň Název: " . ($campaign['name'] ?? 'N/A') . "\n";
        echo "Sestava ID: " . ($adGroup['id'] ?? 'N/A') . "\n";
        echo "Sestava Název: " . ($adGroup['name'] ?? 'N/A') . "\n";
        echo "-----------------------------------------\n";
    }
} else {
    echo "Pro zadané GCLID nebyl nalezen žádný záznam (může jít o kliknutí starší 90 dnů, nebo se data ještě nepropisují).\n";
}
?>

 */
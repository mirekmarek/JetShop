<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\NumberSeriesManager;


use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use Jet\IO_File;
use Jet\Config;
use Jet\Tr;
use JetApplication\EShopConfig;
use JetApplication\NumberSeries_Counter_Month;
use JetApplication\NumberSeries_Counter_Total;
use JetApplication\NumberSeries_Counter_Year;
use JetApplication\NumberSeries_Counter_Day;
use JetApplication\EShop;

#[Config_Definition(
	name: 'NumberSeries'
)]
class EntityConfig extends Config implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	#[Config_Definition(
		type: Config::TYPE_INT
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Number pad length: ',
		is_required: true,
	)]
	protected int $pad_length = 5;
	
	#[Config_Definition(
		type: Config::TYPE_STRING
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Prefix: ',
		is_required: false,
	)]
	protected string $prefix = '';
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Counter: ',
		is_required: true,
		select_options_creator: [
			self::class,
			'getCounterClasses'
		],
		
	)]
	protected string $counter_class = NumberSeries_Counter_Total::CLASS;
	
	public static function getCounterClasses(): array
	{
		return [
			NumberSeries_Counter_Total::class => Tr::_( 'Total' ),
			NumberSeries_Counter_Year::class  => Tr::_( 'Year' ),
			NumberSeries_Counter_Month::class => Tr::_( 'Month' ),
			NumberSeries_Counter_Day::class   => Tr::_( 'Day' ),
		];
	}
	
	
	public function __construct( string $entity_type, ?EShop $eshop=null, ?array $data = null )
	{
		$this->_config_file_path = EShopConfig::getRootDir() . 'number_series/'.$entity_type.'/';
		
		if($eshop) {
			$this->_config_file_path .= $eshop->getKey().'.php';
		} else {
			$this->_config_file_path .= 'general.php';
		}
		
		if($data===null) {
			
			if(!IO_File::exists($this->_config_file_path)) {
				$this->saveConfigFile();
			}
		}
		
		if( $data === null ) {
			$data = $this->readConfigFileData();
		}
		
		$this->setData( $data );
	}
	
	public function getPadLength(): int
	{
		return $this->pad_length;
	}
	
	public function getPrefix(): string
	{
		return $this->prefix;
	}
	
	public function getCounterClass(): string
	{
		return $this->counter_class;
	}

	
	
}
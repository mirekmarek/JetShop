<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\Application_Service_General;
use JetApplication\EShop;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_General::GROUP,
	is_mandatory: true,
	name: 'Calendar',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Application_Service_General_Calendar extends Application_Module
{
	
	abstract public function getNextBusinessDate( ?EShop $eshop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime;
	abstract public function getPrevBusinessDate( ?EShop $eshop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime;
	
	abstract public function calcBusinessDaysOffset( EShop $eshop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int;
	abstract public function calcBusinessDaysSubset( EShop $eshop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int;
	
	abstract public function howManyWorkingDays( EShop $eshop, Data_DateTime|string $from, Data_DateTime|string $till, bool $use_holydays=true ) : int;
	
}
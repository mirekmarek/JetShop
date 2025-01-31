<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_DateTime;
use JetApplication\EShop;

interface Core_Calendar_Manager {
	
	public function getNextBusinessDate( ?EShop $eshop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime;
	public function getPrevBusinessDate( ?EShop $eshop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime;
	
	public function calcBusinessDaysOffset( EShop $eshop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int;
	public function calcBusinessDaysSubset( EShop $eshop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int;
	
	public function howManyWorkingDays( EShop $eshop, Data_DateTime|string $from, Data_DateTime|string $till, bool $use_holydays=true ) : int;
	
}
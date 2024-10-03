<?php
namespace JetShop;

use Jet\Data_DateTime;
use JetApplication\Shops_Shop;

interface Core_Calendar_Manager {
	
	public function getNextBusinessDate( ?Shops_Shop $shop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime;
	public function getPrevBusinessDate( ?Shops_Shop $shop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime;
	
	public function calcBusinessDaysOffset( Shops_Shop $shop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int;
	public function calcBusinessDaysSubset( Shops_Shop $shop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int;
	
	public function howManyWorkingDays( Shops_Shop $shop, Data_DateTime|string $from, Data_DateTime|string $till, bool $use_holydays=true ) : int;
	
}
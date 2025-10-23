<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\Application_Service_MetaInfo;
use JetShop\Core_Application_Service_General_Calendar;

#[Application_Service_MetaInfo]
abstract class Application_Service_General_Calendar extends Core_Application_Service_General_Calendar {
	
	abstract public function getNumberOfDaysRequiredForDispatch( EShop $eshop ) : Calendar_DispatchDeadlineInfo;
}

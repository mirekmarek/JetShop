<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Application_Service_General_EMailMarketingSubscribeManager;
use JetApplication\Application_Service_General;

class Core_EMailMarketing {
	
	protected static ?Application_Service_General_EMailMarketingSubscribeManager $subscribe_manager = null;
	
	public static function SubscriptionManager() : Application_Service_General_EMailMarketingSubscribeManager
	{
		return Application_Service_General::EMailMarketingSubscribe();
	}
	
	
}
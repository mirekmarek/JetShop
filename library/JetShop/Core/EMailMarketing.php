<?php
namespace JetShop;

use JetApplication\EMailMarketing_Subscribe_Manager;
use JetApplication\Managers_General;

class Core_EMailMarketing {
	
	protected static ?EMailMarketing_Subscribe_Manager $subscribe_manager = null;
	
	public static function SubscriptionManager() : EMailMarketing_Subscribe_Manager
	{
		return Managers_General::EMailMarketingSubscribe();
	}
	
	
}
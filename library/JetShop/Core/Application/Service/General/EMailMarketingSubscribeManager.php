<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\Application_Service_EShop;
use JetApplication\Application_Service_General;
use JetApplication\EMailMarketing_Subscribe;
use JetApplication\EMailMarketing_Subscribe_Log;
use JetApplication\EShop;
use Jet\Application_Service_MetaInfo;
use JetApplication\EShop_Pages;
use JetApplication\EShops;

#[Application_Service_MetaInfo(
	group: Application_Service_General::GROUP,
	is_mandatory: true,
	name: 'E-mail marketing subscribe',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Application_Service_General_EMailMarketingSubscribeManager extends Application_Module
{
	public function isSubscribed( EShop $eshop, string $email_address ) : bool
	{
		$exists_reg = EMailMarketing_Subscribe::get( $eshop, $email_address );
		if($exists_reg) {
			return true;
		}
		
		return false;
	}
	
	public function isSubscribedByBackend( EShop $eshop, string $email_address ) : bool
	{
		return Application_Service_EShop::EMailMarketingSubscribeManagerBackend( $eshop )?->isSubscribed( $eshop, $email_address )??false;
	}
	
	public function subscribe( EShop $eshop, string $email_address, string $source, string $comment='' ) : void
	{
		$exists_reg = EMailMarketing_Subscribe::get( $eshop, $email_address );
		if($exists_reg) {
			return;
		}
		
		$reg = new EMailMarketing_Subscribe();
		$reg->setEshop( $eshop );
		$reg->setEmailAddress( $email_address );
		
		$reg->save();
		
		EMailMarketing_Subscribe_Log::subscribe(
			$eshop,
			$email_address,
			$source,
			$comment
		);
		
		Application_Service_EShop::EMailMarketingSubscribeManagerBackend( $eshop )?->subscribe( $eshop, $email_address, $source, $comment );
	}
	
	
	public function delete( EShop $eshop, string $email_address, string $source, string $comment='' ) : void
	{
		foreach(EShops::getList() as $eshop) {
			$exists_reg = EMailMarketing_Subscribe::get( $eshop, $email_address );
			$exists_reg?->delete();
			
			Application_Service_EShop::EMailMarketingSubscribeManagerBackend( $eshop )?->delete( $eshop, $email_address, $source, $comment );
		}
	}
	
	
	public function unsubscribe( EShop $eshop, string $email_address, string $source, string $comment='' ) : void
	{
		foreach(EShops::getList() as $eshop) {
			$exists_reg = EMailMarketing_Subscribe::get( $eshop, $email_address );
			if($exists_reg) {
				$exists_reg->delete();
				
				EMailMarketing_Subscribe_Log::unsubscribe(
					$eshop,
					$email_address,
					$source,
					$comment
				);
			}
			
			
			Application_Service_EShop::EMailMarketingSubscribeManagerBackend( $eshop )?->unsubscribe( $eshop, $email_address, $source, $comment );
		}
	}
	
	
	public function changeMail( EShop $eshop, string $old_email_address, string $new_mail_address, string $source, string $comment='' ) : void
	{
		
		$exists_reg = EMailMarketing_Subscribe::get( $eshop, $old_email_address );
		
		if($exists_reg) {
			EMailMarketing_Subscribe::updateData(
				[
					'email_address' => $new_mail_address
				],
				[
					'email_address' => $old_email_address,
					'AND',
					$eshop->getWhere()
				]
			);
		}
		
		EMailMarketing_Subscribe_Log::changeMail(
			$eshop,
			$old_email_address,
			$new_mail_address,
			$source,
			$comment
		);
		
		Application_Service_EShop::EMailMarketingSubscribeManagerBackend( $eshop )?->changeMail( $eshop, $old_email_address, $new_mail_address, $source, $comment );
	}
	
	public function generateUnsubscribeKey( EShop $eshop, string $email ) : string
	{
		return sha1( $email.$eshop->getKey().__FILE__ );
	}
	
	public function getSubscribePageURL( EShop $eshop ) : string
	{
		return EShop_Pages::MailingSubscribe( $eshop )?->getURL()??'';
	}
	
	public function getUnscubscribePageURL( EShop $eshop, string $email ) : string
	{
		return EShop_Pages::MailingUnsubscribe( $eshop )?->getURL(
			GET_params: [
				'm' => base64_encode($email),
				'k' => $this->generateUnsubscribeKey( $eshop, $email ),
			]
		)??'';
	}
	
	abstract public function showStatus( EShop $eshop, string $email ) : string;
	
}
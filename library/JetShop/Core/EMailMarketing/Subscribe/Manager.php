<?php
namespace JetShop;


use Jet\Application_Module;
use JetApplication\EMailMarketing_Subscribe;
use JetApplication\EMailMarketing_Subscribe_Log;
use JetApplication\Shops_Shop;

abstract class Core_EMailMarketing_Subscribe_Manager extends Application_Module
{
	public function subscribe( Shops_Shop $shop, string $email_address, string $source, string $comment='' ) : void
	{
		$exists_reg = EMailMarketing_Subscribe::get( $shop, $email_address );
		if($exists_reg) {
			return;
		}
		
		$reg = new EMailMarketing_Subscribe();
		$reg->setShop( $shop );
		$reg->setEmailAddress( $email_address );
		
		$reg->save();
		
		EMailMarketing_Subscribe_Log::subscribe(
			$shop,
			$email_address,
			$source,
			$comment
		);
	}
	
	public function unsubscribe( Shops_Shop $shop, string $email_address, string $source, string $comment='' ) : void
	{
		$exists_reg = EMailMarketing_Subscribe::get( $shop, $email_address );
		if(!$exists_reg) {
			return;
		}
		
		$exists_reg->delete();
		
		EMailMarketing_Subscribe_Log::unsubscribe(
			$shop,
			$email_address,
			$source,
			$comment
		);
	}
	
	
	public function changeMail( Shops_Shop $shop,string $old_email_address, string $new_mail_address, string $source, string $comment='' ) : void
	{
		
		$exists_reg = EMailMarketing_Subscribe::get( $shop, $old_email_address );
		
		if($exists_reg) {
			EMailMarketing_Subscribe::updateData(
				[
					'email_address' => $new_mail_address
				],
				[
					'email_address' => $old_email_address,
					'AND',
					$shop->getWhere()
				]
			);
		}
		
		EMailMarketing_Subscribe_Log::changeMail(
			$shop,
			$old_email_address,
			$new_mail_address,
			$source,
			$comment
		);
		
	}
	
	abstract public function showStatus( Shops_Shop $shop, string $email ) : string;

}
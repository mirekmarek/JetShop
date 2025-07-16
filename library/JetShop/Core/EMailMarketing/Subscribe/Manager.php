<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use JetApplication\EMailMarketing_Subscribe;
use JetApplication\EMailMarketing_Subscribe_Log;
use JetApplication\EShop;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'E-mail marketing subscribe',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_EMailMarketing_Subscribe_Manager extends Application_Module
{
	public function isSubscribed( EShop $eshop, string $email_address ) : bool
	{
		$exists_reg = EMailMarketing_Subscribe::get( $eshop, $email_address );
		if($exists_reg) {
			return true;
		}
		
		return false;
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
	}
	
	public function unsubscribe( EShop $eshop, string $email_address, string $source, string $comment='' ) : void
	{
		$exists_reg = EMailMarketing_Subscribe::get( $eshop, $email_address );
		if(!$exists_reg) {
			return;
		}
		
		$exists_reg->delete();
		
		EMailMarketing_Subscribe_Log::unsubscribe(
			$eshop,
			$email_address,
			$source,
			$comment
		);
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
		
	}
	
	abstract public function showStatus( EShop $eshop, string $email ) : string;

}
<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops_Shop;

/**
 *
 */
#[DataModel_Definition(
	name: 'mailing_subscribe',
	database_table_name: 'mailing_subscribe',
)]
abstract class Core_EMailMarketing_Subscribe extends Entity_WithShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		is_id: true,
		max_len: 255,
	)]
	protected string $email_address = '';
	

	public static function get( Shops_Shop $shop, string $email_address ) : static|null
	{
		return static::load( [
			$shop->getWhere(),
			'AND',
			'email_address' => $email_address
		] );
	}

	
	public function setEmailAddress( string $value ) : void
	{
		$this->email_address = $value;
	}
	
	public function getEmailAddress() : string
	{
		return $this->email_address;
	}
	
}

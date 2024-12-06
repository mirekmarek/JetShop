<?php
namespace JetShop;

use Jet\Auth;
use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'administrator_signature',
	database_table_name: 'administrators_signatures',
)]
class Core_AdministratorSignatures extends Entity_WithEShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $user_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 1000,
	)]
	protected string $signature = '';
	
	public static function getSignature( EShop $eshop, ?int $user_id=null ) : string
	{
		if(!$user_id) {
			$user_id = Auth::getCurrentUser()->getId();
		}
		
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['user_id'] = $user_id;
		
		$signature = static::dataFetchOne(select: ['signature'], where: $where);
		if(!$signature) {
			$signature = '';
		}
		
		return $signature;
	}
	
	public static function setSignature( EShop $eshop, int $user_id, string $signature ) : void
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['user_id'] = $user_id;
		
		$i = static::load($where);
		if(!$i) {
			$i = new static();
			$i->setEshop( $eshop );
			$i->user_id = $user_id;
		}
		$i->signature = $signature;
		
		$i->save();
	}
	
}
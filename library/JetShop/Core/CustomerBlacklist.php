<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Field;
use Jet\Form_Definition;
use JetApplication\CustomerBlacklist;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'customer_blacklist',
	database_table_name: 'customers_blacklist',
)]
abstract class Core_CustomerBlacklist extends EShopEntity_WithEShopRelation
{

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'E-mail',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter e-mail address',
			Form_Field::ERROR_CODE_INVALID_FORMAT => 'Please enter e-mail address'
		]
	)]
	protected string $email = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name',
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description'
	)]
	protected string $description = '';
	
	public static function get( string|int $id ) : static|null
	{
		return static::load( $id );
	}
	
	
	public function getEmail() : string
	{
		return $this->email;
	}
	
	public function setEmail( string $email ) : void
	{
		$this->email = strtolower( $email );
	}
	
	
	
	public function getName() : string
	{
		return $this->name;
	}
	
	
	public function setName( string $name ) : void
	{
		$this->name = $name;
	}
	
	
	public function getDescription() : string
	{
		return $this->description;
	}
	
	
	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}
	
	public static function add(
		EShop  $eshop,
		string $email,
		string $name,
		string $description
	) : static
	{
		$item = new static();
		$item->setEshop( $eshop );
		$item->setEmail( $email );
		$item->setName( $name );
		$item->setDescription( $description );
		$item->save();
		
		return $item;
	}
	
	public static function customerIsBlacklisted(
		string             $email,
		?EShop             $eshop=null,
		bool               $load_details = false,
		?CustomerBlacklist &$details = null
	) : bool
	{
		$email = strtolower($email);
		
		if($eshop) {
			$where = $eshop->getWhere();
			$where[] = 'AND';
			$where['email'] = $email;
		} else {
			$where = [
				'email' => $email
			];
		}
		
		$id = static::dataFetchOne(select: ['id'], where: $where);
		if( !$id ) {
			return false;
		}
		
		if($load_details) {
			$details = static::load($id);
		}
		
		return true;
	}
	
}
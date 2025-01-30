<?php
namespace JetShop;

use Jet\Auth;
use Jet\DataModel_Definition;

use Jet\IO_Dir;
use Jet\SysConf_Path;
use JetApplication\EShopEntity_Note;
use JetApplication\ReturnOfGoods;

#[DataModel_Definition(
	name: 'return_of_goods_notes',
	database_table_name: 'return_of_goods_notes',
)]
abstract class Core_ReturnOfGoods_Note extends EShopEntity_Note {
	
	
	protected ?ReturnOfGoods $return_of_goods = null;
	
	
	public function getReturnOfGoodsId(): int
	{
		return $this->entity_id;
	}
	
	public function setReturnOfGoods( ReturnOfGoods $return_of_goods ): void
	{
		$this->return_of_goods = $return_of_goods;
		$this->entity_id = $return_of_goods->getId();
		$this->setEshop( $return_of_goods->getEshop() );
	}
	
	
	protected function getUploadedFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'return_of_goods_note_files_tmp/'.Auth::getCurrentUser()->getId().'/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	protected function getFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'return_of_goods_note_files/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/'.$this->id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}

}
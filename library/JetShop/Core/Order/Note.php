<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Auth;
use Jet\DataModel_Definition;

use Jet\IO_Dir;
use Jet\SysConf_Path;
use JetApplication\EShopEntity_Note;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'orders_notes',
	database_table_name: 'orders_notes',
)]
abstract class Core_Order_Note extends EShopEntity_Note {
	
	protected ?Order $order = null;
	
	
	public function getOrderId(): int
	{
		return $this->entity_id;
	}
	
	public function setOrder( Order $order ): void
	{
		$this->order = $order;
		$this->entity_id = $order->getId();
		$this->setEshop( $order->getEshop() );
	}
	
	protected function getUploadedFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'order_note_files_tmp/'.Auth::getCurrentUser()->getId().'/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	protected function getFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'order_note_files/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/'.$this->id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}

}
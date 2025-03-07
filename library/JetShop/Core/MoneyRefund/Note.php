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
use JetApplication\MoneyRefund;

#[DataModel_Definition(
	name: 'money_refunds_notes',
	database_table_name: 'money_refunds_notes',
)]
abstract class Core_MoneyRefund_Note extends EShopEntity_Note {
	
	protected ?MoneyRefund $money_refund = null;
	
	public function getMoneyRefundId(): int
	{
		return $this->entity_id;
	}
	
	public function setMoneyRefund( MoneyRefund $money_refund ): void
	{
		$this->money_refund = $money_refund;
		$this->entity_id = $money_refund->getId();
		$this->setEshop( $money_refund->getEshop() );
	}
	
	
	protected function getUploadedFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'money_refund_note_files_tmp/'.Auth::getCurrentUser()->getId().'/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	
	protected function getFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'money_refund_note_files/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/'.$this->id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
}
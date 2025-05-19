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
use JetApplication\ExpectedPayment;

#[DataModel_Definition(
	name: 'expected_payment_notes',
	database_table_name: 'expected_payment_notes',
)]
abstract class Core_ExpectedPayment_Note extends EShopEntity_Note {
	
	
	protected ?ExpectedPayment $expected_payment = null;
	
	
	public function getExpectedPaymentId(): int
	{
		return $this->entity_id;
	}
	
	public function setExpectedPayment( ExpectedPayment $expected_payment ): void
	{
		$this->expected_payment = $expected_payment;
		$this->entity_id = $expected_payment->getId();
		$this->setEshop( $expected_payment->getEshop() );
	}
	
	
	protected function getUploadedFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'expected_payment_note_files_tmp/'.Auth::getCurrentUser()->getId().'/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	protected function getFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'expected_payment_note_files/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/'.$this->id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
}
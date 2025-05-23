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
use JetApplication\Complaint;

#[DataModel_Definition(
	name: 'complaints_notes',
	database_table_name: 'complaints_notes',
)]
abstract class Core_Complaint_Note extends EShopEntity_Note {
	
	protected ?Complaint $complaint = null;
	
	public function getComplaintId(): int
	{
		return $this->entity_id;
	}
	
	public function setComplaint( Complaint $complaint ): void
	{
		$this->complaint = $complaint;
		$this->entity_id = $complaint->getId();
		$this->setEshop( $complaint->getEshop() );
	}
	
	
	protected function getUploadedFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'complaint_note_files_tmp/'.Auth::getCurrentUser()->getId().'/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	
	protected function getFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'complaint_note_files/'.$this->getEshop()->getKey().'/'.$this->entity_id.'/'.$this->id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;
use Jet\IO_File;
use JetApplication\EMail_TemplateText;
use JetApplication\EMail_TemplateText_EShopData;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\Files;
use JetApplication\Application_Service_General;


#[DataModel_Definition(
	name: 'email_templates_attachment',
	database_table_name: 'email_templates_attachment',
	parent_model_class: EMail_TemplateText::class,
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_EMail_TemplateText_Attachment extends DataModel_Related_1toN implements
	EShopEntity_HasEShopRelation_Interface
{
	use EShopEntity_HasEShopRelation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		related_to: 'main.id',
	)]
	protected int $entity_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $file = '';
	
	public function getId(): int
	{
		return $this->id;
	}
	
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	
	public function getFile(): string
	{
		return $this->file;
	}
	
	public function setFile( string $file ): void
	{
		$this->file = $file;
	}
	
	protected function getFileEntityType() : string
	{
		return static::getDataModelDefinition(static::class)->getModelName().'_'.$this->getEshop()->getKey();
	}
	
	public function afterDelete(): void
	{
		Application_Service_General::Files()->deleteFile( $this->getFileEntityType(), $this->entity_id, $this->file );
	}
	
	public function getURL() : string
	{
		return Files::Manager()->getFileURL( $this->getFileEntityType(), $this->entity_id, $this->file );
	}
	
	
	public function getPath() : string
	{
		return Files::Manager()->getFilePath( $this->getFileEntityType(), $this->entity_id, $this->file );
	}
	
	public function getSize() : int
	{
		return IO_File::getSize( $this->getPath() );
	}
	
	public function upload( EMail_TemplateText_EShopData $template_text, string $file_name, string $srouce_file_path ) : void
	{
		$this->setEshop( $template_text->getEshop() );
		$this->entity_id = $template_text->getEntityId();
		$this->file = Files::Manager()->uploadFile( $this->getFileEntityType(), $this->entity_id, $file_name, $srouce_file_path );
	}
	
	
	/**
	 * @param EMail_TemplateText_EShopData $template_text
	 * @return static[]
	 */
	public static function getList( EMail_TemplateText_EShopData $template_text ) : array
	{
		return static::fetch([''=>[
			$template_text->getEshop()->getWhere(),
			'AND',
			'entity_id' => $template_text->getId()
		]]);
	}
	
	
}
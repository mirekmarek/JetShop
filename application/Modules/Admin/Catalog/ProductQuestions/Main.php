<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;

use Jet\Application_Module;
use JetApplication\Admin_Entity_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Trait;
use JetApplication\Admin_Managers_ProductQuestions;
use JetApplication\EMail_TemplateProvider;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\ProductQuestion;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ProductQuestions, EMail_TemplateProvider
{
	use Admin_EntityManager_WithEShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'product-questions';
	
	public const ACTION_GET = 'get';
	public const ACTION_ADD = 'add';
	public const ACTION_UPDATE = 'update';
	public const ACTION_DELETE = 'delete';
	
	
	public static function getEntityNameReadable() : string
	{
		return 'Product question';
	}
	
	public static function getEntityInstance(): Entity_WithEShopRelation|Admin_Entity_WithEShopRelation_Interface
	{
		return new ProductQuestion();
	}
	
	public static function getCurrentUserCanCreate() : bool
	{
		return false;
	}
	
	public function getEMailTemplates(): array
	{
		$template = new EmailTemplate_Answer();
		
		return [
			$template
		];
	}
}
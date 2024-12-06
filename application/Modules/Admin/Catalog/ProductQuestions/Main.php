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
use JetApplication\Admin_EntityManager_WithEShopRelation_Interface;
use JetApplication\Admin_EntityManager_WithEShopRelation_Trait;
use JetApplication\EMail_TemplateProvider;
use JetApplication\Entity_WithEShopRelation;

/**
 *
 */
class Main extends Application_Module implements Admin_EntityManager_WithEShopRelation_Interface, EMail_TemplateProvider
{
	use Admin_EntityManager_WithEShopRelation_Trait;
	
	public const ADMIN_MAIN_PAGE = 'product-questions';

	public const ACTION_GET = 'get_product_question';
	public const ACTION_ADD = 'add_product_question';
	public const ACTION_UPDATE = 'update_product_question';
	public const ACTION_DELETE = 'delete_product_question';
	
	
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
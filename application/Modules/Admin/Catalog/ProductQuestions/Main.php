<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;

use Jet\Application_Module;
use JetApplication\Admin_EntityManager_Trait;
use JetApplication\Admin_Managers_ProductQuestions;
use JetApplication\EMail_TemplateProvider;
use JetApplication\EShopEntity_Basic;
use JetApplication\ProductQuestion;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_ProductQuestions, EMail_TemplateProvider
{
	use Admin_EntityManager_Trait;
	
	public const ADMIN_MAIN_PAGE = 'product-questions';
	
	public const ACTION_GET = 'get';
	public const ACTION_ADD = 'add';
	public const ACTION_UPDATE = 'update';
	public const ACTION_DELETE = 'delete';
	
	
	public static function getEntityInstance(): EShopEntity_Basic
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
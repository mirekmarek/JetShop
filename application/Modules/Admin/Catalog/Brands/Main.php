<?php
namespace JetApplicationModule\Admin\Catalog\Brands;

use JetApplication\Admin_Managers_Trait;
use Jet\Application_Module;

/**
 *
 */
class Main extends Application_Module
{
	use Admin_Managers_Trait;

	public const ADMIN_MAIN_PAGE = 'brands';

	public const ACTION_GET = 'get_brand';
	public const ACTION_ADD = 'add_brand';
	public const ACTION_UPDATE = 'update_brand';
	public const ACTION_DELETE = 'delete_brand';

}
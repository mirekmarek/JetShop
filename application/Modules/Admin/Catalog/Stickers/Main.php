<?php
namespace JetApplicationModule\Admin\Catalog\Stickers;

use JetApplication\Admin_Managers_Trait;

use Jet\Application_Module;

/**
 *
 */
class Main extends Application_Module
{
	use Admin_Managers_Trait;

	public const ADMIN_MAIN_PAGE = 'stickers';

	public const ACTION_GET = 'get_sticker';
	public const ACTION_ADD = 'add_sticker';
	public const ACTION_UPDATE = 'update_sticker';
	public const ACTION_DELETE = 'delete_sticker';
	

}
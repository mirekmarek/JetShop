<?php
namespace JetApplicationModule\Admin\Catalog\Signposts;

use JetApplication\Admin_EntityManager_Trait;

use Jet\Application_Module;
use JetApplication\Entity_Basic;
use JetApplication\Signpost;
use JetApplication\Admin_Managers_Signpost;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Signpost
{
	use Admin_EntityManager_Trait;

	public const ADMIN_MAIN_PAGE = 'signposts';

	public const ACTION_GET = 'get_signpost';
	public const ACTION_ADD = 'add_signpost';
	public const ACTION_UPDATE = 'update_signpost';
	public const ACTION_DELETE = 'delete_signpost';
	
	
	public static function getEntityInstance(): Entity_Basic
	{
		return new Signpost();
	}
	
}
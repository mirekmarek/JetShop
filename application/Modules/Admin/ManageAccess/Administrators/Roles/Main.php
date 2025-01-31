<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ManageAccess\Administrators\Roles;


use Jet\Application_Module;


class Main extends Application_Module
{
	public const ADMIN_MAIN_PAGE = 'administrators-roles';
	
	public const ACTION_GET = 'get_role';
	public const ACTION_ADD = 'add_role';
	public const ACTION_UPDATE = 'update_role';
	public const ACTION_DELETE = 'delete_role';

}
<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\DataModel_Definition;

/**
 *
 */
#[DataModel_Definition(database_table_name: 'events_sysservices')]
class Logger_SysServices_Event extends Logger_Event
{
}
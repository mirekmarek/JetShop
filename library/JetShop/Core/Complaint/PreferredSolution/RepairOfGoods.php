<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Complaint_PreferredSolution;

abstract  class Core_Complaint_PreferredSolution_RepairOfGoods extends Complaint_PreferredSolution {
	
	public const CODE = 'repair';
	protected string $title = 'Repair of goods';
	protected int $priority = 20;
}
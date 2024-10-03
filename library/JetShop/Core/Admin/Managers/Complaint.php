<?php
namespace JetShop;

use JetApplication\Complaint;
use JetApplication\Order;

interface Core_Admin_Managers_Complaint
{
	public function showName( int $id ): string;
	
	public function showComplaintStatus( Complaint $complaint ) : string;
	
	public function showOrderComplaints( Order $order ) : void;
}
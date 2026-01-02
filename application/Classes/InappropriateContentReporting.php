<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\DataModel_Definition;
use Jet\Mailing_Email;
use JetShop\Core_InappropriateContentReporting;

#[DataModel_Definition]
#[EShopEntity_Definition]
class InappropriateContentReporting extends Core_InappropriateContentReporting
{
	public function processNew() : bool
	{
		if(!parent::processNew()) {
			return false;
		}
		
		$subject = 'Nové upozornění na nevhodný obsahů';
		$mail = '<p>Nové upozornění na nevhodný obsahů:</p>';
		
		$mail .= '<table>';
		
		$mail .= '<tr>';
		$mail .= '<td>Jméno</td>';
		$mail .= '<td>'.$this->name.'</td>';
		$mail .= '</tr>';
		
		$mail .= '<tr>';
		$mail .= '<td>e-mail</td>';
		$mail .= '<td><a href="mailto:'.$this->email.'">'.$this->email.'</a></td>';
		$mail .= '</tr>';
		
		$mail .= '<tr>';
		$mail .= '<td>URL</td>';
		$mail .= '<td><a href="'.$this->URL.'">'.$this->URL.'</a></td>';
		$mail .= '</tr>';
		
		$mail .= '<tr>';
		$mail .= '<td>Soubory:</td>';
		$mail .= '<td>';
		
		foreach($this->getImageGallery()->getImages() as $image) {
			$mail .= '<a href="'.$image->getURL().'">'.$image->getImageFileName().'</a>';
		}
		
		$mail .= '</td>';
		$mail .= '</tr>';
		$mail .= '</table>';
		
		$email = new Mailing_Email();
		$email->setSenderEmail('');
		$email->setSubject($subject);
		$email->setBodyHtml( $mail );
		$email->setTo( '' );
		$email->send();
		
		
		return true;
	}
}
<?php
class Mail {
	function SendMail($from, $to, $type, $param) {
		$body = file_get_contents('mail/template.html');
		
		if(isset($param['matiere']))
		{
			$cours_horaires = '<b>' . $param['matiere'] . '</b> du <b>' . $param['date_origine'] . ' � ' . $param['heure_origine'] . '</b>' . ($param['date_report'] != null ? ' au <b>' . $param['date_report'] . ' � ' . $param['heure_report'] . '</b>': '');
		}
		else
		{
			$materiels_horaires = '<b>' . $param['type'] . '</b> <i>' . $param['modele'] . '</i> le <b>' . $param['date'] . ' de ' . $param['heure_debut'] . ' � ' . $param['heure_fin'] . '</b>';
		}
		
		switch($type) {
			case 1: case 'deplacement_cours':
				$subject = 'Demande de d�placement de cours';
				$body = str_replace("%%contenu%%", $param['enseignant'] . " a demand� un d�placement du cours " . ($param['matiere'][0] == 'A' || $param['matiere'][0] == 'E' || $param['matiere'][0] == 'I' || $param['matiere'][0] == 'O' || $param['matiere'][0] == 'U' ? 'd\'' : 'de ') . $cours_horaires . ".", $body);
				$body = str_replace("%%signature%%", "Veuillez vous rendre sur <a href=\"" . SITE_ADRESS . "\">" . SITE_NAME . "</a> pour valider cette demande.", $body);
				break;
			
			case 2: case 'deplacement_accepte':
				$subject = 'Votre demande de d�placement de cours a �t� accept�e';
				$body = str_replace("%%contenu%%", "Cet e-mail confirme le d�placement de votre cours " . ($param['matiere'][0] == 'A' || $param['matiere'][0] == 'E' || $param['matiere'][0] == 'I' || $param['matiere'][0] == 'O' || $param['matiere'][0] == 'U' ? 'd\'' : 'de ') . $cours_horaires . ".", $body);
				$body = str_replace("%%signature%%", "Cordialement, l'administration.", $body);
				break;
			
			case 3: case 'deplacement_refuse':
				$subject = 'Votre demande de d�placement de cours a �t� refus�e';
				$body = str_replace("%%contenu%%", "Cet e-mail indique le refus du d�placement de votre cours " . ($param['matiere'][0] == 'A' || $param['matiere'][0] == 'E' || $param['matiere'][0] == 'I' || $param['matiere'][0] == 'O' || $param['matiere'][0] == 'U' ? 'd\'' : 'de ') . $cours_horaires . ".", $body);
				$body = str_replace("%%signature%%", "Cordialement, l'administration.", $body);
				break;
			
			case 4: case 'deplacement_avertir_classe':
				$subject = 'D�placement du  cours';
				$body = str_replace("%%contenu%%", $param['enseignant'] . " a d�plac� son cours " . ($param['matiere'][0] == 'A' || $param['matiere'][0] == 'E' || $param['matiere'][0] == 'I' || $param['matiere'][0] == 'O' || $param['matiere'][0] == 'U' ? 'd\'' : 'de ') . $cours_horaires . ".", $body);
				$body = str_replace("%%signature%%", "Cordialement, l'administration.", $body);
				break;
				
			case 'materiel_new_prof': //Avertir nouvelle demande de mat�riel depuis le prof
				$subject = 'Demande de r�servation de mat�riel';
				$body = str_replace("%%contenu%%", $param['enseignant'] . " a demand� � r�server le mat�riel suivant : " . $materiels_horaires, $body);
				$body = str_replace("%%signature%%", "Veuillez vous rendre sur <a href=\"" . SITE_ADRESS . "\">" . SITE_NAME . "</a> pour valider cette demande.", $body);
				break;
			
			case 'materiel_new_superviseur': //Avertir nouvelle demande de mat�riel depuis le superviseur
				$subject = 'Attribution de mat�riel';
				$body = str_replace("%%contenu%%", "Le mat�riel suivant vous a �t� attribu� : " . $materiels_horaires, $body);
				$body = str_replace("%%signature%%", "Veuillez vous rendre sur <a href=\"" . SITE_ADRESS . "\">" . SITE_NAME . "</a> pour valider cette demande.", $body);
				break;
				
			case 'materiel_accept': //Accepter une demande de r�servation de mat�riel
				$subject = 'Acceptation de votre demande de r�servation de mat�riel';
				$body = str_replace("%%contenu%%", "Le mat�riel suivant vous a �t� attribu� : " . $materiels_horaires, $body);
				$body = str_replace("%%signature%%", "Cordialement, l'administration.", $body);
				break;
			
			case 'materiel_reject': //Refuser une demande de r�servation de mat�riel
				$subject = 'Refus de votre demande de r�servation de mat�riel';
				$body = str_replace("%%contenu%%", "Cet e-mail indique le refus de votre demande de r�servation : " . $materiels_horaires, $body);
				$body = str_replace("%%signature%%", "Cordialement, l'administration.", $body);
				break;
				
			case 'materiel_delete': //Supprimer une demande de r�servation de mat�riel
				$subject = 'Suppression de votre demande de r�servation de mat�riel';
				$body = str_replace("%%contenu%%", "Cet e-mail indique la suppression de votre demande de r�servation : " . $materiels_horaires, $body);
				$body = str_replace("%%signature%%", "Cordialement, l'administration.", $body);
				break;
				
		}
		/*
		$headers = 'From: "' . $from . '"<' . $from . '>' . "\n";
		$headers .= 'Reply-To: ' . $from . "\n";
		$headers .= 'Content-Type: text/plain; charset="iso-8859-1"' . "\n";
		$headers .= 'Content-Transfer-Encoding: 8bit';
		*/
		$headers  = "From: " . $from . "\n";
		$headers .= "MIME-version: 1.0\n";
		$headers .= "Content-type: text/html; charset= iso-8859-1\n";
		return mail($to, $subject, $body, $headers);
	}
}
?>
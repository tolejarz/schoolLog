<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title><?php echo(SITE_NAME); ?> - charte</title>
		<link rel="stylesheet" type="text/css" href="/resource/css/style.css" />
		<script type="text/javascript" src="/resource/javascript/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="/resource/javascript/charte.js"></script>
	</head>
	<body>
		<div id="charterContainer">
			<div class="centered"><img src="/resource/img/bidon.png" /></div>
			<p class="separator2"></p>
			<p class="centered">
				Bienvenue <span class="log"><?php echo(($_SESSION['user_privileges'] == 'eleve' ? $_SESSION['user_surname'] : $_SESSION['user_civility']) . ' ' . $_SESSION['user_name']); ?></span>.
			</p>
			<p class="centered">
				Il s'agit de votre première connexion. Veuillez lire et accepter la charte d'utilisation pour pouvoir utiliser <b><?php echo(SITE_NAME); ?></b>.
			</p>
			<p class="separator2"></p>
			<form action="" method="post">
				<h2>Charte d'utilisation</h2>
				<p>
					L'usage du droit de publication devra respecter toute réglementation applicable dans ce domaine :
				</p>
				<p>
					Respect des droits d'auteurs, du régime juridique des licences publiques et de la
					législation liés aux documents écrits et audiovisuels : chaque auteur devra s'assurer
					qu'il a le droit de diffuser les documents qu'il propose.
				</p>
				<ul>
					<li>
						L'article L 122-5 du code de la propriété intellectuelle n'autorisant que les "copies ou
						reproductions strictement réservées à l'usage privé du copiste et non destinées à une
						utilisation collective" et "les analyses et les courtes citations dans un but d'exemple
						et d'illustration", toute représentation ou reproduction intégrale ou partielle faite sans
						consentement de l'auteur est interdite, les citations devront êtres courtes et leurs
						sources clairement indiquées.
					</li>
					<li>
						Respect du droit à l'image : il convient de vérifier que les images sont bien libres de
						droits ou d'obtenir une autorisation écrite du détenteur de ces droits.
					</li>
					<li>
						Conformément à l'article 34 de la loi "Informatique et Libertés" du 6 janvier 1978,
						les personnes citées disposent d'un droit d'accès, de modification, de rectification et
						de suppression des données les concernant.
					</li>
				</ul>
				<p>
					Responsabilité des utilisateurs :
				</p>
				<ul>
					<li>
						Chaque utilisateur est responsable de l'utilisation qu'il fait des moyens
						informatiques de l'EPSI ainsi que de l'ensemble des informations qu'il met à la
						disposition d'autrui.
					</li>
					<li>
						Chaque titulaire de comptes, ou d'un dispositif de contrôle d'accès, est
						responsable des opérations locales ou distantes effectuées depuis son compte
						ou sous le couvert des dispositifs de contrôle d'accès qui lui a été attribué.
					</li>
					<li>
						Chaque utilisateur reconnaît que toute violation des dispositions de la charte
						ainsi que, plus généralement, tout dommage crée à l'EPSI ou à des tiers
						l'engagera personnellement.
					</li>
				</ul>
				<p>
					Les utilisateurs ne respectant pas les règles et les obligations de cette charte sont
					également passibles d'une procédure disciplinaire inhérente à leurs statuts. Ils peuvent être
					traduits devant la section disciplinaire du conseil d'administration de l'école en ce qui
					concerne les étudiants et les enseignants et devant le conseil de discipline de leur corps
					respectif en ce qui concerne les personnels administratifs et techniques.
				</p>
				<p>
					Tout utilisateur qui contreviendrait aux règles précédemment définies peut s'exposer à
					des poursuites civiles et/ou pénales prévues par les textes en vigueur (« Article 323-1 à 323-7
					du code pénal »).
				</p>
				<h2></h2>
				<div id="validationSubmitContainer">
					<p id="charterValidationContainer">
						<input id="validation" name="validation" type="checkbox" /> <label for="validation">J'accepte la charte d'utilisation</label>
					</p>
					<p id="charterSubmitContainer">
						<input id="validationSubmit" type="submit" value="Valider" />
					</p>
					<p class="clear"></p>
				</div>
			</form>
		</div>
	</body>
</html>

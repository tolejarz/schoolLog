<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title><?php echo SITE_NAME; ?> - connexion</title>
		<link rel="stylesheet" type="text/css" href="templates/style.css" />
		<script type="text/javascript" src="lib/_js/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="lib/_js/login.js"></script>
	</head>
	<body>
		<form action="index.php" method="post">
			<div id="logboxContainer">
				<img src="templates/img/bidon.png" />
				<p id="identify">Veuillez vous identifier pour accéder à <?php echo SITE_NAME; ?>.</p>
				<?php
				if (isset($_SESSION['ERROR_MSG'])) {
					echo('<div class="errorContainer"><b>Erreur :</b> ' . $_SESSION['ERROR_MSG'] . '</div>');
					unset($_SESSION['ERROR_MSG']);
				}
				?>
				<table id="logbox">
					<tr>
						<th><label for="loginId">Identifiant :</label></th>
						<td><input id="loginId" name="login" type="text" value="<?php echo @$_POST['login']; ?>" /></td>
					</tr>
					<tr>
						<th><label for="passwordId">Mot de passe :</label></th>
						<td><input id="passwordId" name="password" type="password" /></td>
					</tr>
				</table>
				<div id="logboxSubmitContainer"><input type="submit" value="Connexion" /></div>
			</div>
		</form>
	</body>
</html>

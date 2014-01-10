<?php
/* ---------------------------------------------------------------------
Minimiseur créé par Rémi Perrot au départ, en 2013, sous license WTFPL :

         DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
                   Version 2, December 2004

Copyright (C) 2004 Sam Hocevar <sam@hocevar.net>
 
Everyone is permitted to copy and distribute verbatim or modified
copies of this license document, and changing it is allowed as long
as the name is changed.
 
           DO WHAT THE FUCK YOU WANT TO PUBLIC LICENSE
  TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
 
 0. You just DO WHAT THE FUCK YOU WANT TO.
--------------------------------------------------------------------- */


// Lancement de la session
session_start();

// On inclu la page de fonction :
require_once('fonctions.php');
// On inclu les variables :
include('bdd.php');

// Connexion à la bdd :
$db = mysql_connect($serveur, $login, $mdp);
// Selection de la table :
mysql_select_db($bdd,$db);

// Enregistrement si existant du mdp dans un cookie/session :

// La session existe, on set le cookie
if (isset($_SESSION['mdp'])) setcookie('mdp', $_SESSION['mdp'], time() + 7*24*3600, null, null, false, true);
// Le cookie existe, on set la session
if (isset($_COOKIE['mdp'])) $_SESSION['mdp']=$_COOKIE['mdp'];

// ---------------------------------------------------------------------
// On commence par vérifier s'il y a du texte après la racine du site
// En fonction, on redirige la suite vers les différents fichiers.

if (isset($_GET['lien'])) 	// S'il y a la variable lien dans l'url ..
	{						// (Normalement c'est tjs le cas, cf .htaccess)
	
		if ($_GET['lien'] != "") // L'ID existe et est =/= de ""
		{
			$ID=$_GET['lien']; // Le $lien entré (site.com/blabla) correspond à l'ID dans la bdd
			// On récupère le lien
			$sql = mysql_query("SELECT Lien FROM Liens WHERE ID='".$ID."'") or die('Erreur SQL :'.mysql_error());
			while($data = mysql_fetch_assoc($sql)) {$lien = $data['Lien'];}
			// Normalement il n'y a qu'une correspondance dans la bdd
			// S'il y en a plusieurs, c'est le dernier que nous obtiendrons
			if ($lien != "" && $lien != "null")
			{ // Le lien obtenu est ni vide ni égale à "null" (cf après)
// ----------------
?><!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="iso-8859-1">
		<title>Redirection en cours</title>
		<meta http-equiv="refresh" content="0; URL=<?php echo $lien; ?>">
	</head>
	<body>
		<h1>Redirection vers <a href="<?php echo $lien; ?>"><?php echo $lien; ?></a> ...</h1>
	</body>
</html><?php
// ----------------	
				die();
			}
			else if ($lien == "null") 	// Si le lien est = null c'est un fichier
			{							// directement sur le serveur
				
				//On va faire un test pour savoir de quelle extension il s'agit
				//Seules les extensions enregistrées fonctionneront
				if (strrchr($ID,'.') == ".pdf")
				{
					header('Content-type: application/pdf');
					header('Content-Disposition: attachment; filename="'.$ID.'"');
					readfile($ID);
					die();
				}
			}
			// Si on arrive au "sinon", c'est qu'aucun lien n'a été trouvé pour l'ID donné
			else die('<h1>Lien non contenu dans la database</h1>');
		}
		
		// Si $lien == "", c'est qu'on est à l'accueil (site.com/)
		else
		{
// ----------------
?><!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="iso-8859-1">
    <title>Minimiseur d'url</title>
</head>

<body>
<?php include('ajout.php');?>
</body>
</html><?php
// ----------------
		}

	}// On a pas de variable $lien en GET ...
else die('<h1>Problème de redirection d\'url (vérifiez le .htaccess)...</h1>');
// ---------------------------------------------------------------------
?>

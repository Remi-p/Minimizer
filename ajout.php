<h1>Ajout</h1>

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


// Si les variables ont été transmises en mode POST, on les insère/enregistre

if (isset($_POST['ID']))
{
	// À la première utilisation on a le chps de mot de passe
	// qu'il nous faut donc enregistrer
	if ($_POST['mdp']==$connexion)
	{
		$_SESSION['mdp'] = $_POST['mdp'];
	}
			
	// Variable de test de bon fonctionnement
	// Si elle est à false quand il s'agit d'enregistrer le lien
	// c'est qu'une des conditions de test n'est pas remplie
	$test=true;
	
	$lienpost = $_POST['Lien'];
			
	// Vérifications liées au mdp --------------------------------------
	if (!isset($_SESSION['mdp']) || !$_SESSION['mdp']==$connexion)
	{ // Si la variable de session mdp n'existe pas ou est fausse ..
		echo '<h2>Ennui de mot de passe ..</h2>';
		$test=false;
	}
	
	if (isset($_COOKIE['mdp']) && !$_COOKIE['mdp']=='ed')
	{ // Si la variable de cookie existe mais est fausse ..
		echo '<h2>Mauvais mot de passe !</h2>';
		$test=false;
	}
	// -----------------------------------------------------------------
	
	// -----------------------------------------------------------------
	/* À présent, et pour chaque vérification, on inclue dans la
	 * condition un test pour savoir si $test est bien à true.
	 * Si ce n'est pas le cas, c'est qu'il y a déjà eu une erreur de
	 * rencontrée : avec cette méthode on aura donc toujours qu'une
	 * erreur d'affichée.											*/
	// -----------------------------------------------------------------
			
	if ($lienpost != "null") 	// Si la valeur du lien entré est "null",
		{						// il ne sert à rien de le vérifier
								// puisque c'est pr accéder à un fichier
								// du serveur

			if ($lienpost == '' && $test) {echo '<h2>Lien vide ! -_-</h2>'; $test=false;}
			
			// Lorsque l'url envoyé ne contient pas d'http ou d'https ..
			if (parse_url($lienpost, PHP_URL_SCHEME) != 'http' &&
				parse_url($lienpost, PHP_URL_SCHEME) != 'https') 
				$lienpost = 'http://'.$lienpost;
			// On le rajoute ! (ce qui permet de mettre en lien une
			// adresse du type monsite.com/blabla
				
			// On vérifie qu'il y a bien un point qlq part
			if (substr_count($lienpost, '.')==0 && $test) {echo '<h2>Il n\'y a pas de point o.O</h2>'; $test=false;}
			
			// Une fois que c'est fait on peut utiliser cette fonction
			// de PHP qui vérifie que l'url est correcte
			if (!filter_var($lienpost, FILTER_VALIDATE_URL) && $test) {echo '<h2>URL invalide .. \:</h2>'; $test=false;}
			
			// Malheureusement l'utilisation de la fonction prévue à cet effet,
			// filter_var, semble ne pas fonctionner au niveau de la reconnaissance
			// de .com et autre extension ..
			
			// Du coup on fonctionne comme suit : on vérifie que ce
			// qui suit le point est entre 2 et 6 caractères
			$extension = explode('.', parse_url($lienpost, PHP_URL_HOST));
			if (
				(
				(strlen($extension[count($extension)-1]))<2 
				|| strlen($extension[count($extension)-1])>6
				)  
				&& $test)
			{echo "<h2>Extension invalide .. :/</h2>"; $test=false;}
			
			// Juste pour vérifier qu'on ne fait pas un lien vers nous-même
			// (histoire d'éviter les boucles par exemple)
			if (parse_url($lienpost, PHP_URL_HOST) == $_SERVER['SERVER_NAME'] && $test) {echo '<h2>On m\'arnaque pas si facilement 8)</h2>'; $test=false;}

			// ---------------------------------------------------------
			//On vérifie également si le lien existe déjà ..
			$testExistence = mysql_query("SELECT * FROM Liens WHERE Lien = '".$lienpost."'");
			if ($test && mysql_num_rows($testExistence)>0)
			{
				while($data = mysql_fetch_assoc($testExistence)) {$actuel = $data['ID'];}
				$lien = 'http://'.$_SERVER['SERVER_NAME'].'/'.$actuel;
				echo "<h2>Lien existant : <a href=\"$lien\">$lien</a></h2>";
				$test=false;
			}
			// ---------------------------------------------------------
			
		} // Fin de |if ($lienpost != "null")|
	
	// Ici on est dans le cas ou on a entré une ID manuellement
	if ($test && $_POST['ID']!='')
	{
		// Vérification de l'existence ou non de l'ID dans notre bdd
		$testExistence = mysql_query("SELECT * FROM Liens WHERE ID = '".$_POST['ID']."'");
		
		if (mysql_num_rows($testExistence)>0)
		{
			echo '<h2>ID existant</h2>';
			$test=false;
		}
		
		// On vérifie qu'il y a au moins un tiret si l'ID est rentré,
		// (c-a-d qu'il y a un caractère spécial), pour que l'ID ne
		// puisse pas être un jour générée par le script
		if ($test && substr_count(cleanChaine($_POST['ID']), '-'))
		{
			echo '<h2>Il n\'y a aucun caractère spécial dans votre ID.</h2>';
			$test=false;
		}
		
	}

	// Si tout les tests ont été réussis avec brio ! =>
	if ($test)
	{
			
		// Lorsque l'on ne spécifie pas d'ID ..
		if ($_POST['ID'] == '' && $lienpost != "null")
		{
			
			// Selection du numéro du dernier lien
			$sql = mysql_query('SELECT Lien FROM Liens LIMIT 0,1') or die('Erreur SQL :'.mysql_error());
			while($data = mysql_fetch_assoc($sql)) {$actuel = $data['Lien'];}
			
			// On appelle la fonction qui incrémente ce compteur hexa.
			$nouveau=plusun($actuel);
			
			// On ajoute le lien dans la bdd
			mysql_query("INSERT INTO Liens
					VALUES('$nouveau', '$lienpost')") or die('Erreur SQL :'.mysql_error());
					
			// On modifie le compteur pour correspondre au nouveau
			mysql_query("UPDATE Liens SET Lien='$nouveau'
					WHERE ID='0'") or die('Erreur SQL :'.mysql_error());
			
			$lien='http://'.$_SERVER['SERVER_NAME'].'/'.$nouveau;
			echo '<h2>Le lien : <a href="'.$lien.'">'.$lien.'</a></h2>';
		}
		// Et lorsqu'on le fait
		// (que la valeur du lien soit "null" ou normal
		else 
		{
			// On nettoie la chaine passée en paramètre
			$nouveau = cleanChaine($_POST['ID']);
			
			// On ajoute le lien dans la bdd
			mysql_query("INSERT INTO Liens
					VALUES('$nouveau', '$lienpost')") or die('Erreur SQL :'.mysql_error());
			
			// Le lien est ..
			$lien='http://'.$_SERVER['SERVER_NAME'].'/'.$nouveau;
			echo '<h2>Le lien : <a href="'.$lien.'">'.$lien.'</a></h2>';
		}
	}
}
?>

<form method="post" action="/"> <!-- Formulaire d'envoi -->
<?php
// On commence par regarder si le mot de passe de session à déjà été entré.
// Si ce n'est pas le cas, on rajoute au formulaire un champs prévu à cet effet
if (!isset($_SESSION['mdp'])) echo '<input type="password" name="mdp"/>Mdp* <br/>';
?>
<input type="text" name="Lien" value="<?php if (isset($lien)) echo $lien;?>"/>Lien* <br/>
<input type="text" name="ID" value=""/>ID <br/>
<input type="submit" value="Minimise moi !"/>
</form>

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
 
function cleanChaine($String) {
// Fonction de nettoyage de chaine de caractère
	
     $Search = array("\\n", "\\r", "\n", "\r", "/", "     ", "    ", "&amp;", " ", "        ", "       ", "      ", "     ", "    ", "   ", "  ", "à", "á", "â", "à", "À", "ç", "ç", "Ç", "é", "è", "ê", "ë", "É", "È", "é", "è", "ê", "í", "ï", "ï", "î", "ñ", "ô", "ò", "ö", "ô", "ó", "Ó", "ù", "û", ";");
     $Replace = array("-" ,"-"   ,"-"  ,"-"  , "-", "-"    , ""    , "-"    , "-", "-"       , "-"      , "-"     , "-"    , "-"   , "-"  , "-" , "a", "a", "a", "a", "A", "c", "c", "C", "e", "e", "e", "e", "E", "E", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "O", "u", "u", "\;");
     $String = str_replace($Search, $Replace, $String);
     $String = str_replace("'", "-", $String);
     $String = str_replace("\\", "-", $String);
     return $String;
}

function toDec($n)
{
	// Donne l'équivalent en décimal suivant le schéma suivant :
	
	// 0..9 = 10, a..Z = 26, A..Z = 26
	// = 62 possibilités pour un caractère
	// c-a-d de 0 à Z se convertis en 0 à 61
	
	// Il existe surement une méthode plus simple que celle ci ..
	$Recherche = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 
	'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 
	'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 
	'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 
	'W', 'X', 'Y', 'Z');
    $Remplace = array('10', '11', '12', '13', '14', '15', '16', '17', '18', '19', 
	'20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', 
	'33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', 
	'46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', 
	'59', '60', '61');
	$n = str_replace($Recherche, $Remplace, $n);
	return $n;
}

function toHex($n)
{
	// Donne l'équivalent en non-décimal suivant le schéma suivant :
	
	// 0..9 = 10, a..Z = 26, A..Z = 26
	// = 62 possibilités pour un caractère
	// c-a-d de 0 à Z se convertis en 0 à 61
	
	$Remplace = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 
	'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 
	'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 
	'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 
	'W', 'X', 'Y', 'Z');
    $Recherche = array('10', '11', '12', '13', '14', '15', '16', '17', '18', '19', 
	'20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', 
	'33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', 
	'46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', 
	'59', '60', '61');
	$n = str_replace($Recherche, $Remplace, $n);
	return $n;
}

function plusun($val)
{
	// Fonction d'ajout sur un compteur lettres-chiffres

	// Il faut compter le nombre de caractères de la valeur en paramètre pour la suite.
	// Pour ce faire, la fonction : strlen.
	$nb=strlen($val);

	// On commence par convertir les nombres en décimal
	// $z sera le résultat de cette conversion
	$z = 0;
	for ($n = 0; $n < $nb; $n++)
	{
		// $string[n] est le n-ième caractère de la chaine string
		// On commence par la valeur avec le plus de poids,
		// donc on utilise $nb - $n
		$z = $z + (toDec($val[$n]))*(pow(62,($nb-($n+1))));
	}
	
	// On a à présent la version décimal de $val. On l'incrémente ..
	$val='';
	$z++;
	
	// Il faut convertir dans le sens inverse cette fois.
	
	// Désormais on va effectuer des divisions euclidiennes sur 
	// toutes les puissances de 62. Si la première est égale à 0 on
	// ne la compte pas.
	
	// On part de $nb et on va jusqu'à 0 (pour 62^0 = 1)
	$NB=$nb;
	// Initialisation en chaine de caractère pour la variable de retour
	$return='';
	
	for ($n = 0; $n <= $NB; $n++)
	{
		if (($z / (pow(62,$nb))) >= 1)
		{
			$val=toHex(floor($z/(pow(62,$nb))));
			$return=$return.$val;
			$z=$z%(pow(62,$nb));
		}
		else 
		{
			if ($nb != 0)
			{
				if ($nb == $NB) $val = '';
				else $val = '0';
				$return=$return.$val;
			}
			else 
			{
				$val = toHex($z%(pow(62,$nb)));
				$return=$return.$val;
			}
		}
		$nb--;
	}
	
	// On a donc à présent un nombre dans notre format
	return $return;
}
?>

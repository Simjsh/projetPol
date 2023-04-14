<?php 
	function executeC($ligne) {		// Connexion à la base de données et éxécution de la requête $ligne
		$db = new PDO(
		'mysql:host=localhost;dbname=projet;charset=utf8',		
		'root', '');		// root correspond a l'identifiant pour se connecter à PHPMyAdmin et '' le mot de passe (vide)
		// projet correspond au nom de la base de donnée dans PHPMyAdmin. 
		//   /!\ ATTENTION : Il faut vérifier que le nom de la base, l'identifiant et le mot de passe soit correct (Dans le fichier projet.php aussi) /!\
		$commande = $db->prepare($ligne);
		$commande->execute();
		return $commande->fetchAll();		// Renvoi le résultat de la requête
	}

	$lDom = executeC('Select * from domaines');		// Liste de tous les domaines de formations
	$lEco = executeC('Select * from ecoles Order By nomE');		// Liste de toutes les écoles
	$lType = executeC('Select * from typeformation');		// Liste de tous les types de formations


	$lVilleS = [];		// Liste des villes ayant été sélectionnées par l'utilisateur

	foreach($lEco as $i) {		// Pour chaque écoles
		if (isset($_POST["ecole".$i[0]])) {		// On vérifie que l'école a été cochée par l'utilisateur en vérifiant qu'elle est dans la variable POST
			array_push($lVilleS, [$i[0], $i[1], $i[2]]);	// Dans ce cas là, on l'ajoute à la liste des villes
		}
	};
	if ($lVilleS == []) {	// Si cette liste est vide, alors l'utilisateur n'en a choisi aucune
		$lVilleS = $lEco;	// Dans ce cas là, on décide d'ajouter toutes les villes à la liste pour ne pas avoir aucun résultat
	} 

	$ligneType = "";	// Morceau de requête qui sera ajouté à la requête générale et qui ajoutera une condition sur le type
	$ligneDomaine = "";		// Même chose mais pour le domaine

	foreach($lType as $i) {		// Pour chaque Type de formations :
		if (isset($_POST[$i[2]])) {		// S'il a été coché
			$ligneType = $ligneType."tf.nomT = '".$i[2]."' OR "; // On l'ajoute à la ligne. Au final, cette ligne sera de la forme "type1 OR type2 OR"
		}
	};
	if ($ligneType == "") {	// Si aucun type de formation n'a été coché :
		$ligneType = "1   ";	// On fait comme s'ils étaient tous cochés. On écrit "1" pour que la condition soit toujours vraie
	};

	foreach($lDom as $i) {		// Les 7 prochaines lignes correspondent à la même étape, mais pour les domaines de formation
		if (isset($_POST["dom".$i[0]])) {
			$ligneDomaine = $ligneDomaine."d.idDomaine = ".$i[0]." OR ";
		}
	};
	if ($ligneDomaine == "") {
		$ligneDomaine = "1   ";
	};

	foreach($lVilleS as $ville) {	// Pour chaque ville séléctionnée, on récupère toutes les spécialités correspondant au critères choisis
		$listeSpe = executeC( 	// On récupère ces spécialités dans la liste $listeSpe
		"Select DISTINCT s.idSpecialite, nomS, lienSpecialite from specialites as s 

		JOIN Specialitetype as st ON st.idSpecialite = s.idSpecialite 
		JOIN typeformation as tf ON tf.idtype = st.idtype and (".substr($ligneType,0,-3).") 

		JOIN specialitedomaine as sd ON sd.idspecialite = s.idspecialite 
		JOIN domaines as d ON d.idDomaine = sd.idDomaine and (".substr($ligneDomaine,0,-3).")

		Where s.idEcole = ".$ville[0]."
		Order By nomS
		");

		// Les $ligneDomaine / $ligneType finissent soit par ' OR' soit par '   '. On enlève donc les 3 derniers caractères avec la fonction 'substr' pour ne pas avoir d'erreurs lors de l'éxécution de la requête

		if (isset($listeSpe[0])) {	// On vérifie que la liste des spécialités n'est pas vide. Dans ce cas là, on envoi une table en HTML affichant la liste des spécialités

			echo "<h1><a target='blank' href='".$ville[2]."'>".$ville[1]."</a></h1>";	// $ville[2] -> lien de l'école		$ville[1] -> son nom 

			echo "
			<table>
				<thead><tr>
					<th>Spécialités</th><th>Formation</th><th>Domaine</th>
				</tr></thead>
				<tbody>";

			foreach($listeSpe as $spe) {
				$listeDomSpe = executeC(	// Pour chaque spécialités, on récupère d'abord ses domaines de formation, qu'on ajoute dans une liste
				"Select d.idDomaine, d.nomD, d.imgD
				from domaines as d
				Join specialiteDomaine as sd On sd.idDomaine = d.idDomaine
				Where sd.idSpecialite = ".$spe[0]);		// $spe[0] correspond à l'identifiant de la spécialité

				$listeTypeSpe = executeC(	// On récupère ensuite ses types de formation, qu'on ajoute dans une seconde liste
				"Select t.idType, t.nomT, t.imgT, st.infoC, description
				from typeformation as t
				Join specialitetype as st On st.idType = t.idType
				Where st.idSpecialite = ".$spe[0]);		// $spe[0] -> identidiant

				echo "<tr class='resultat'>
					<td>
						<a target='blank' href='".$spe[2]."' class='nomSpe'>".$spe[1]."</a>
					</td>
					<td>";	// $spe[2] -> lien de la spécialité 		$spe[1] -> son nom

				foreach($listeTypeSpe as $type) {		// On affiche le logo pour chaque type de cette spécialité
					echo "<img src='".$type[2]."' alt='Logo type de formation' title='".$type[4]."'>";	// $type[2] -> lien du logo	   $type[4] -> Sa description (Alternant, Étudiant)
					if (!($type[3] == "")) {	// Si il y a une information complémentaire, on l'affiche dans un paragraphe
						echo "<p class='infoC'>".$type[3]."</p>";	// $type[3] correspond à cette information complémentaire
					};
				};
						
				echo "</td><td>";

				foreach($listeDomSpe as $dom){		// Pour chaque domaine de cette spécialité, on affiche le logo
					echo "<img src='".$dom[2]."' alt='Logo domaine' title='".$dom[1]."'>";	// $dom[2] est le logo	  $dom[1] est son nom
				};

				echo "</td></tr>";
			};

			echo "</tbody></table>";

		}
	};

?>
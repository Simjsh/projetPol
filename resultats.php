<?php 
	function executeC($ligne) {
		$db = new PDO(
		'mysql:host=localhost;dbname=projet;charset=utf8',
		'root', '');
		$commande = $db->prepare($ligne);
		$commande->execute();
		return $commande->fetchAll();
	}

	$lDom = executeC('Select * from domaines');
	$lEco = executeC('Select * from ecoles Order By nomE');
	$lType = executeC('Select * from typeformation');

	function testCheck($nom) {
		if (isset($_POST[$nom])) { echo "checked";}};


	$lVilleS = [];

	foreach($lEco as $i) {
		if (isset($_POST["ecole".$i[0]])) {
			array_push($lVilleS, [$i[0], $i[1], $i[2]]);
		}
	};
	if ($lVilleS == []) {
		$lVilleS = $lEco;
	} 

	$ligneType = "";
	$ligneDomaine = "";	

	foreach($lType as $i) {
		if (isset($_POST[$i[2]])) {
			$ligneType = $ligneType."tf.nomT = '".$i[2]."' OR ";
		}
	};
	if ($ligneType == "") {
		$ligneType = "1   ";
	};

	foreach($lDom as $i) {
		if (isset($_POST["dom".$i[0]])) {
			$ligneDomaine = $ligneDomaine."d.idDomaine = ".$i[0]." OR ";
		}
	};
	if ($ligneDomaine == "") {
		$ligneDomaine = "1   ";
	};

	foreach($lVilleS as $ville) {
		$listeSpe = executeC( 
		"Select DISTINCT s.idSpecialite, nomS, lienSpecialite from specialites as s 

		JOIN Specialitetype as st ON st.idSpecialite = s.idSpecialite 
		JOIN typeformation as tf ON tf.idtype = st.idtype and (".substr($ligneType,0,-3).") 

		JOIN specialitedomaine as sd ON sd.idspecialite = s.idspecialite 
		JOIN domaines as d ON d.idDomaine = sd.idDomaine and (".substr($ligneDomaine,0,-3).")

		Where s.idEcole = ".$ville[0]."
		Order By nomS
		");

		if (isset($listeSpe[0])) {

			echo "<h1><a target='blank' href='".$ville[2]."'>".$ville[1]."</a></h1>";

			echo "
			<table>
				<thead><tr>
					<th>Spécialités</th><th>Formation</th><th>Domaine</th>
				</tr></thead>
				<tbody>";

			foreach($listeSpe as $spe) {
				$listeDomSpe = executeC(
				"Select d.idDomaine, d.nomD, d.imgD
				from domaines as d
				Join specialiteDomaine as sd On sd.idDomaine = d.idDomaine
				Where sd.idSpecialite = ".$spe[0]);

				$listeTypeSpe = executeC(
				"Select t.idType, t.nomT, t.imgT, st.infoC, description
				from typeformation as t
				Join specialitetype as st On st.idType = t.idType
				Where st.idSpecialite = ".$spe[0]);

				echo "<tr class='resultat'>
					<td>
						<a target='blank' href='".$spe[2]."' class='nomSpe'>".$spe[1]."</a>
					</td>
					<td>";

				foreach($listeTypeSpe as $type) {
					echo "<img src='".$type[2]."' alt='Logo type de formation' title='".$type[4]."'>";
					if (!($type[3] == "")) {
						echo "<p class='infoC'>".$type[3]."</p>";
					};
				};
						
				echo "</td><td>";

				foreach($listeDomSpe as $dom){
					echo "<img src='".$dom[2]."' alt='Logo domaine' title='".$dom[1]."'>";
				};

				echo "</td></tr>";
			};

			echo "</tbody></table>";

		}
	};

?>
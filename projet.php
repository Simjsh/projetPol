<?php 
	function executeC($ligne) {		// Connexion à la base de données et éxécution de la requête $ligne
		$db = new PDO(
		'mysql:host=localhost;dbname=projet;charset=utf8',		
		'root', '');		// root correspond a l'identifiant pour se connecter à PHPMyAdmin et '' le mot de passe (vide)
		// projet correspond au nom de la base de donnée dans PHPMyAdmin. 
		//   /!\ ATTENTION : Il faut vérifier que le nom de la base, l'identifiant et le mot de passe soit correct (Dans le fichier resultats.php aussi) /!\
		$commande = $db->prepare($ligne);
		$commande->execute();
		return $commande->fetchAll();		// Renvoi le résultat de la requête
	}

	$lDom = executeC('Select * from domaines');		// Liste de tous les domaines de formations
	$lEco = executeC('Select * from ecoles Order By nomE');		// Liste de toutes les écoles
	$lType = executeC('Select * from typeformation');		// Liste de tous les types de formations

?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="Description" content="Rechercher une spécialité à Polytech">
	<title>Rechercher une spécialité à Polytech</title>
	<link rel="shortcut icon" href="img/logoPolytech.png" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="style/style.css">
	<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>	<!-- Ajout de JQuery pour le code JavaScript -->
	<script src="script/script.js"></script>

</head>
<body>
	<form onchange="affResult(this)">	<!-- Quand le formulaire change, il appel une fonction qui affiche les résultats dans la partie de droite -->
		<header>
			<a href="https://www.polytech-reseau.org/" target="blank"><img src="img/logo2.png" alt="logo de Polytech"></a>

			<div id="choixTD">
				<nav>
					<p class="titreF">Type de formation▼</p>
					
					<fieldset id="field1">	<!-- Première liste déroulante, affichant les types de formation -->
						<label for="tout">
							<input type="checkbox" id="tout" name="tout0" onchange="toutSelect(this, 1)">
							Tout sélectionner
						</label>

						<?php foreach ($lType as $i) :	// Pour chaque Type de formation, on ajoute un input dans le formulaire?>	
						<label for="type<?php echo $i[0]	// $i[0] correspond à l'id du type ?>">
							<input type="checkbox" id="type<?php echo $i[0]?>" name="<?php echo $i[2]	// $i[2] donne le nom (FISA, FISE)?>">
							<?php echo $i[1]	// $i[1] donne la description (Alternance, Étudiant)?>
							<img src="<?php echo $i[3]	// $i[3] donne le logo du type de formation?>" alt="Type <?php echo $i[2]?>">
						</label>
						<?php endforeach ?>
					</fieldset>
				</nav>

				<nav>
					<p class="titreF">Domaine de formation▼</p>

					<fieldset id="field2">	<!-- Deuxième liste déroulante, affichant les domaines de formation -->
						<label for="in0">
							<input type="checkbox" id="in0" name="tout1" onchange="toutSelect(this, 2)">
							Tout sélectionner
						</label>

						<?php foreach ($lDom as $i) : 	// Pour chaque domaines, on ajoute un input dans la liste déroulante?>
						<label for="in<?php echo $i[0] 	// $i[0] donne l'id du domaine de formation?>">
							<input type="checkbox" id="in<?php echo $i[0] ?>" name="dom<?php echo $i[0] ?>">
							<?php echo $i[1] 	// $i[1] donne le nom?>
							<img src="<?php echo $i[2] 		// $i[2] donne le logo?>" alt="Domaine <?php echo $i[1] ?>">
						</label>
						<?php endforeach ?>							
					</fieldset>
				</nav>
			</div>
		</header>

		<section id="choixEcoles">	<!-- Carte de la France avec un input pour chaque école -->
			<div id="zone">
				<img id="carte" src="img/carte.png" alt="Carte des écoles Polytech">

				<?php foreach ($lEco as $i) : 	// Pour chaque écoles, on ajoute un logo Polytech et un input qu'on place sur la carte?>	
					<input type="checkbox" id="ecole<?php echo $i[0] 	// $i[0] donne l'id de l'école?>" name="ecole<?php echo $i[0] ?>">
					<p class="positionVille" id="lienE<?php echo $i[0] ?>" style="left: <?php echo $i[4]	// $i[4] -> Décalage par rapport à la gauche ?>%; top: <?php echo $i[3]	// $i[3] -> Décalage par rapport au haut ?>%;">
						<label for="ecole<?php echo $i[0] ?>"><img src="img/logo3.png" alt="logo Polytech"></label>
					</p>
					
					<h2 class="nomVille" style="top:<?php echo $i[3] ?>%; left:<?php echo $i[4] ?>%;"><?php echo $i[1] 	// $i[1] -> nom de la ville?></h2>
				<?php endforeach ?>
			</div>			
		</section>
	</form>

	<div id="resultats">		<!-- Partie de droite, où la liste des spécialités sera ajoutées -->
	</div>
</body>
</html>
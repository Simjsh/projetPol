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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="Description" content="Rechercher une spécialité à Polytech">
	<title>Rechercher une spécialité à Polytech</title>
	<link rel="shortcut icon" href="img/logoPolytech.png" type="image/x-icon">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
	<script src="script/script.js"></script>

</head>
<body>
	<form onchange="affResult(this)">
		<header>
			<a href="https://www.polytech-reseau.org/" target="blank"><img src="img/logo2.png" alt="logo de Polytech"></a>

			<div id="choixTD">
				<nav>
					<p class="titreF">Type de formation▼</p>
					
					<fieldset id="field1">
						<label for="tout">
							<input type="checkbox" id="tout" name="tout0" onchange="toutSelect(this, 1)" <?php  testCheck("tout0")?>>
							Tout sélectionner
						</label>

						<?php foreach ($lType as $i) :?>
						<label for="type<?php echo $i[0]?>">
							<input type="checkbox" id="type<?php echo $i[0]?>" name="<?php echo $i[2]?>" <?php testCheck($i[2]) ?>>
							<?php echo $i[1]?>
							<img src="<?php echo $i[3]?>" alt="Type <?php echo $i[2]?>">
						</label>
						<?php endforeach ?>
					</fieldset>
				</nav>

				<nav>
					<p class="titreF">Domaine de formation▼</p>

					<fieldset id="field2">
						<label for="in0">
							<input type="checkbox" id="in0" name="tout1" onchange="toutSelect(this, 2)" <?php  testCheck("tout1")?>>
							Tout sélectionner
						</label>

						<?php foreach ($lDom as $i) : ?>
						<label for="in<?php echo $i[0] ?>">
							<input type="checkbox" id="in<?php echo $i[0] ?>" name="dom<?php echo $i[0] ?>" <?php testCheck("dom".$i[0]) ?>>
							<?php echo $i[1] ?>
							<img src="<?php echo $i[2] ?>" alt="Domaine <?php echo $i[1] ?>">
						</label>
						<?php endforeach ?>							
					</fieldset>
				</nav>
			</div>
		</header>

		<section id="choixEcoles">	
			<div id="zone">
				<img id="carte" src="img/carte.png" alt="Carte des écoles Polytech">

				<?php foreach ($lEco as $i) : ?>
					<input type="checkbox" id="ecole<?php echo $i[0] ?>" name="ecole<?php echo $i[0] ?>"  <?php testCheck("ecole".$i[0]) ?>>
					<p class="positionVille" id="lienE<?php echo $i[0] ?>" style="left: <?php echo $i[4] ?>%; top: <?php echo $i[3] ?>%;">
					<label for="ecole<?php echo $i[0] ?>"><img src="img/logo3.png" alt="logo Polytech"></label></p>
					
					<h2 class="nomVille" style="top:<?php echo $i[3] ?>%; left:<?php echo $i[4] ?>%;"><?php echo $i[1] ?></h2>
				<?php endforeach ?>
			</div>			
		</section>
	</form>

	<div id="resultats">
	</div>
</body>
</html>
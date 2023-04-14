function toutSelect(e, num) {
	$("#field"+num+" input").prop("checked", e.checked);  // Coche / Décoche toutes les checkboxs d'une liste déroulante 
};

function affResult() {
	$.post('resultats.php', $('form').serialize(), function(data) {  // Envoi les données du formulaire par la méthode POST au fichier secondaire et récupère une variable "data"
  	$('#resultats').html(data).css('width', (data==="") ? '0%':'35%');   // Actualise les tables pour afficher la liste des spécialités et disparait si elle est vide
  	$('form').css('width', (data=="") ? '100%':'65%');     // La partie de gauche prend toute la longueur si la liste est vide
  })
  .fail(function(xhr, status, error) {
  	alert('Une erreur s\'est produite\n'+xhr+'\n'+status+'\n'+error); // Un message s'affiche s'il y a un problème

  });
};
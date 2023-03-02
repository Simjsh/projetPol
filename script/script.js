function toutSelect(e, num) {
	$("#field"+num+" input").prop("checked", e.checked);
};

function affResult() {
	$.post('resultats.php', $('form').serialize(), function(data) {
  	var $div = $('#resultats').html(data).css('width', (data==="") ? '0%':'35%');
  	$('form').css('width', (data=="") ? '100%':'65%');
  })
  .fail(function(xhr, status, error) {
  	alert('Une erreur s\'est produite');
  });
};
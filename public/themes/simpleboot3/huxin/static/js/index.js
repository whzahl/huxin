function toggleVisibility(newSection) {
	$(".section").not("#" + newSection).hide();
	$("#" + newSection).show();
}
var $links = $('a');
$links.click(function(){
	$links.removeClass('selected');
	$(this).addClass('selected');
	var identify= $(this).attr("data-identify");
	if ( identify == 1 ) {
		$('#navActiveBG').css ('transform', 'translateX(0%)');
	}
	if (identify == 2) {
		$('#navActiveBG').css ('transform', 'translateX(100%)')
	}
	if ( identify == 3) {
		$('#navActiveBG').css ('transform', 'translateX(200%)')
	}
	if ( identify == 4) {
		$('#navActiveBG').css ('transform', 'translateX(300%)')
	}
	if ( identify == 5) {
		$('#navActiveBG').css ('transform', 'translateX(400%)')
	}
});
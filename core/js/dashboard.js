$(function(){
	$("input[name=invoice]").click(function(){
		$("#view").html('<iframe id="frame" src="https://quicure.com/invoice/frameless/' + $(this).val() + '" width="100%" frameborder="0" scrolling="no">');
		$("iframe").iframeAutoHeight();
	});
});
function showLoader()
{
	$('.loader').css("display","block");
}

function showEditor()
{
	
	 if($('.zeArt').val()=="1")
	 {
		$('.editorhide').show();
	 }
	 else
	 $('.editorhide').hide();
}

function jumpid(id)
{
	$(document).ready(function(){
	 $('html, body').animate({
        scrollTop: $("#"+id).offset().top-500
    }, 500);
	$("#"+id).css("background-color","#dedede");
});
}


function closeFancyReload()
{
	$.fancybox.close();
	this.parent.location = "index.php";
}

$(document).ready(function(){

$('.loader').css("display","none");
$('.fancybox').fancybox({
	  'scrolling'     : 'no',
	  'type' : 'iframe',
	  'width' : '600px',
	  'height' : 'auto',
	  });

$('.fancyboxreload').fancybox({
	  'scrolling'     : 'no',
	  'type' : 'iframe',
	  'width' : '600px',
	  'height' : 'auto',
	  'afterClose':function(){
	 	window.location = window.location.href;
	  }
	  });

$('.fancyboxfullscreen').fancybox({
	  'scrolling'     : 'no',
	  'type' : 'iframe',
	  'width' : '1200px',
	  'height' : '768px',
	  'autoDimensions':false,
	  'autoSize':false
	  });
	  
    $(".datepicker").click(function() {
        $(this).datepicker({
			dateFormat:'dd.mm.yy',
			yearRange : '-100:-18',
			changeMonth: true,
     		changeYear: true,
			maxDate:'-00D'
			}).datepicker("show")
    });



$(".datepickerzahlung").click(function() {
        $(this).datepicker({
			dateFormat:'dd.mm.yy',
			changeMonth: true,
     		changeYear: true,
			}).datepicker("show")
    });
$('.maskednumber').mask('000000000000000.00', {reverse: true});

$( '.autosubmitnumber' ).change(function() {
	
	var regex  = /^\d+(?:\.\d{0,2})$/;
	if (regex.test($(this).val()))
	{
		$(this).closest('form').submit();
	}
	else
	{
		alert("Falsches Format erkannt. z.B 10.99 ");
		return;
	}
});
$("#dropdown #clicknew").click(function(){
window.open($(this).attr("href"));
 return false;
});
$("#dropdown #click").click(function(){
showLoader();
window.location=$(this).attr("href"); return false;
});

$('.checkall').change(function() {
   if($(this).is(":checked")) {
  $("input[type=checkbox]").prop("checked",true);
      return;
   }
   $("input[type=checkbox]").prop("checked",false);
   //'unchecked' event code
});

$('form').submit(function()
{
	showLoader();
});


});
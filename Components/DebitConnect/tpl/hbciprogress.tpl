{if $doMatching}
<div style='height:auto;width:auto;'>
<div align="center"><strong>Bitte warten Sie bis alle Umsätze zugeordnet wurden</strong> </div>
<strong>Fortschritt:</strong> 
<div id="progressbar"></div>
<div align="center"><i id='currentprocess'>0</i><i>/{$synccounter} Umsätze bearbeitet</i> ( {$ordercounter} Bestellungen ) <i id='secondscounter'>0</i></div>	
</div>
<script>	
var maxConcurrentRequests = 1;
var concurrentRequests = 0;
var currentSourceIndex = 0;
var maxOperations = {$synccounter};
var counter = 0;


$(document).ready(function() {
	var myInterval = setInterval(function () {
  ++counter;
  $( "#secondscounter" ).html(counter);
}, 1000);
 doSync({$synccounter})
 
});

function syncVorgang(val)
{
	try
	{
		
			var API = "VOPDebitConnect?ajaxmatching="+val;
			var rsapi = $.getJSON( API, function(json_data) {
				 $( "#progressbar" ).progressbar({
					   value:  $( "#progressbar" ).progressbar("value")+1
				});
			
				if(json_data.done != true){
					console.log("Matching offline");
				}
				var currentvalue = Number( $( "#currentprocess" ).html());
				$( "#currentprocess" ).html(currentvalue+1);
				concurrentRequests--;
				
				
				if(currentvalue+1==maxOperations)
				{
					parent.showLoader();
					parent.$.fancybox.close();
				}
			
				getTask();
				
			});
	}catch(Exception)
	{
		concurrentRequests--;
		getTask();
	}
}

function getTask()
{
	while(currentSourceIndex < maxOperations && concurrentRequests < maxConcurrentRequests){
        currentSourceIndex++;
        concurrentRequests++;
        syncVorgang(currentSourceIndex);
    }
	
	
	
}

function doSync(counterg){
	
		$( "#progressbar" ).progressbar({
 		 max: counterg
		});	
		getTask();
		};

</script>

{literal}
<style type="text/css">
.ui-progressbar {
  height: 2em;
  text-align: left;
  overflow: hidden;
}
.ui-progressbar .ui-progressbar-value {
  margin: -1px;
  height: 100%;
  background-color: #9F3;
}</style>
{/literal}
{/if}
{if $doPayments}
{if $synccounter > 0}
<div style='height:auto;width:auto;'>
<div align="center"><strong>Bitte warten Sie bis alle Zahlungen verbucht wurden</strong> </div>
<strong>Fortschritt:</strong> 
<div id="progressbar"></div>
<div align="center"><i id='currentprocess'>0</i><i>/{$synccounter} Umsätze bearbeitet</i> <i id='secondscounter'>0</i></div>	
</div>
<script>	
var maxConcurrentRequests = 1;
var concurrentRequests = 0;
var currentSourceIndex = 0;
var lastResponse = "next";
var counter = 0;


$(document).ready(function() {
	var myInterval = setInterval(function () {
  ++counter;
  $( "#secondscounter" ).html(counter);
}, 1000);
 doSync({$synccounter})
 
});

function syncVorgang()
{
	try
	{
		
			var API = "VOPDebitConnect?ajaxwritepayments=true";
			var rsapi = $.getJSON( API, function(json_data) {
				 $( "#progressbar" ).progressbar({
					   value:  $( "#progressbar" ).progressbar("value")+1
				});
				lastResponse = json_data.state;
				var currentvalue = Number( $( "#currentprocess" ).html());
				if(lastResponse == "next" ) $( "#currentprocess" ).html(currentvalue+1);
				concurrentRequests--;
				getTask();
			});
	}catch(Exception)
	{
		concurrentRequests--;
		getTask();
		lastResponse == "error";
	}
}

function getTask()
{
	while(lastResponse == "next" && concurrentRequests < maxConcurrentRequests){
        currentSourceIndex++;
        concurrentRequests++;
        syncVorgang();
    }	
	if(lastResponse == "finish" || lastResponse == "error")
	{
		parent.showLoader();
		parent.$.fancybox.close();
	}
}

function doSync(counterg){
	
		$( "#progressbar" ).progressbar({
 		 max: counterg
		});	
		getTask();
		};

</script>

{literal}
<style type="text/css">
.ui-progressbar {
  height: 2em;
  text-align: left;
  overflow: hidden;
}
.ui-progressbar .ui-progressbar-value {
  margin: -1px;
  height: 100%;
  background-color: #9F3;
}</style>
{/literal}
{else}
<div style='height:auto;width:auto;'>
<div align="center"><strong>Es sind keine Umsätze zugeordnet</strong> </div>
</div>
{/if}
{/if}



<div style='height:400px'>
  <div align="center"><strong>Bitte warten Sie bis die Synchronisierung abgeschlossen wurde</strong>  </div>
    <strong>Fortschritt:</strong>

  <div id="progressbar"></div>
<table width='100%' class='auftragtable'>
<thead><tr><td width="11%">Auftrag</td><td width="5%">Rechnung</td><td width="84%">Status</td></tr></thead>
<tbody></tbody>
</table>
</div>
<script>	



$(document).ready(function() {
var counterg =  {$synccounter};
var progress = $( "#progressbar" ).progressbar({
 	 	max: counterg
	});
	
	function doSync(run) {
		if (run > 0) {
			var API = "VOPDebitConnect?ajaxsync";
			var rsapi = $.getJSON( API, function(json_data) {
				$('.auftragtable tbody').prepend("<tr><td>"+json_data.order+"</td><td>"+json_data.invoice+"</td><td>"+json_data.res+"</td></tr>");
				
				progress.progressbar({
					value: progress.progressbar("value")+1
				});
				doSync(run-1);
			});
		}	
	}

	doSync(counterg);


});
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
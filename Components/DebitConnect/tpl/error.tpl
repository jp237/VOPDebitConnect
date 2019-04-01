{if isset($SQL_ERROR)}<script>$( "<div class='errormsg'><b>SQL-Error:</b><br>{$SQL_ERROR}</div>" ).prependTo( ".msgoutput" );</script>{/if}
{if isset($API_ERROR)}<script>$( "<div class='errormsg'><b>API-Error:</b><br>{$API_ERROR}</div>" ).prependTo( ".msgoutput" );</script>{/if}
{if isset($MAIL_ERROR)}<script>$( "<div class='errormsg'><b>MAIL-Error:</b><br>{$MAIL_ERROR}</div>" ).prependTo( ".msgoutput" );</script>{/if}
{if isset($REG_ERROR)}<script>$( "<div class='errormsg'><b>Registrierung Erforderlich:</b><br>Eine Beauftragung ist erst nach abgeschlossener Registrierung möglich.</div>" ).prependTo( ".msgoutput" );</script>{/if}
{if isset($ERROR_MSG)}<script>$( "<div class='errormsg'><b>{$ERROR_MSG}</div>" ).prependTo( ".msgoutput" );</script>{/if}
{if isset($SUCCESS_MSG)}<script>$( "<div class='successmsg'>{$SUCCESS_MSG}</div>" ).prependTo( ".msgoutput" );</script>{/if}

<form method="post">
<div class='box-group'>
<table class='full'>
<tr><td colspan="2"><h5>Dokumentationen</h5></td></tr>
<tr><td width='200px' style='border-right:1px solid #f2f2f2;' valign="top">
    <table>
    {foreach key=kid from=$musterArt item=art}
    {if $art != "Handbücher"}
<tr><td><a href='?switchTo=documentation&art={$kid}'>{$art}</a></td></tr>
{/if}
{/foreach}
    </table>
	</td>
 	<td  valign="top">
    <table>
    {if !$musterArt}<tr><td>Bitte wählen Sie ein Hilfethema aus</td></tr>{/if}
    {foreach from=$selected item=muster}
    <tr><td><a href='#' class='fancyboxfullscreen' style='font-weight:bold;color:black !important' data-fancybox-href='?switchTo=getMuster&fancy=1&doctype=pdf&doc={$muster.id}'>{$muster.description}</a><br /></td></tr>
    {/foreach}
    </table>
    </td>
</tr>
</table>
</div>
</form>

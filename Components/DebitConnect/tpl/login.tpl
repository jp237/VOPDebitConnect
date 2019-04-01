{literal}
<style type="text/css">
table tbody tr td{
padding-left:50px;
padding-right:50px;
padding-top:12px;
}
</style>
{/literal}
<div style='padding-top:15%;padding-left:40%' id="loginPage">
<form action="VOPDebitConnect" method="post">
<input type="hidden" name="login" />
  <table style='background-color:white;border:1px solid black;padding:10px' width="400px" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><img src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/debitconnect.png' /></td></tr>
    <td><div style='width:auto;border-bottom:1px solid grey' align="center">Version {$version}  {if $handshake=='OK'}<b style='color:green'>Service verfügbar</b>{else}<b style='color:red'>Service nicht verfügbar</b>{/if}</div></td></tr>
    <tr>
      <td><input type="text" class='full'  placeholder="Benutzername" name="userlogin" value='{$smarty.post.userlogin}'/></td>
    </tr>
    <tr>
      <td><input type="password"  class='full'  placeholder="Passwort" name="passwd" /></td>
    </tr>
     <tr><td align="center"><input class='button' {if $handshake!='OK'} disabled {/if} type="submit" name="login" value="Login"/><input  {if $handshake!='OK'} disabled {/if} class='button' type="button" name="demo" value="Demo"/>
     {if $loginerror && !$SUCCESS_MSG}
     <tr>
       <td style='color:red'><div align="center">{$loginerror}</div></td></tr>
     {/if}
    <tr><td><div style='border:1px solid grey;padding:10px'><b>Wichtig</b><br />Machen Sie Regelmäßig einen Datenbank Export</div></td></tr>
   <tr>
     <td><div style='height:40px' align="center"><a style='color:black' href='https://www.eaponline.de/support'>Support</a>  | <a style='color:black' href='https://www.eaponline.de'>Produkthomepage</a></div></td></tr>
  </table>
  </form>
</div>


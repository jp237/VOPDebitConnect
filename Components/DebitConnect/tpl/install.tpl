<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{$title}</title>
<link type='text/css' rel='stylesheet' href="style/button.css">
<link type='text/css' rel="stylesheet" href="style/tblstyle.css" />
<link type='text/css' rel="stylesheet" href="style/main.css" />
</head>

<body>
{if isset($SQL_ERROR)}<div style="background-color:lightcoral">{$SQL_ERROR}</div> {/if}
<div style='padding-top:250px' align="center" id="loginPage">
<form method="post" action="VOPDebitConnect?install=1">
<input type="hidden" name="login" />

  <table style='background-color:white;border:1px solid black;padding:10px' width="450px" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div align='center'><img src='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Views/backend/_resources/img/debitconnect.png' /></div></td></tr>
    <td><div style='margin:2px 15px 2px 15px;width:auto;border-bottom:1px solid grey' align="center">Version {$version}  {if $handshake=='OK'}<b style='color:green'>Service verfügbar</b>{else}<b style='color:red'>Service nicht verfügbar</b>{/if}</div></td></tr>
    <tr><td colspan="2"><div align="center"><b><a target='_new' href='/engine/Shopware/Plugins/Community/Backend/VOPDebitConnect/Components/DebitConnect/Softwarelizenzvertragsbedingungen.txt'>Softwarelizenzbedingungen</a></b></div></td></tr>
      <tr><td colspan="2"><div align="center"><input type="checkbox" required id='lizenz' name='softwarelizenzcheckbox' /> <label for="lizenz">Hiermit akzeptiere ich die Softwarelizenzbedingungen</label></div> </td></tr>
     <tr>
      <td><div align="center"><strong>DebitConnect Installation</strong></div></td>
    </tr>
    <tr>
      <td><div align="center">{if $installmode=='install'}<input type="submit" class="btn btn-success" name="install" value='Installieren'/>{else} <input type="submit" class="btn btn-success" name="update" value='Update durchführen'/>{/if}</div></td>
    </tr>
     <tr>
       <td align="center">
      <tr>
       <td style='color:red'><div align="center"></div></td></tr>
   <tr>
     <td><div style='height:40px' align="center"><a style='color:black' href='https://www.eaponline.de/support'>Support</a>  | <a style='color:black' href='https://www.eaponline.de'>Produkthomepage</a></div></td></tr>
  </table>
  </form>
  </div>
</body>
</html>

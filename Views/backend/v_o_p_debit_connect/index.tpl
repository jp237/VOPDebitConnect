<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="{link file='backend/_resources/img/eap.ico'}">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src='https://code.jquery.com/jquery-3.2.1.js'></script>
<link type='text/css' rel='stylesheet' href="{link file='backend/_resources/style/button.css'}">
<link type='text/css' rel="stylesheet" href="{link file='backend/_resources/style/tblstyle.css'}" />
<link type='text/css' rel="stylesheet" href="{link file='backend/_resources/style/main.css'}" />
{if !isset($smarty.get.noncss)}

<link type='text/css' rel="stylesheet" href="{link file='backend/_resources/style/style.css'}" />
<link type='text/css' rel="stylesheet" href="{link file='backend/_resources/style/dropdown.css'}" />
<link type='text/css' rel='stylesheet' href="{link file='backend/_resources/style/jquery.fancybox.css'}">
<link type='text/css' rel='stylesheet' href="{link file='backend/_resources/style/flatpickr.css'}">
<link type='text/css' rel='stylesheet' href="{link file='backend/_resources/style/gateway.css'}">
<link href='https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
{/if}
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="{link file='backend/_resources/js/jquery.fancybox.pack.js'}"></script>
<script src="{link file='backend/_resources/js/jquery.mask.js'}"></script>
<script src="{link file='backend/_resources/js/validation.js'}"></script>
<script src="{link file='backend/_resources/tinymce/js/tinymce/tinymce.min.js'}"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <style type="text/css">
        #dropdown-nav {
            max-height:500px;
            overflow-x:scroll;
        }
    </style>
</head>
<body style="background-color:#EFEFEF">
<div class="loader" style='display:block'></div>
 {if ($nomenu)}
    {$DebitConnectOutput}
    {else}
            <img style='position:fixed;z-index:99999999;padding-left:5px' height='60px' src="{link file='backend/_resources/img/VOP_Trans_klein.png'}">
<div class='container header' id='dropdown'>
                <ul>
                	 <li id='click'  href='VOPDebitConnect?switchTo=start&changeShop={$shopList[0].id}'>{$shopList[0].name}
                        {if $shopList|count > 1}
                        <ul id="dropdown-nav">
                         {for $shopcounter=1 to $shopList|count-1}
                         <li id='click'  href='VOPDebitConnect?switchTo=start&changeShop={$shopList[{$shopcounter}].id}'>{$shopList[{$shopcounter}].name}</li>
                         {/for}
                         </ul>
                        {/if}
                    </li>
                      <li  id='click' href='VOPDebitConnect?switchTo=bonigateway'>BoniGateway
                    <ul>
                      <li  id='click' href='VOPDebitConnect?switchTo=bonigateway'>Einstellungen</li>
                      <li  id='click' href='VOPDebitConnect?switchTo=BoniLog'>Protokoll</li>
                      <li id='clicknew' href='https://gateway.eaponline.de'>Konfiguration</li>
                    </ul>
                    <li  id='click' href='VOPDebitConnect?switchTo=zahlungsabgleich'>Zahlungsabgleich
                    <ul>
                    	<li  id='click' href='VOPDebitConnect?switchTo=zalog'>Protokoll</li>
                        <li  id='click' href='VOPDebitConnect?switchTo=dtaList'>Lastschriften</li>
                         <li  id='click' href='VOPDebitConnect?switchTo=opListe'>OP-Liste</li>
                    </ul>
                    </li>
                    <li id='click'  href='VOPDebitConnect?switchTo=1'>Zahlungserinnerung
                        <ul>
                            <li  id='click' href='VOPDebitConnect?switchTo=1'>Vorschlagsliste</li>
                            <li  id='click' href='VOPDebitConnect?switchTo=2'>Versendet</li>
                        </ul>
                    </li>
                    <li  id='click' href='VOPDebitConnect?switchTo=3'>Mahnung
                        <ul>
                            <li  id='click' href='VOPDebitConnect?switchTo=3'>Vorschlagsliste</li>
                            <li  id='click' href='VOPDebitConnect?switchTo=4'>In Bearbeitung</li>
                            <li  id='click' href='VOPDebitConnect?switchTo=5'>Erledigt</li>
                        </ul>
                    </li>
                    <li  id='click' href='VOPDebitConnect?switchTo=7'>Inkasso
                        <ul>
                            <li  id='click' href='VOPDebitConnect?switchTo=7'>In Bearbeitung</li>
                            <li  id='click' href='VOPDebitConnect?switchTo=8'>Erledigt</li>
                        </ul>
                    </li>
					<li  id='click' href='VOPDebitConnect?switchTo=settings&setting=reg'>Einstellungen
                    <ul>
                    <li  id='click' href='VOPDebitConnect?switchTo=settings&setting=reg'>Registrierung</li>
                    <li  id='click' href='VOPDebitConnect?switchTo=settings&setting=frist'>Fristen/Status</li>
                    <li  id='click' href='VOPDebitConnect?switchTo=settings&setting=hbci'>HBCI</li>
                     <li  id='click' href='VOPDebitConnect?switchTo=settings&setting=cronjob'>Cronjob</li>
                    </ul>
                    </li>
                    <li  id='click' href='VOPDebitConnect?switchTo=papierkorb'>Allgemein
                    <ul>
                   <li  id='click' href='VOPDebitConnect?switchTo=documentation'>Dokumentation</li>
                    <li  id='click' href='VOPDebitConnect?switchTo=papierkorb'>Papierkorb</li>
                    <li  id='click' href='VOPDebitConnect?switchTo=belege'>Belege</li>
                    <li id='click' href='VOPDebitConnect?switchTo=logbuch'>Logbuch</li>
                    <li id='clicknew' href='//www.eaponline.de/support'>Support</li>
                    </ul>
                    </li>
                     <li class='fancyboxreload' href='#' data-fancybox-href='VOPDebitConnect?switchTo=sync&noncss=1&fancy=1'>Synchronisierung</li>
                </ul>
            </div>


                     <div class='bodycontainer'>
                         {foreach from=$alerts item="alert"}
                             <div class="alert alert-{$alert.type}">{$alert.msg}</div>
                         {/foreach}
                         <div class="alert alert-info" style="padding: 5px" >Gewählter Shop/Sub-Shop : {$SELECTED_SUBSHOP}</div>
                     <div class='msgoutput'><div>
                     {$DebitConnectOutput}                     
                     </div>
                     {/if}
			</body>		
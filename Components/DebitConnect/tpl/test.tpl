<style type="text/css">
    .stddiv{
        width:100px;
        float:left;
    }
    .stdrow{
        word-wrap:break-word;
        width:100%;
        float:left;
        padding-top
    }
</style>
<h3>Artikelpositionen</h3>

<div class="stddiv">
    Artikelnummer
</div>
<div class="stddiv">
    Anzahl
</div>
<div class="stddiv">
    Preis
</div>
<div class="stddiv">
    Gesamtpreis
</div>
<div class="stddiv">
    Steuern
</div><br>

{foreach from=$Artikel item=_artikel}
    <div class="stdrow">

        <div class="stddiv">
            {$_artikel.articleordernumber}
        </div>

        <div class="stddiv">
            {$_artikel.quantity}
        </div>
        <div class="stddiv">
            {$_artikel.price} &euro;
        </div>
        <div class="stddiv">
            {$_artikel.quantity*$_artikel.price} &euro;
        </div>
        <div class="stddiv">
            {$_artikel.tax_rate} %
        </div>
        <div class="stdrow">
            <b>{$_artikel.name|truncate:"40"}</b>
        </div>
    </div>
{/foreach}
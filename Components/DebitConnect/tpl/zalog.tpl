{$listview.header}
{$listview.table}
<div style='padding-top:15px'>
<form method="post" name="zaDate">
    <div class="col-sm-1">Protokoll vom</div>
    <div class="col-sm-1">
        <input type="text" class="form-control" value='{$datefilter}' required name='datefilter' class='datepickerzahlung' />
    </div>
    <div class="col-sm-1">
        <input class='btn btn-info' value='Abrufen' type="submit" name="changeDate" />
    </div>
</form>
</div>
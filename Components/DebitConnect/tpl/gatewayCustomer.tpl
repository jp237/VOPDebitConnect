<form id='redirectForm' method="post" action="https://gateway.eaponline.de/index.php?controller=requests&action=getRequest&no_render_template">
    <input type="hidden" name="jsonData" value="{$jsonData}">
    <input type="hidden" name="authToken" value="{$vopAuthToken}">
</form>
<script>
    $(document).ready(function () {
        $('#redirectForm').submit();
    })
</script>
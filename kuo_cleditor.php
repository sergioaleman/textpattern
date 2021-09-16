if (@txpinterface == 'admin') {
register_callback('kuo_cleditor_head','admin_side','head_end');
}

function kuo_cleditor_head() {
if ($GLOBALS['event'] === 'article') {

echo '<link rel="stylesheet" type="text/css" href="./cleditor/jquery.cleditor.css" media="all" />
<script type="text/javascript" src="./cleditor/jquery.cleditor.min.js"></script>
<script type="text/javascript" src="./cleditor/jquery.cleditor.xhtml.min.js"></script>
<script type="text/javascript" src="./cleditor/jquery.cleditor.table.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
$("textarea#body").cleditor({height:350,width:\'94%\'});
$("textarea#excerpt").cleditor({height:150,width:\'94%\'});
$("select#markup-body option[value=0]").attr("selected","selected");
$("select#markup-excerpt option[value=0]").attr("selected","selected");
$("select#markup-body").css("border","1px solid #690");
$("select#markup-excerpt").css("border","1px solid #690");
$($("select#markup-body")).change(function(){
if ($(this).val() == 0) {
$("select#markup-body").css("border","1px solid #690");
}
else {
$("select#markup-body").css("border","1px solid #c00");
}
});
$($("select#markup-excerpt")).change(function(){
if ($(this).val() == 0) {
$("select#markup-excerpt").css("border","1px solid #690");
}
else {
$("select#markup-excerpt").css("border","1px solid #c00");
}
});
});
</script>';

}
}

$(document).ready(function () {

    $("#accounts .nav-pills li[data-filter='web'] a").trigger("click");

    $("div.thumbnail button.message-btn").click(function(){
        $("#form-content .contactbuyaccount #categoryid").val($(this).attr("data-id"))
        $("#form-content").modal();
    });
});
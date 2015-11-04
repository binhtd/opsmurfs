$(document).ready(function () {
    $("div.thumbnail button.message-btn").click(function(){
        $("#form-content .contactbuyaccount #categoryid").val($(this).attr("data-id"))
        $("#form-content").modal();
    });
});
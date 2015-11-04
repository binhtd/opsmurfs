$(document).ready(function () {
    $("div.thumbnail button.message-btn").click(function(){
        $("#form-content .contact #categoryid").val($(this).attr("data-id"))
        $("#form-content").modal();
    });
});
$(document).ready(function () {

    $("#post_id").focus(function () {
        $.ajax({
            url: "/api/posts/id/" + $(this).val()
        }).then(function (data) {
            $('#content').val(data.content);
        });
       
    });

    $('#deleteSimplePost').submit(function () {
        var confirmation = confirm("Are you sure to delete?");
        return confirmation;
    });

    $('#updateSimplePost').submit(function () {
        var confirmation = confirm("Are you sure to update?");
        return confirmation;
    });

    $("#myInput").keyup(function () {
        var input, filter, ul, li, a, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        ul = document.getElementById("id01");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            if (li[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    });

    $("#userSearch").keyup(function () {
        var input, filter, ul, li, a, i;
        input = document.getElementById("userSearch");
        filter = input.value.toUpperCase();
        ul = document.getElementById("idUserList");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            if (li[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    });
});

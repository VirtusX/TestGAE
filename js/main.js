$(document).ready(function () {
    $(document).on('change', '#select', function () {
        if ($(this).val() >= "0") {
            localStorage.setItem('select', $(this).val());
        }
        getList();
    });
    if (localStorage.getItem('select')) {
        $("#select").find("option").eq(localStorage.getItem('select')).prop('selected', true);
        getList();
    }
});
function getList() {
    var xmlhttp;
    if (localStorage.getItem('select') == "") {
        document.getElementById("company").innerHTML = "";
    }
    else {
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        }
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("company").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "list.php?c=" + localStorage.getItem('select'), true);
        xmlhttp.send();
    }
}

$(function () {
    $("#form_open").click(function () {
        var value = $("#add");
        if (value.hasClass("col-md-2")) {
            value.removeClass("col-md-2");
            value.addClass("col-md-7");
            setTimeout(function () {
                $("#form").slideToggle();
            }, 400);
        }
        else {
            $("#form").slideToggle();
            setTimeout(function () {
                value.addClass("col-md-2");
                value.removeClass("col-md-7");
            }, 400);
        }

    });
    $("#select_head").click(function () {
        $("#select").slideToggle();
    });

});

$(document).on('click', '#edit', function (event) {
    event.preventDefault();
    $(".com_prop").removeAttr("readonly");
    $('.c_edit').hide();
    $('.input-group-btn').append('<button type="submit" class="btn btn-success "><span class="glyphicon glyphicon-ok"></button>');
    //$('.check').removeAttr('disabled hidden'); for main and sub companies editing
});
$(document).on('click', '#delete', function (event) {
    event.preventDefault();
    var id = $('#id').val();
    window.location.href = "index.php?delete=" + id;
});


$(function () {
    $('#side-menu').metisMenu();
});

$(function () {
    $('select[value]').each(function () {
        $(this).val(this.getAttribute("value"));
    });
});

//Loads the correct sidebar on window load
$(function () {
    $(window).bind("load", function () {
        console.log($(this).width())
        if ($(this).width() < 768) {
            $('div.sidebar-collapse').addClass('collapse')
        } else {
            $('div.sidebar-collapse').removeClass('collapse')
        }
    })
});

//Collapses the sidebar on window resize
$(function () {
    $(window).bind("resize", function () {
        console.log($(this).width())
        if ($(this).width() < 768) {
            $('div.sidebar-collapse').addClass('collapse')
        } else {
            $('div.sidebar-collapse').removeClass('collapse')
        }
    })
});

$(document).ready(function () {

    //initialize beautiful datetime picker
    $("input[type=date]").datepicker();
    $("input[type=date]").datepicker("option", "dateFormat", "yy-mm-dd");

    $("#followers").submit(function (e) {
        e.preventDefault();
        var startdate = $('input[name="startdate"]').val();
        var enddate = $('input[name="enddate"]').val();
        $('#output').attr("src", "/employer/followerstats/" + startdate + "/" + enddate + "");
    });

    $("#reach").submit(function (e) {
        e.preventDefault();
        var startdate = $('input[name="startdate"]').val();
        var enddate = $('input[name="enddate"]').val();
        var updatekey = $('select[name="meta_value"]').val();
        $('#output').attr("src", "/employer/reachstats/" + updatekey + "/" + startdate + "/" + enddate + "");
    });
        
});
(function (window, Model) {
    window.request = Model.initialize();
    window.opts = {};
}(window, window.Model));

//Google Tag Manager
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KFFXD4');

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

    $(".status").click(function(e) {
        e.preventDefault();
        var btn = $(this),
            application = btn.data('application'),
            status = btn.data('status');

        request.create({
            action: 'students/applications',
            data: {status: status, application: application, action: 'updateStatus'},
            callback: function (data) {
                btn.closest('tr').hide();
                $("#result_status").html("Applicant "+ status + "Successfully");
            }
        });

    });
    
    $('button[name=message]').click(function (e) {
        var self = this;
        window.opts.user_id = $(this).data("user_id");
        $('#message_modal').modal('show');
    });

    $('#messageform').submit(function(e) {
        e.preventDefault();
        var body = $('#body').val();
        request.create({
            action: "employer/messages",
            data: {action: 'message', user_id: window.opts.user_id, body: body},
            callback: function (data) {
                $('#status').html('Message Sent Successfully!!!');
                $('#message_modal').modal('hide');
            }
        });
    });
});
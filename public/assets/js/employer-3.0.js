(function (window, Model) {
    window.request = Model.initialize();
    window.opts = {};
}(window, window.Model));

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

//Google Analytics
(function (i, s, o, g, r, a, m) {
    i['GoogleAnalyticsObject'] = r;
    i[r] = i[r] || function () {
        (i[r].q = i[r].q || []).push(arguments)
    }, i[r].l = 1 * new Date();
    a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
    a.async = 1;
    a.src = g;
    m.parentNode.insertBefore(a, m)
})(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

ga('create', 'UA-49272936-3', 'swiftintern.com');
ga('require', 'linkid', 'linkid.js');
ga('send', 'pageview');

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
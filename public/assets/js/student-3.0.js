(function (window, Model) {
    window.request = Model.initialize();
    window.opts = {};
}(window, window.Model));

$(".chosen").chosen({
    no_results_text: "Oops, nothing found!",
    width: "95%"
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
    $('button[name=more]').click(function (e) {
        var refid = this.value;
        var element = $('#' + refid).html();
        $('#' + refid).after(element);
    });

    $('button[name=message]').click(function (e) {
        var self = this;
        window.opts.property = $(this).data("property");
        window.opts.property_id = $(this).data("propertyid");
        $('#message_modal').modal('show');
    });

    $('#messageform').submit(function(e) {
        e.preventDefault();
        var body = $('#body').val();
        request.create({
            action: "home/contact",
            data: {action: "contact", property: window.opts.property, property_id: window.opts.property_id, body: body},
            callback: function (data) {
                $('#status').html('Message Sent Successfully!!!');
                $('#message_modal').modal('hide');
            }
        });
    });
    
    $('button[name=delete]').click(function (e) {
        var self = this,
            value = this.value,
            id = $(self).data("id");
        console.log(id);
        request.create({
            action: "students/delete",
            data: {action: 'delete'+value, id: id},
            callback: function (data) {
                alert('Deleted Successfully!!!');
            }
        });
    });

    setInterval(loadSponsored({
        limit: 3
    }), 5000);
});

function loadColleges() {
    request.read({
        action: "organizations/index",
        data: {
            limit: 5000,
            type: 'institute'
        },
        callback: function (data) {
            $.each(data.organizations, function (i, college) {
                $('#colleges').append('<option value="' + college._name + '">');
            });
        }
    });
}

function loadCompanies() {
    request.read({
        action: "organizations/index",
        data: {
            limit: 5000,
            type: 'company'
        },
        callback: function (data) {
            $.each(data.organizations, function (i, college) {
                $('#companies').append('<option value="' + college._name + '">');
            });
        }
    });
}

function loadSponsored(opts) {
    request.read({
        action: "home/sponsored",
        data: opts,
        callback: function (data) {
            if (data.sponsoreds) {
                $.each(data.sponsoreds, function (i, item) {
                    $('#sponsored').html('<div class="media"><a class="media-left" href="' + item._type + '/details/' + encodeURI(item._title) + '/' + item._id + '"><img src="/organizations/photo/' + item._organization_id + '" width=60 alt="' + item.title + '"></a><div class="media-body"><p class="media-heading"><a href="' + item._type + '/details/' + encodeURI(item._title) + '/' + item._id + '" target="_blank">' + item._title + '</a></p></div></div>');
                });
            }
        }
    });
}

//Start of Zopim Live Chat Script
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
$.src="//v2.zopim.com/?3RxEIkzsUZsvxcaF9qoCaxQcPVvDYeJJ";z.t=+new Date;$.
type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
(function (window, Model) {
    window.request = Model.initialize();
    window.opts = {};
}(window, window.Model));

$(".chosen").chosen({
    no_results_text: "Oops, nothing found!",
    width: "95%"
});

$('button[name=more]').click(function (e) {
    var refid = this.value;
    var element = $('#' + refid).html();
    $('#' + refid).after(element);
});

$(document).ready(function () {
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
                    $('#sponsored').html('<div class="media"><a class="media-left" href="' + encodeURI(item._title) + '/' + item._id + '"><img src="http://assets.swiftintern.com/uploads/images/sml_7921790.jpg" width=60 alt="' + item.title + '"></a><div class="media-body"><p class="media-heading"><a href="' + encodeURI(item._title) + '/' + item._id + '" target="_blank">' + item._title + '</a></p></div></div>');
                });
            }
        }
    });
}
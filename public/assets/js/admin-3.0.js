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


$(document).ready(function () {
    $('#search').submit(function (e) {
        e.preventDefault();
        var action = $('input[name="action"]').val(),
            model = $('select[name="model"]').val(),
            key = $('input[name="key"]').val(),
            value = $('input[name="value"]').val();
        $('#results').html('');
        $('#result_status').html('');
        request.read({
            action: "admin/search",
            data: {action : action, model: model, key: key, value: value},
            callback: function (data) {
                if (data.results) {
                    $('#result_status').html('Total Results : ' + data.results.length);
                    $.each(data.results, function (i, result) {
                        $('#results').append('<tr><td><b>Action</b></td><td><a href="/admin/update/" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a></td></tr>');
                        $.each(result, function (field, value) {
                            $('#results').append('<tr><td>' + field + '</td><td>' + value + '</td></tr>');
                        });
                    });
                } else {
                    $('#result_status').html('Not Found.');
                }
            }
        });

    });
});
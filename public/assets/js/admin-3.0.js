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

    //initialize beautiful datetime picker
    $("input[type=date]").datepicker();
    $("input[type=date]").datepicker("option", "dateFormat", "yy-mm-dd");

    //Search form
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
            data: {action: action, model: model, key: key, value: value},
            callback: function (data) {
                if (data.results) {
                    $('#result_status').html('Total Results : ' + data.results.length);
                    $.each(data.results, function (i, result) {
                        $('#results').append('<tr><td><b>Action</b></td><td><a href="/admin/update/' + model + '/' + result._id + '" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Edit</a></td></tr>');
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

    $('#created_stats').submit(function (e) {
        $('#stats').html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
        e.preventDefault();
        var data = $(this).serializeArray();
        request.read({
            action: "admin/data",
            data: data,
            callback: function (data) {
                $('#stats').html('');
                if (data.data) {
                    Morris.Bar({
                        element: 'stats',
                        data: toArray(data.data),
                        xkey: 'y',
                        ykeys: ['a'],
                        labels: ['Total']
                    });
                }
            }
        });
    });
    
    $('#getstats').submit(function (e) {
        $('#stats').html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
        e.preventDefault();
        var data = $(this).serializeArray();
        request.read({
            action: "admin/stats",
            data: data,
            callback: function (data) {
                $('#stats').html('');
                if (data.data) {
                    Morris.Bar({
                        element: 'stats',
                        data: toArray(data.data),
                        xkey: 'y',
                        ykeys: ['a'],
                        labels: ['Total']
                    });
                }
            }
        });
    });

});

function toArray(object) {
    var array = $.map(object, function (value, index) {
        return [value];
    });
    return array;
}
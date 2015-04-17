(function (window, Model) {

    $(document).on("click", "button[name=delete]", function (e) {
        e.preventDefault();

        var button = this,
                id = this.data('id'),
                name = this.data('name'),
                callback = this.data('callback'),
                request = Model.initialize();

        request.delete({
            action: name + '/delete/' + id,
            callback: function (data) {
                var opts = {
                    data: data,
                    element: button
                }

                $.each(data, function (i, item) {
                    opts.i = item;
                });

                callback.call(data);
            }
        });

    });

    $(document).on("click", "button[name=read]", function (e) {
        e.preventDefault();

        var button = this,
                data = {},
                request = Model.initialize();
                
        $.each(button.data(), function (i, item) {
            data.i = item;
        });

        request.read({
            action: this.data('action'),
            data: data,
            callback: function (data) {
                var opts = {
                    data: data,
                    element: button
                }

                $.each(data, function (i, item) {
                    opts.i = item;
                });

                callback.call(data);
            }
        });

    });

}(window, window.Model));
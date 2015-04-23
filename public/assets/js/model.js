(function (window) {

    var Model = (function () {
        function Model(opts) {
            this.api = window.location.origin + '/';
            this.ext = '.json';
        }

        Model.prototype = {
            create: function (opts) {
                var self = this,
                        link = this._clean(this.api) + this._clean(opts.action) + this._clean(this.ext);
                $.ajax({
                    url: link,
                    type: 'POST',
                    data: opts.data,
                }).done(function (data) {
                    if (opts.callback) {
                        opts.callback.call(self, data);
                    }
                }).fail(function () {
                    console.log("error");
                }).always(function () {
                    //console.log("complete");
                });
            },
            read: function (opts) {
                var self = this,
                        link = this._clean(this.api) + this._clean(opts.action) + this._clean(this.ext);
                $.ajax({
                    url: link,
                    type: 'GET',
                    data: opts.data,
                }).done(function (data) {
                    if (opts.callback) {
                        opts.callback.call(self, data);
                    }
                }).fail(function () {
                    console.log("error");
                }).always(function () {
                    //console.log("complete");
                });

            },
            update: function (opts) {
                return this.url;
            },
            delete: function (opts) {
                var self = this,
                        link = this._clean(opts.api) + this._clean(opts.action) + this._clean(opts.ext);
                $.ajax({
                    url: link,
                    type: 'POST',
                    data: opts.data,
                }).done(function (data) {
                    if (opts.callback) {
                        opts.callback.call(self, data);
                    }
                }).fail(function () {
                    console.log("error");
                }).always(function () {
                    console.log("complete");
                });
            },
            _clean: function (entity) {
                return entity || "";
            }
        };
        return Model;
    }());

    Model.initialize = function (opts) {
        return new Model(opts);
    };

    window.Model = Model;
}(window));

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
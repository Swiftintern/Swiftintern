(function (window) {

    var Model = (function () {
        function Model(opts) {
            this.api = 'http://localhost/swiftintern/';
            this.ext = '.json';
        }

        Model.prototype = {
            create: function (opts) {
                return this.url;
            },
            read: function (opts) {
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
            _clean: function(entity) {
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
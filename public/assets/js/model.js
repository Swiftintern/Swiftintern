(function (window) {

    var Model = (function () {
        function Model(opts) {
            this.domain = opts.domain;
        }

        Model.prototype = {
            create: function (opts) {
                var self = this;
                $.ajax({
                    url: opts.domain + opts.url,
                    type: 'POST',
                    data: opts.data,
                }).done(function (data) {
                    if (opts.callback) {
                        opts.callback.call(self);
                    }
                }).fail(function () {
                    console.log("error");
                }).always(function () {
                    console.log("complete");
                });

            },
            read: function (opts) {
                return this.url;
            },
            update: function (opts) {
                return this.url;
            },
            delete: function (opts) {
                return this.url;
            }
        };
        return Model;
    }());

    Model.initialize = function (opts) {
        return new Model(opts);
    };

    window.Model = Model;
}(window));
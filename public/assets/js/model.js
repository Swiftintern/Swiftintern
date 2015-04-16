(function (window) {
    var Model = (function () {

        function Model(opts) {
            var url = opts.url;
        }

        Model.prototype = {
            getURL: function () {
                return url;
            }
        };

        return Model;
    });

    Model.create = function (opts) {
        return new Model(opts);
    };

    window.Model = Model;

}(window));
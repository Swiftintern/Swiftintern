(function (window, Model) {
    var request = Model.initialize({
        domain: window.location.host + '/swiftintern/'
    });

    request.create({
        url : "home/index",
        data : {},
        callback : function(data) {
            console.log(data);
        }
    });

}(window, window.Model));
(function(window, Model) {
    var request = Model.create({
        url: window.location.host+'/swiftintern/'
    });
    
    request.addMethod('read');
}(window, window.Model));
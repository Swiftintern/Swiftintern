(function (window) {
    var Model = (function(){
       
        function Mapster(opts) {
           
       }
       
       Model.prototype = {
           _create: function(opts){
               
           },
           
           _read: function(opts){
               
           },
           
           _update: function(opts){
               
           },
           
           _delete: function(opts){
               
           }
       };
       
       return Model;
    });
    
    Model.create = function(opts){
        return new Model(opts);
    };
    
    window.Model = Model;
    
}(window));


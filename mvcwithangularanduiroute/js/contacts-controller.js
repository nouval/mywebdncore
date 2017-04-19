
contact.controller('ContactsController', function ($scope, $state, $http) {
    
    // pull all
    $http.get('tmp-data/contacts.json').success(function(data) {
        $scope.contacts = data;
    });  
    
    $scope.delete = function(id) {
        if(confirm("are you sure to delete '" + id + "'?")) {
            console.log(id + " is deleted!");
        }
    };

});
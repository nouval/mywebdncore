
contact.controller('ContactDetailController', function ($scope, $stateParams, $state, $http) {
  
  // pull all
  $http.get('tmp-data/contacts.json').success(function(data) {
      $scope.contact = null;
      // should do better job on this :(
      
      data.forEach(function(item, index) {
          if (item.user.registered == $stateParams.id) {
              
              $scope.contact = item;
          }
      });
  });
  
  // save new or editted contact
  $scope.save = function() {
      $state.go('home');
  }
  
  // cancel and go home
  $scope.home = function() {
      $state.go('home');
  }
});
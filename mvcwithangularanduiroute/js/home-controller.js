
contact.controller('HomeController', function ($scope, $state) {
  
  // user search by either first name or last name
  $scope.search = function () {
      $state.go('search'); 
  }
  
  // user add new contact
  $scope.add = function() {
      $state.go('detail');
  }
  
  // user hit reset
  $scope.home = function() {
      $state.go('home');
  }
});
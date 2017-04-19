var contact = angular.module('contact', ['ui.router']);

contact.config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/');
    
    $stateProvider
        .state('home', {
            url: '/',
            views: {
                'sidebar': {
                    templateUrl: 'search-bar.html',
                    controller: 'HomeController'
                },
                'content': {
                    templateUrl: 'home-content.html'
                }
            }
        })
        .state('search', {
            url: '/search',
            views: {
                'sidebar': {
                    templateUrl: 'search-bar.html',
                    controller: 'HomeController'
                },
                'content': {
                    templateUrl: 'contacts-content',
                    controller: 'ContactsController'
                }
            }            
        })
        .state('detail', {
            url: '/contact/:id',
            views: {
                'sidebar': {
                    templateUrl: 'search-bar.html',
                    controller: 'HomeController'
                },
                'content': {
                    templateUrl: 'detail-content.html',
                    controller: 'ContactDetailController'
                }
            }            
        })        
}])

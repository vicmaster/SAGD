// app/core/config.js

(function (){
  'use strict';

  angular
    .module('sagdApp.core')
    .config(configure)
    .run(updateState)
    .run(formlyConfig);

  configure.$inject = ['$urlRouterProvider', '$authProvider', '$locationProvider'];

  function configure($urlRouterProvider, $authProvider, $locationProvider){
    var baseUrl = "http://api.sagd.app/api/v1";
    $authProvider.loginUrl = baseUrl + '/authenticate';
    $authProvider.withCredentials = true;

    $urlRouterProvider.otherwise('/');

    if (window.history && window.history.pushState) {
      //$locationProvider.html5Mode(true).hashPrefix('!');
    }
  }

  updateState.$inject = ['$rootScope', '$state', 'state'];

  function updateState($rootScope, $state, state){
    $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams){
      state.setNewState(fromState.name, toState.name);
    });
  }

  formlyConfig.$inject = ['formly.sagd'];

  function formlyConfig(formlySagd){
    formlySagd.init();
  }

})();

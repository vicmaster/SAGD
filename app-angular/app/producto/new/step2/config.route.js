// app/producto/new/step2/config.route.js

(function() {
  'use strict';

  angular
    .module('sagdApp.producto')
    .config(configureRoutes);

  configureRoutes.$inject = ['$stateProvider'];

  /* @ngInject */
  function configureRoutes($stateProvider) {
    $stateProvider
      .state('productoNew.step2', {
        parent: 'productoNew',
        templateUrl: 'app/producto/new/step2/step2.html',
        controller: 'productoNewStepController',
        controllerAs: 'vm'
      });
  }
})();

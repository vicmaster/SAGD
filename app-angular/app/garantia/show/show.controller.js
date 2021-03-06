// app/garantia/show/show.controller.js

(function() {
  'use strict';

  angular
    .module('sagdApp.garantia')
    .controller('garantiaShowController', garantiaShowController);

  garantiaShowController.$inject = ['$stateParams', 'api'];

  /* @ngInject */
  function garantiaShowController($stateParams, api) {

    var vm = this;
    vm.id = $stateParams.id;
    vm.back = goBack;

    vm.fields = [
      {
        type: 'input',
        key: 'descripcion',
        templateOptions: {
          type: 'text',
          label: 'Descripcion:'
        }
      }, {
        type: 'input',
        key: 'dias',
        templateOptions: {
          type: 'number',
          label: 'Días:'
        }
      }, {
        type: 'input',
        key: 'seriado',
        templateOptions: {
          type: 'text',
          label: 'Seriado:'
        }
      }
    ];

    activate();

    ////////////////

    function activate() {
      return obtenerGarantia().then(function(response) {
        console.log(response.message);
      })
    }

    function obtenerGarantia() {
      return api.get('/tipo-garantia/', vm.id)
        .then(function(response) {
          vm.garantia = response.data.tipoGarantia;
          return response.data;
        })
        .catch(function(response) {
          vm.error = response.data;
          return response.data;
        })
    }

    function goBack() {
      window.history.back();
    }
  }
})();

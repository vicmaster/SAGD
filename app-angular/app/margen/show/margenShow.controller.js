// app/margen/show/margen.controller.js

(function() {

  'use strict';

  angular
    .module('sagdApp.margen')
    .controller('margenShowController', MargenShowController);

  MargenShowController.$inject = ['$stateParams', 'api', 'utils'];

  /* @ngInject */
  function MargenShowController($stateParams, api, utils) {

    var vm = this;
    vm.id = $stateParams.id;
    vm.back = goBack;

    vm.fields = [
      {
        type: 'input',
        key: 'nombre',
        templateOptions: {
          type: 'text',
          label: 'Nombre:'
        }
      }, {
        type: 'input',
        key: 'valor',
        templateOptions: {
          type: 'text',
          label: 'Valor:'
        },
        formatters: [utils.formatPercentage]
      }, {
        type: 'input',
        key: 'valor_webservice_p1',
        templateOptions: {
          type: 'text',
          label: 'Webservice P1:'
        },
        formatters: [utils.formatPercentage]
      }, {
        type: 'input',
        key: 'valor_webservice_p8',
        templateOptions: {
          type: 'text',
          label: 'Webservice P8:'
        },
        formatters: [utils.formatPercentage]
      }
    ];
    initialize();

    function initialize() {
      return obtenerMargen().then(function(response) {
        console.log(response.message);
      });
    }

    function obtenerMargen() {
      return api.get('/margen/', vm.id)
        .then(function(response) {
          vm.margen = response.data.margen;
          return response.data;
        })
        .catch(function(response) {
          vm.error = response.data;
          return response.data;
        });
    }

    function goBack() {
      window.history.back();
    }
  }

})
();

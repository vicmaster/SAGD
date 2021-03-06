// app/cliente/show/clienteShow.controller.js

(function() {

  'use strict';

  angular
    .module('sagdApp.cliente')
    .controller('clienteShowController', ClienteShowController);

  ClienteShowController.$inject = ['$location', '$state', '$stateParams', 'api', 'pnotify', 'Cliente', 'utils'];

  /* @ngInject */
  function ClienteShowController($location, $state, $stateParams, api, pnotify, Cliente, utils) {

    var vm = this;
    vm.id = $stateParams.id;

    ///////////////////////////////////

    activate();

    function activate() {

      obtenerCliente()
        .then(function() {
          utils.whichTab($location.hash() || 'datos-generales');
          return $state.go('clienteShow.details');
        })
        .then(obtenerEmpleados)
        .then(obtenerReferencias)
        .then(obtenerRoles)
        .then(obtenerEstatus)
        .then(obtenerSucursales);
    }

    /////////////// Get resources ///////////////

    function obtenerCliente() {
      return Cliente.show(vm.id)
        .then(function(cliente) {
          vm.cliente = cliente;
          console.log('Cliente ' + cliente.nombre + ' obtenido');
          return cliente;
        });
    }

    function obtenerEmpleados() {
      return api.get('/empleado')
        .then(function(response) {
          vm.empleados = response.data;
          console.log('Empleados obtenidos correctamente.');
          return response.data;
        })
        .catch(function(response) {
          console.log('No se pudieron obtener los empleados', response.data);
        });
    }

    function obtenerReferencias() {
      return api.get('/cliente-referencia')
        .then(function(response) {
          vm.clientes_referencias = response.data;
          console.log('Catálogo de referencias obtenido correctamente.');
          return response.data;
        })
        .catch(function(response) {
          console.log('No se pudo obtener el catálogo de referencias.');
        });
    }

    function obtenerRoles() {
      return api.get('/roles/clientes')
        .then(function(response) {
          vm.roles = response.data;
          return response;
        })
        .catch(function(response) {
          vm.error = response.data;
          pnotify.alert('Hubo un problema al obtener los Roles', vm.error.message, 'error');
          return response;
        });
    }

    function obtenerEstatus() {
      return api.get('/cliente-estatus')
        .then(function(response) {
          vm.estatus = response.data;
          return response.data;
        })
        .catch(function(response) {
          vm.error = response.data;
          pnotify.alert('Hubo un problema al obtener los Estatus', vm.error.error, 'error');
        });
    }

    function obtenerSucursales() {
      return api.get('/sucursal')
        .then(function(response) {
          vm.sucursales = response.data.filter(function(sucursal) {
            return !sucursal.proveedor.externo;
          });

          return response.data;
        })
        .catch(function(response) {
          vm.error = response.data;
          pnotify.alert('Hubo un problema al obtener las Sucursales', vm.error.error, 'error');
        });
    }

  }

})();

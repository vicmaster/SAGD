// app/cliente/clienteNew.controller.js

(function (){

  'use strict';

  angular
      .module('sagdApp.cliente')
      .controller('clienteNewController', ClienteNewController);

  ClienteNewController.$inject = ['$auth', '$state', 'api', 'pnotify', 'utils', 'session'];

  function ClienteNewController($auth, $state, api, pnotify, utils, session){
    if (!$auth.isAuthenticated()) {
      $state.go('login', {});
    }

    var vm = this;
    vm.create = create;
    vm.setClass = utils.setClass;
    vm.model = {};
    //vm.empleado = session.obtenerEmpleado;
    
    activate();

    function activate() {    
      obtenerReferencias()
          .then(obtenerEmpleados)
          .then(obtenerSucursales);
    }

    function obtenerReferencias() {
      return api.get('/cliente-referencia').then(function (response) {
            vm.referencias = response.data;
            return response;
          })
          .catch(function (response) {
            vm.error = response.data;
            pnotify.alert('Hubo un problema al obtener las Referencias', vm.error.error, 'error');
            return response;
          });
    }

    function obtenerEmpleados() {
      return api.get('/empleado')
          .then(function (response) {
            vm.empleados = response.data;
            console.log('Empleados obtenidos correctamente');
          })
          .catch(function (response) {
            vm.error = response.data;
            pnotify.alert('Hubo un problema al obtener los Empleados', vm.error.error, 'error');
            return response;
          });
    }

    function obtenerSucursales() {
      return api.get('/sucursal')
          .then(function (response) {
            vm.sucursales = response.data;
            console.log('Sucursales obtenidas correctamente');
          })
          .catch(function (response) {
            vm.error = response.data;
            pnotify.alert('Hubo un problema al obtener las Sucursales', vm.error.error, 'error');
            return response;
          });
    }

    function create(){

      if(vm.model.$invalid){
        pnotify.alert('Error', 'Error validacion', 'error');
        return false;
      }

      vm.model.cliente_estatus_id = 1; // Cliente Nuevo
      vm.model.rol_id = 8;             // Ufinal
//      vm.model.sucursal_id = session.obtenerEmpleado'

      return api.post('/cliente', vm.model)
          .then(function (response){
            vm.message = response.data.message;
            pnotify.alert('Exito', vm.message, 'success');
            $state.go('clienteShow', {id: response.data.cliente.id});
          })
          .catch(function (response){
            vm.error = response.data;
            pnotify.alertList('No se pudo guardar el cliente', vm.error.error, 'error');
            return response;
          });
    }

  }

})();

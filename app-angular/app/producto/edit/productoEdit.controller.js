// app/producto/edit/productoEdit.controller.js

(function (){

  'use strict';

  angular
    .module('sagdApp.producto')
    .controller('productoEditController', ProductoEditController);

  ProductoEditController.$inject = ['$auth', '$state', '$stateParams', 'api', 'pnotify'];

  function ProductoEditController($auth, $state, $stateParams, api, pnotify){
    if (!$auth.isAuthenticated()) {
      $state.go('login', {});
    }

    var vm = this;
    vm.id = $stateParams.id;
    vm.sortKeys = [
      {name: 'Proveedor', key: 'clave'},
      {name: 'Costo', key: 'costo'},
      {name: 'P1', key: 'precio_1'},
      {name: 'P2', key: 'precio_2'},
      {name: 'P3', key: 'precio_3'},
      {name: 'P4', key: 'precio_4'},
      {name: 'P5', key: 'precio_5'},
      {name: 'P6', key: 'precio_6'},
      {name: 'P7', key: 'precio_7'},
      {name: 'P8', key: 'precio_8'},
      {name: 'P9', key: 'precio_9'},
      {name: 'P10', key: 'precio_10'}
    ];

    vm.updateClave = updateClave;
    vm.updateSubclave = updateSubclave;
    vm.save = guardarProducto;
    vm.sort = sort;
    vm.back = goBack;

    ///////////////////////////////////

    initialize();

    function initialize(){
      obtenerProducto()
        .then(function (){
          obtenerMarcas();
          obtenerSubfamilias();
          obtenerUnidades();
          obtenerTiposDeGarantias();
          obtenerMargenes();
        });

    }

    function obtenerProducto(){
      return api.get('/producto/', vm.id)
        .then(function (response){
          vm.producto = response.data.producto;
          vm.subfamilia = vm.producto.subfamilia;
          vm.producto.precios = response.data.precios_proveedor;
          console.log('Producto #' + vm.id + ' obtenido.');
          $state.go('productoEdit.details');
          return response.data;
        })
        .catch(function (response){
          vm.error = response.data;
          return response.data;
        });
    }

    function obtenerMarcas(){
      return api.get('/marca').then(function (response){
        vm.marcas = response.data;
        vm.marca = vm.marcas.filter(function (element){
          return vm.producto.marca_id == element.id;
        })[0];
        console.log('Marcas obtenidas correctamente');
      });
    }

    function obtenerSubfamilias(){
      return api.get('/subfamilia').then(function (response){
        vm.subfamilias = response.data;
        vm.subfamilia = vm.subfamilias.filter(function (element){
          return vm.producto.subfamilia_id == element.id;
        })[0];
        console.log('Subfamilias obtenidas');
      });
    }

    function obtenerUnidades(){
      return api.get('/unidad').then(function (response){
        vm.unidades = response.data;
        console.log('Unidades obtenidas correctamente');
      });
    }

    function obtenerTiposDeGarantias(){
      return api.get('/tipo-garantia').then(function (response){
        vm.tiposGarantia = response.data;
        console.log('Tipos de garantía obtenidos correctamente');
      });
    }

    function obtenerMargenes(){
      return api.get('/margen').then(function (response){
        vm.margenes = response.data;
        console.log('Margenes obtenidos correctamente');
      });
    }

    function updateSubclave(){
      if (vm.producto) {
        vm.producto.subclave = vm.producto.subclave || vm.producto.numero_parte || '';
        vm.producto.subclave = vm.producto.subclave.toUpperCase();
        updateClave();
      }
    }

    function updateClave(){
      var subfamilia = vm.subfamilia ? vm.subfamilia.clave : '';
      var familia = vm.subfamilia ? vm.subfamilia.familia.clave : '';
      var marca = vm.marca ? vm.marca.clave : '';

      vm.producto.clave = familia + subfamilia + marca + vm.producto.subclave;
      vm.producto.subfamilia_id = vm.subfamilia ? vm.subfamilia.id : null;
      vm.producto.marca_id = vm.marca ? vm.marca.id : null;
    }

    function guardarProducto(){
      return api.put('/producto/', vm.id, vm.producto)
        .then(function (response){
          vm.message = response.data.message;
          pnotify.alert('Exito', vm.message, 'success');
          return response;
        })
        .catch(function (response){
          vm.error = response.data;
          pnotify.alertList('No se pudo guardar el producto', vm.error.error, 'error');
          return response;
        });
    }

    //////// Utils /////////

    function sort(keyname){
      vm.sortKey = keyname;
      vm.reverse = !vm.reverse;
    }

    function goBack(){
      window.history.back();
    }
  }

})();
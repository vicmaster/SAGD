// app/blocks/session/session.module.js

(function() {
    'use strict';

    angular.module('blocks.session', []);
})();
// app/blocks/state/state.module.js

(function() {
  'use strict';

  angular.module('blocks.state', []);
})();

// app/blocks/utils/utils.module.js

(function() {
  'use strict';

  angular.module('blocks.utils', []);
})();

// app/core/core.module.js

(function() {
    'use strict';

    angular.module('sagdApp.core', [
        /*
         * Angular modules
         */
        'ngAnimate',

        /*
         * Our reusable cross app code modules
         */
        'blocks.session', 'blocks.state', 'blocks.utils',

        /*
         * 3rd party app modules
         */
        'ui.router',
        'satellizer'
    ]);
})();

// app/empleado/empleado.module.js

(function() {
    'use strict';

    angular.module('sagdApp.empleado', [
      'sagdApp.core'
    ]);
})();

// app/home/home.module.js

(function() {
    'use strict';

    angular.module('sagdApp.home', [
      'sagdApp.core',
      'satellizer'
    ]);
})();

// app/home/home.module.js

(function() {
    'use strict';

    angular.module('sagdApp.layout', [
      'sagdApp.core',
      'satellizer'
    ]);
})();

// app/navbar/navbar.module.js

(function() {
    'use strict';

    angular.module('sagdApp.navbar', [
      'sagdApp.core'
    ]);
})();

// app/producto/producto.module.js

(function() {
    'use strict';

    angular.module('sagdApp.producto', [
      'sagdApp.core'
    ]);
})();

// app/session/session.module.js

(function() {
    'use strict';

    angular.module('sagdApp.session', [
      'sagdApp.core'
    ]);
})();

// app.js

(function () {

  'use strict';

  angular
    .module('sagdApp', [
      'sagdApp.core',

      'sagdApp.layout',
      'sagdApp.home',
      'sagdApp.session',
      'sagdApp.empleado',
      'sagdApp.producto',
      'sagdApp.navbar'
  ]);
})();

// app/blocks/session/session.js

(function () {
  'use strict';

  angular
    .module('blocks.session')
    .factory('session', session);

  session.$inject = ['$auth', '$state', '$http'];

  function session($auth, $state, $http) {

    var auth = $auth;
    var state = $state;

    var loginError = false;
    var loginErrorText = '';

    var isAuthenticated = auth.isAuthenticated;

    var redirectToHomeIfAuthenticated = function () {
      if (isAuthenticated()) {
        state.go('home', {});
      }
    };

    var logoutUserIfAuthenticated = function () {
      if (isAuthenticated()) {
        auth.removeToken();
        localStorage.removeItem('empleado');
      }
    }

    var getEmpleado = function () {

      $http.get('http://api.sagd.app/api/v1/authenticate/empleado').then(setEmpleadoToLocalStorage);
    };

    var setEmpleadoToLocalStorage = function (response) {
      localStorage.setItem('empleado', JSON.stringify(response.data.empleado));
      $state.go('home',{});
    };

    var loginWithCredentials = function (credentials) {
      return auth.login(credentials).then(getEmpleado, function (error) {
        loginError = true;
        loginErrorText = error.data.error;
      });
    };


    var login = function (email, password) {
      redirectToHomeIfAuthenticated();
      var credentials = {
        email: email,
        password: password
      };
      return loginWithCredentials(credentials);
    };

    var logout = function () {
      logoutUserIfAuthenticated();
      state.go('login', {});
    };

    return {
      isAuthenticated: isAuthenticated,
      obtenerEmpleado: function () {
        return JSON.parse(localStorage.getItem('empleado'));
      },
      login: login,
      getLoginError: function(){
        return loginError;
      },
      cleanLoginError : function(){
        loginError = false;
      },
      getLoginErrorText: function(){
        return loginErrorText;
      },
      logout: logout
    };

  }
}());

// app/blocks/state/state.module.js

(function () {
  'use strict';

  angular
    .module('blocks.state')
    .factory('state', state);

  state.$inject = [];

  function state() {
    var fromState;
    var toState;

    var setNewState = function (from, to) {
      fromState = from;
      toState = to;
    };

    var getPreviousState = function () {
      return fromState || "home";
    };

    var getCurrentState = function () {
      return toState;
    };

    return {
      setNewState: setNewState,
      current_state: getCurrentState,
      previous_state: getPreviousState
    };
  }
}());

// app/blocks/utils/utils.module.js

(function () {
  'use strict';

  angular
    .module('blocks.utils')
    .factory('utils', utils);

  utils.$inject = [];

  function utils() {

    function pluck(collection, key) {
      var result = angular.isArray(collection) ? [] : {};

      angular.forEach(collection, function (val, i) {
        result[i] = angular.isFunction(key) ? key(val) : val[key];
      });
      return result;
    }

    return {
      pluck: pluck
    };

  }
}());

// app/core/config.js

(function () {
  'use strict';

  var core = angular.module('sagdApp.core');

  core.config(configure);

  configure.$inject = ['$urlRouterProvider', '$authProvider', '$locationProvider'];

  function configure($urlRouterProvider, $authProvider, $locationProvider) {
    var baseUrl = 'http://api.sagd.app/api/v1';
    $authProvider.loginUrl = baseUrl + '/authenticate';
    $authProvider.withCredentials = true;

    $urlRouterProvider.otherwise('/');

    if (window.history && window.history.pushState) {
      $locationProvider.html5Mode(true).hashPrefix('!');
    }
  }

  core.run(updateState);

  updateState.$inject = ['$rootScope', '$state', 'state'];

  function updateState($rootScope, $state, state) {
    $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState, fromParams) {
      state.setNewState(fromState.name, toState.name);
    });
  }

})();

// app/empleado/config.route.js

(function () {
  'use strict';

  angular
    .module('sagdApp.empleado')
    .config(configureRoutes);

  configureRoutes.$inject = ['$stateProvider'];

  function configureRoutes($stateProvider) {
    $stateProvider
      .state('empleado', {
        url: 'empleado',
        parent: 'layout',
        templateUrl: 'app/empleado/empleado.html',
        controller: 'EmpleadoController',
        controllerAs: 'vm'
      });
  }
})();

// app/empleado/empleado.controller.js

(function () {

  'use strict';

  angular
    .module('sagdApp.empleado')
    .controller('EmpleadoController', EmpleadoController);

  EmpleadoController.$inject = ['$http', '$auth', '$state'];

  function EmpleadoController($http, $auth, $state) {
    if(! $auth.isAuthenticated()){
      $state.go('login', {});
    }

    var vm = this;

    vm.empleados;
    vm.errores;

    vm.isAuthenticated = function () {
      return $auth.isAuthenticated();
    }

    vm.getEmpleados = function () {

      // This request will hit the index method in the AuthenticateController
      // on the Laravel side and will return the list of users
      $http.get('http://api.sagd.app/api/v1/empleado').success(function (empleados) {
        vm.empleados = empleados;
      }).error(function (error) {
        vm.errores = error;
      });
    }
  }

})();

// app/home/config.route.js

(function() {
    'use strict';

    angular
        .module('sagdApp.home')
        .config(configureRoutes);

    configureRoutes.$inject = ['$stateProvider'];

    function configureRoutes($stateProvider) {
      $stateProvider
          .state('home', {
              url: '',
              parent: 'layout',
              templateUrl: 'app/home/home.html',
              controller: 'HomeController',
              controllerAs: 'vm'
          });
    }
})();

// app/home/home.controller.js

(function (){

  'use strict';

  angular
    .module('sagdApp.home')
    .controller('HomeController', HomeController);

  HomeController.$inject = ['$auth', '$state'];

  function HomeController($auth, $state) {
    if(! $auth.isAuthenticated()){
      $state.go('login', {});
    }
  }
})();

// app/layout/config.route.js

(function () {
  'use strict';

  angular
    .module('sagdApp.layout')
    .config(configureRoutes);

  configureRoutes.$inject = ['$stateProvider'];

  function configureRoutes($stateProvider) {
    $stateProvider
      .state('layout', {
        url: '/',
        templateUrl: 'app/layout/layout.html',
        abstract: true,
        controller: 'layoutController',
        controllerAs: 'vm'
      });
  }
})();

// app/home/home.controller.js

(function () {

  'use strict';

  angular
    .module('sagdApp.layout')
    .controller('layoutController', LayoutController);

  LayoutController.$inject = ['$auth', '$state'];

  function LayoutController($auth, $state) {

  }
})();

// app/navbar/navbar.directive.js

(function () {

  'use strict';

  angular
    .module('sagdApp.navbar')
    .directive('logout', function () {
      return {
        templateUrl: 'app/session/logout.html'
      };
    });
})();

// app/navbar/navbar.animations.js

(function () {

  'use strict';

  angular
    .module('sagdApp.navbar')
    .animation('.module-navbar', NavbarAnimation);

  NavbarAnimation.$inject = [];

  function NavbarAnimation() {

    var showSubmenu = function (element, className, done) {
      var menu = $(element).children('.menu');
      $(menu).addClass('active');
    };

    var hideSubmenu = function (element, className, done) {
      var menu = $(element).children('.menu');
      $(menu).removeClass('active');
    };

    var setActive = function (element, className, done) {
      $('li.module-navbar').each(function () {
        $(this).removeClass('active');
      });
      $(element).addClass('active');
    };

    var addClass = function(element, className, done) {
      if (className === 'active') {
        setActive(element, className, done);
      } else {
        showSubmenu(element, className, done);
      }
    };

    return {
      addClass: addClass,
      removeClass: hideSubmenu
    };
  }

})();

// app/navbar/navbar.controller.js

(function () {

  'use strict';

  angular
    .module('sagdApp.navbar')
    .controller('NavbarController', NavbarController)
    .directive('navBar', function () {
      return {
        templateUrl: 'app/navbar/navbar.html'
      };
    });

  NavbarController.$inject = ['session', 'state', 'utils'];

  function NavbarController(session, state, utils) {
    var vm = this;
    vm.modules = [
      {
        nombre: 'Inicio',
        state: 'home',
        active: false,
        submodules: [
          {
            nombre: 'Inicio',
            state: 'home'
          }
        ]
      }, {
        nombre: 'Productos',
        state: 'producto',
        active: false,
        submodules: [
          {
            nombre: 'Cátalogo',
            state: 'producto/catalogo',
          },{
            nombre: 'Marcas',
            state: 'producto/marca',
          },{
            nombre: 'Familias',
            state: 'producto/familia',
          },{
            nombre: 'Subfamilias',
            state: 'producto/subfamilias',
          }
        ]
      }, {
        nombre: 'Inventario',
        state: 'inventario',
        active: false,
        submodules: [
          {
            nombre: 'Transferencias',
            state: 'producto/transferencia',
          },{
            nombre: 'Un nombre muy muy largo',
            state: 'producto/test',
          }
        ]
      }, {
        nombre: 'Ventas',
        state: 'venta',
        active: false,
        submodules: [
          {
            nombre: 'Ventas',
            state: 'venta',
          },{
            nombre: 'Consultar',
            state: 'venta/consulta',
          },{
            nombre: 'Cotizaciones',
            state: 'venta/cotizacion',
          },{
            nombre: 'Utilidad',
            state: 'venta/utilidad',
          }
        ]
      }, {
        nombre: 'Clientes',
        state: 'cliente',
        active: false,
        submodules: [
          {
            nombre: 'Listar',
            state: 'cliente',
          }
        ]
      }, {
        nombre: 'Proveedores',
        state: 'proveedor',
        active: false,
        submodules: [
          {
            nombre: 'Listar',
            state: 'proveedor',
          }
        ]
      }, {
        nombre: 'Soporte',
        state: 'soporte',
        active: false,
        submodules: [
          {
            nombre: 'Listar',
            state: 'soporte',
          }
        ]
      }, {
        nombre: 'Empleados',
        state: 'empleado',
        active: false,
        submodules: [
          {
            nombre: 'Listar',
            state: 'empleado',
          }
        ]
      }, {
        nombre: 'Cajas y Cortes',
        state: 'caja_corte',
        active: false,
        submodules: [
          {
            nombre: 'Listar cajas',
            state: 'caja_corte/caja'
          },{
            nombre: 'Corte de Caja',
            state: 'caja_corte',
          }
        ]
      }, {
        nombre: 'Paqueterías',
        state: 'paqueteria',
        active: false,
        submodules: [
          {
            nombre: 'Listar',
            state: 'paqueteria'
          }
        ]
      } , {
        nombre: 'Facturación',
        state: 'facturacion',
        active: false,
        submodules: [
          {
            nombre: 'Consultar',
            state: 'facturacion'
          },{
            nombre: 'Reportes',
            state: 'facturacion/reporte'
          },{
            nombre: 'Efectivo',
            state: 'facturacion/efectivo'
          },{
            nombre: 'Administrar folios',
            state: 'facturacion/folio'
          },{
            nombre: 'VM',
            state: 'facturacion/VM'
          },
        ]
      } , {
        nombre: 'Configuración',
        state: 'configuracion',
        active: false,
        submodules: [
          {
            nombre: 'Sucursales',
            state: 'configuracion/sucursal'
          },{
            nombre: 'Logs',
            state: 'configuracion/logs'
          },{
            nombre: 'PM',
            state: 'configuracion/pm'
          },{
            nombre: 'Tipo de Cambio',
            state: 'configuracion/tipocambio'
          }
        ]
      }
    ];

    vm.setActiveState = function () {
      var current_state = state.current_state();
      var states = utils.pluck(vm.modules, "state");
      var index = states.indexOf(current_state);
      vm.modules[index].active = true;
    }
    vm.setActiveState();

    vm.clicked = function($event) {
      $('li.module-navbar').each(function () {
        $(this).removeClass('active');
      });
      $($event.currentTarget).addClass('active');
    }

    vm.expandMenu = function($event) {
      MobileNavbarAnimations.animate($event);
    }

    vm.toggleMenu = function($event) {
      MobileNavbarAnimations.toggleMenu($event);
    }

    vm.isAuthenticated = session.isAuthenticated;
    vm.empleado = session.obtenerEmpleado();
    vm.logout = session.logout;
  }

  var MobileNavbarAnimations = (function() {
    var currentTarget;
    var currentTargetHeight;
    var target;
    var targetHeight;
    var mobileModuleBaseHeight = 65;
    var mobileNavbarHidden;

    var animateMenu = function($event) {
      currentTarget = $event.currentTarget;
      target = $(currentTarget).children('.mobile-menu');
      calculateHeights();
      applyAnimation();
    }

    var calculateHeights = function() {
      currentTargetHeight = $(currentTarget).outerHeight();
      targetHeight = $(target).outerHeight();
    }

    var applyAnimation = function() {
      if( currentTargetHeight > mobileModuleBaseHeight ){
        collapseMenu();
      } else {
        expandMenu();
      }
    }

    var collapseMenu = function() {
      animate(mobileModuleBaseHeight, false, 0, -100);
    }

    var expandMenu = function() {
      var newMobileMenuHeight = currentTargetHeight + targetHeight;
      animate(newMobileMenuHeight, true, 1, 0);
    }

    var animate = function(newMobileMenuHeight, display, opacityValue, translateValue) {
      $(currentTarget).css({
        'max-height': newMobileMenuHeight
      })
      $(target).css({
        'opacity': opacityValue,
        'transform': 'translateY('+ translateValue +'%)'
      });
      $(target).css({
        'display': display ? 'block' : 'none'
      });
    }

    var toggleMenu = function($event) {
      currentTarget = $event.currentTarget;
      mobileNavbarHidden = $('.hamburguer').data('hidden');
      slideMenu();
      rotateHamburguer();
      $('.hamburguer').data('hidden', !mobileNavbarHidden);
    }

    var slideMenu = function() {
      $('.mobile-navbar').css({
        'display': mobileNavbarHidden ? 'block' : 'none',
        'transform': 'translateX('+ (mobileNavbarHidden ? '0':'-100') +'%)'
      });
    }

    var rotateHamburguer = function() {
      $('.hamburguer').css({
        'transform': 'rotateZ('+ (mobileNavbarHidden ? '90':'0') +'deg)'
      });
    }

    return {
      animate : animateMenu,
      toggleMenu : toggleMenu
    }
  })();

})();

// app/producto/config.route.js

(function() {
    'use strict';

    angular
        .module('sagdApp.producto')
        .config(configureRoutes);

    configureRoutes.$inject = ['$stateProvider'];

    function configureRoutes($stateProvider) {
        $stateProvider
            .state('producto', {
                url: 'producto',
                parent: 'layout',
                templateUrl: 'app/producto/producto.html',
                controller: 'productoController',
                controllerAs: 'vm'
            });
    }
})();

// app/producto/producto.controller.js

(function () {

  'use strict';

  angular
    .module('sagdApp.producto')
    .controller('productoController', ProductoController);

  ProductoController.$inject = ['$auth', '$state'];

  function ProductoController($auth, $state) {
    if(! $auth.isAuthenticated()){
      $state.go('login',{});
    }
  }

})();

// app/session/config.route.js

(function() {
    'use strict';

    angular
        .module('sagdApp.session')
        .config(configureRoutes);

    configureRoutes.$inject = ['$stateProvider'];

    function configureRoutes($stateProvider) {
        $stateProvider
            .state('login', {
                url: '/login',
                templateUrl: 'app/session/login.html',
                controller: 'SessionController',
                controllerAs: 'vm'
            })
            .state('logout', {
                url: '/logout',
                templateUrl: 'app/session/logout.html',
                controller: 'SessionController',
                controllerAs: 'vm'
            });
    }
})();

// app/session/session.controller.js

(function () {

  'use strict';

  angular
    .module('sagdApp.session')
    .controller('SessionController', SessionController);

  SessionController.$inject = ['session'];

  function SessionController(session) {
    var vm = this;

    vm.login = function () {
      session.login(vm.email, vm.password).then(function(){
        vm.loginError = session.getLoginError();
      });
    };

    vm.logout = function () {
      session.logout();
    };

    vm.cleanLoginError = function(evt){
      session.cleanLoginError();
      vm.loginError = session.getLoginError();
    };

  }

})();
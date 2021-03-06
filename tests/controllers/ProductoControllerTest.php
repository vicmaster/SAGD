<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

/**
 * @coversDefaultClass \App\Http\Controllers\Api\V1\ProductoController
 */
class ProductoControllerTest extends TestCase {

    use WithoutMiddleware;

    protected $endpoint = '/v1/producto';

    public function setUp() {
        parent::setUp();
        $this->mock = $this->setUpMock('App\Producto');
    }

    public function setUpMock($class) {
        $mock = Mockery::mock($class);

        return $mock;
    }

    public function tearDown() {
        Mockery::close();
    }

    /**
     * @covers ::index
     */
    public function test_GET_index() {
        $this->mock->shouldReceive(
            ['with' => Mockery::self(),
             'get'  => 'success']
        )->once()->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);
        $this->get($this->endpoint)
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::index
     */
    public function test_GET_index_precio_no_revisado() {
        $this->mock->shouldReceive([
            'whereHas' => Mockery::self(),
            'get' => ['producto']
        ])->once()->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);
        $this->get($this->endpoint . '?revisados=true')
            ->seeJson(['producto'])
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::store
     */
    public function test_POST_store() {
        $this->mock
            ->shouldReceive([
                'fill'         => Mockery::self(),
                'guardarNuevo' => true,
                'self'         => 'self',
                'getId'        => 1
            ])
            ->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->post($this->endpoint, ['producto' => ['upc' => 123456]])
            ->seeJson([
                'message'  => 'Producto creado exitosamente',
                'producto' => 'self'
            ])
            ->assertResponseStatus(201);
    }

    /**
     * @covers ::store
     */
    public function test_POST_store_bad_data() {
        $this->mock
            ->shouldReceive([
                'fill'         => Mockery::self(),
                'guardarNuevo' => false
            ])->withAnyArgs();
        $this->mock->errors = ['clave' => 'Clave es requerido'];
        $this->app->instance('App\Producto', $this->mock);

        $this->post($this->endpoint, ['producto' => ['upc' => 123456]])
            ->seeJson([
                'message' => 'Producto no creado',
                'error'   => ['clave' => 'Clave es requerido']
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @covers ::show
     */
    public function test_GET_show_ok() {
        $endpoint = $this->endpoint . '/1';

        $this->mock->shouldReceive([
            'with'             => Mockery::self(),
            'find'             => Mockery::self(),
            'self'             => 'self',
            'preciosProveedor' => 'precios'
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);


        $this->get($endpoint)
            ->seeJson([
                'message'           => 'Producto obtenido exitosamente',
                'producto'          => 'self',
                'precios_proveedor' => 'precios'
            ])
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::show
     */
    public function test_GET_show_no_encontrado() {
        $endpoint = $this->endpoint . '/10000';

        $this->mock->shouldReceive([
            'with' => Mockery::self(),
            'find' => false,
            'self' => 'self'
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->seeJson([
                'message' => 'Producto no encontrado o no existente',
                'error'   => 'No encontrado'
            ])
            ->assertResponseStatus(404);

    }

    /**
     * @covers ::update
     */
    public function test_PUT_update_ok() {
        $endpoint = $this->endpoint . '/1';
        $parameters = [
            'upc'       => 1234567890,
            'dimension' => [
                'largo' => '10.00'
            ],
            'precios'   => [
                [
                    'id'    => 1,
                    'clave' => 'DICO',
                    'costo' => "30.00"
                ],
                [
                    'id'    => 5,
                    'clave' => 'INGRAM',
                    'costo' => "90.00"
                ]
            ]
        ];

        $this->mock->shouldReceive([
            'find'       => Mockery::self(),
            'actualizar' => true,
        ])->withAnyArgs();

        $this->app->instance('App\Producto', $this->mock);

        $this->put($endpoint, $parameters)
            ->seeJson([
                'message' => 'Producto se actualizo correctamente'
            ])
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::update
     */
    public function test_PUT_update_no_encontrado() {
        $endpoint = $this->endpoint . '/1';
        $parameters = ['upc' => 123456];

        $this->mock->shouldReceive([
            'find' => null,
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->put($endpoint, $parameters)
            ->seeJson([
                'message' => 'No se pudo realizar la actualizacion del producto',
                'error'   => 'Producto no encontrado'
            ])
            ->assertResponseStatus(404);
    }

    /**
     * @covers ::update
     */
    public function test_PUT_update_clave_repetida() {
        $endpoint = $this->endpoint . '/1';
        $parameters = ['upc' => 14569];

        $this->mock->shouldReceive([
            'find'       => Mockery::self(),
            'actualizar' => false
        ])->withAnyArgs();
        $this->mock->errors = ['clave' => 'La clave ya existe'];
        $this->app->instance('App\Producto', $this->mock);

        $this->put($endpoint, $parameters)
            ->seeJson([
                'message' => 'No se pudo realizar la actualizacion del producto',
                'error'   => ['clave' => 'La clave ya existe']
            ])->assertResponseStatus(400);
    }

    /**
     * @covers ::destroy
     */
    public function test_DELETE_destroy_ok() {
        $endpoint = $this->endpoint . '/10';

        $this->mock->shouldReceive([
            'find'   => Mockery::self(),
            'delete' => true
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->delete($endpoint)
            ->seeJson([
                'message' => 'Producto eliminado correctamente'
            ])
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::destroy
     */
    public function test_DELETE_destroy_not_found() {
        $endpoint = $this->endpoint . '/1';

        $this->mock->shouldReceive([
            'find' => null,
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->delete($endpoint)
            ->seeJson([
                'message' => 'No se pudo eliminar el producto',
                'error'   => 'Producto no encontrado'
            ])
            ->assertResponseStatus(404);
    }

    /**
     * @covers ::destroy
     */
    public function test_DELETE_destroy_bad() {
        $endpoint = $this->endpoint . '/1';

        $this->mock->shouldReceive([
            'find'   => Mockery::self(),
            'delete' => false,
        ])->withAnyArgs();
        $this->mock->errors = 'Metodo de eliminar no se pudo ejecutar';
        $this->app->instance('App\Producto', $this->mock);

        $this->delete($endpoint)
            ->seeJson([
                'message' => 'No se pudo eliminar el producto',
                'error'   => 'Metodo de eliminar no se pudo ejecutar'
            ])
            ->assertResponseStatus(400);

    }

    /**
     * @covers ::buscarUpc
     * @group feature-salidas
     */
    public function testBuscarPorUpc()
    {
        $endpoint = $this->endpoint . '/buscar/upc/123123';

        $this->mock->shouldReceive([
            'where->get' => Mockery::self(),
            'count' => 1,
            'first' => []
        ]);
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->seeJson([
                'message' => 'Producto encontrado',
                'producto' => []
            ])
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::buscarUpc
     * @group feature-salidas
     */
    public function testBuscarPorUpcInvalido()
    {
        $endpoint = $this->endpoint . '/buscar/upc/123123';

        $this->mock->shouldReceive([
            'where->get' => Mockery::self(),
            'count' => 0
        ]);
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->seeJson([
                'message' => 'Producto no encontrado',
                'error' => 'Producto no existente'
            ])
            ->assertResponseStatus(404);
    }

    /**
     * @covers ::indexExistencias
     * @group feature-transferencias
     */
    public function testGetIndexExistencias()
    {
        $endpoint = $this->endpoint . '/1/existencias';

        $this->mock->shouldReceive([
            'leftJoin->join->join->where->where->get' => true
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->seeJson([
                'message' => 'Productos con existencias obtenidas exitosamente',
                'productos' => true
            ])
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::indexExistencias
     * @group feature-transferencias
     */
    public function testGetIndexExistenciasProductoNoEncontrado()
    {
        $endpoint = $this->endpoint . '/1/existencias';

        $this->mock->shouldReceive([
            'leftJoin->join->join->where->where->get' => false
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->seeJson([
                'message' => 'Las existencias del producto que solicitaste no se encontraron.',
                'error' => 'Producto no encontrado'
            ])
            ->assertResponseStatus(404);
    }

    /**
     * @covers ::pretransferir
     * @group feature-transferencias
     */
    public function testPostPretransferir()
    {
        $endpoint = $this->endpoint . '/1/existencias/pretransferir';
        $params = [];

        $this->mock->shouldReceive([
            'find' => Mockery::self(),
            'pretransferir' => true
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->post($endpoint, $params)
            ->seeJson([
                'message' => 'Pretransferencias registradas exitosamente'
            ])
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::pretransferir
     * @group feature-transferencias
     */
    public function testPostPretransferirFindProductoFails()
    {
        $endpoint = $this->endpoint . '/1/existencias/pretransferir';
        $params = [];

        $this->mock->shouldReceive([
            'find' => false
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->post($endpoint, $params)
            ->seeJson([
                'message' => 'La pretransferencia no se registro debido a que no se encontro el producto',
                'error' => 'Producto no encontrado'
            ])
            ->assertResponseStatus(404);
    }

    /**
     * @covers ::pretransferir
     * @group feature-transferencias
     */
    public function testPostPretransferirFalla()
    {
        $endpoint = $this->endpoint . '/1/existencias/pretransferir';
        $params = [];

        $this->mock->shouldReceive([
            'find' => Mockery::self(),
            'pretransferir' => false
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->post($endpoint, $params)
            ->seeJson([
                'message' => 'La pretransferencia no se registro debido a un error interno. Las existencias no se modificaron',
                'error' => 'Pretransferencia fallo'
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @covers ::indexMovimientos
     * @group feature-movimientos
     */
    public function testIndexMovimientos()
    {
        $endpoint = $this->endpoint . '/1/movimientos/sucursal/1';

        $this->mock->shouldReceive([
            'select->join->join->where->where->orderBy->get' => true
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->seeJson([
                'message' => 'Productos con movimientos obtenidos exitosamente'
            ])
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::indexMovimientos
     * @group feature-movimientos
     */
    public function testIndexMovimientosNotFount()
    {
        $endpoint = $this->endpoint . '/1/movimientos/sucursal/1';

        $this->mock->shouldReceive([
            'select->join->join->where->where->orderBy->get' => null
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->seeJson([
                'message' => 'Los movimientos del producto que solicitaste no se encontraron.',
                'error' => 'Producto no encontrado'
            ])
            ->assertResponseStatus(404);
    }

	/**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductoConTodosLosParametros()
    {
        $endpoint = '/v1/productos/buscar/?clave=A&descripcion=A&numeroParte=A&upc=1';

        $this->mock->shouldReceive([
            'where' => Mockery::self(),
            'whereHas' => Mockery::self(),
            'get' => []
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductoSoloPorClave()
    {
        $endpoint = '/v1/productos/buscar/?clave=A&descripcion=*&numeroParte=*&upc=*';

        $this->mock->shouldReceive([
            'where' => Mockery::self(),
            'whereHas' => Mockery::self(),
            'get' => []
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductoSoloConDescripcion()
    {
        $endpoint = '/v1/productos/buscar/?clave=*&descripcion=A&numeroParte=*&upc=*';

        $this->mock->shouldReceive([
            'where' => Mockery::self(),
            'whereHas' => Mockery::self(),
            'get' => []
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->assertResponseStatus(200);
    }
    /**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductoSoloConNumeroDeParte()
    {
        $endpoint = '/v1/productos/buscar/?clave=*&descripcion=*&numero_parte=A&upc=*';

        $this->mock->shouldReceive([
            'where' => Mockery::self(),
            'whereHas' => Mockery::self(),
            'get' => []
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->assertResponseStatus(200);
    }


    /**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductoSoloConUpc()
    {
        $endpoint = '/v1/productos/buscar/?clave=*&descripcion=*&numero_parte=*&upc=1';

        $this->mock->shouldReceive([
            'where' => Mockery::self(),
            'whereHas' => Mockery::self(),
            'get' => []
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductoSinEspecificarNinguno()
    {
        $endpoint = '/v1/productos/buscar/?clave=*&descripcion=*&numero_parte=*&upc=*';

        $this->get($endpoint)
            ->seeJson([
                'message' => 'Debes de especificar al menos un valor de busqueda',
                'error' => 'Busqueda muy larga'
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductoSinEnviarUnParametro()
    {
        $endpoint = '/v1/productos/buscar/?clave=A&descripcion=A&numero_parte=A';

        $this->mock->shouldReceive([
            'where' => Mockery::self(),
            'whereHas' => Mockery::self(),
            'get' => []
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductosEstaPorDefaultSoloConExistencia() {
        $endpoint = '/v1/productos/buscar/?clave=A&descripcion=A&numero_parte=A&upc=A';

        $this->mock->shouldReceive([
            'where' => Mockery::self(),
            'whereHas' => Mockery::self(),
            'get' => []
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductosSoloConExistencia() {
        $endpoint = '/v1/productos/buscar/?clave=A&descripcion=A&numero_parte=A&upc=A&existencia=true';

        $this->mock->shouldReceive([
            'where' => Mockery::self(),
            'whereHas' => Mockery::self(),
            'get' => []
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::buscar
     * @group feature-buscador-productos
     */
    public function testBuscarProductosIncluirSinExistencia() {
        $endpoint = '/v1/productos/buscar/?clave=A&descripcion=A&numero_parte=A&upc=A&existencia=0';

        $this->mock->shouldReceive([
            'where' => Mockery::self(),
            'get' => []
        ])->withAnyArgs();
        $this->app->instance('App\Producto', $this->mock);

        $this->get($endpoint)
            ->assertResponseStatus(200);
    }

    /**
     * @covers ::entradas
     */
    public function testObtenerEntradasParaUnProducto() {
        $producto_id = 10;
        $endpoint = "/v1/producto/{$producto_id}/entradas";

        $this->app->instance('App\Producto', $this->mock);

        $this->mock->shouldReceive('find')->with($producto_id)->andReturn(Mockery::self());
        $this->mock->shouldReceive('entradasDetalles')->withNoArgs()->andReturn(Mockery::self());
        $this->mock->shouldReceive('groupBy')->with('entrada_id')->andReturn(Mockery::self());
        $this->mock->shouldReceive('with')->with('entrada.sucursal','entrada.proveedor')->andReturn(Mockery::self());
        $this->mock->shouldReceive('get')->withNoArgs()->andReturn('success');

        $this->get($endpoint)->seeJson([
            'message'  => 'Entradas obtenidas correctamente.',
            'entradas' => 'success'
        ])->assertResponseOk();
    }

    /**
     * @covers ::entradas
     */
    public function testObtenerEntradasParaUnProductoFailed() {
        $producto_id = 10;
        $endpoint = "/v1/producto/{$producto_id}/entradas";

        $this->app->instance('App\Producto', $this->mock);

        $this->mock->shouldReceive('find')->with($producto_id)->andReturnNull();

        $this->get($endpoint)->seeJson([
            'message' => 'No se pudieron obtener las entradas.',
            'error'   => 'Producto no encontrado.'
        ])->assertResponseStatus(404);
    }
}

<?php

/**
 * @coversDefaultClass \App\Entrada
 */
class EntradaTest extends TestCase {

    /**
     * @coversNothing
     */
    public function testModeloEsValido()
    {
        $entrada = factory(App\Entrada::class)->make();
        $this->assertTrue($entrada->isValid());
    }

    /**
     * @coversNothing
     * @group modelo_actualizable
     */
    public function testModeloEsActualizable()
    {
        $entrada = factory(App\Entrada::class, 'full')->create();
        $entrada->moneda = 'MC Hammer';
        $this->assertTrue($entrada->isValid('update'));
        $this->assertTrue($entrada->save());
        $this->assertSame('MC Hammer', $entrada->moneda);
    }

    /**
     * @coversNothing
     */
    public function testFacturaExternaNumeroEsObligatorio()
    {
        $entrada = factory(App\Entrada::class)->make(['factura_externa_numero' => null]);
        $this->assertFalse($entrada->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFacturaExternaNumeroNoPuedeSerLargo()
    {
        $entrada = factory(App\Entrada::class, 'longfen')->make();
        $this->assertFalse($entrada->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFacturaFechaNoEsObligatorio()
    {
        $entrada = factory(App\Entrada::class)->make(['factura_fecha' => null]);
        $this->assertTrue($entrada->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFacturaFechaEsTimestamp()
    {
        $entrada = factory(App\Entrada::class)->make(['factura_fecha' => 'a']);
        $this->assertFalse($entrada->isValid());
    }

    /**
     * @coversNothing
     */
    public function testMonedaEsObligatorio()
    {
        $entrada = factory(App\Entrada::class)->make(['moneda' => null]);
        $this->assertFalse($entrada->isValid());
    }

    /**
     * @coversNothing
     */
    public function testMonedaNoPuedeSerLarga()
    {
        $entrada = factory(App\Entrada::class, 'longmoneda')->make();
        $this->assertFalse($entrada->isValid());
    }

    /**
     * @coversNothing
     */
    public function testTipoDeCambioEsObligatorio()
    {
        $entrada = factory(App\Entrada::class)->make(['tipo_cambio' => null]);
        $this->assertFalse($entrada->isValid());
    }

    /**
     * @coversNothing
     */
    public function testTipoDeCambioEsDecimal()
    {
        $entrada = factory(App\Entrada::class)->make(['tipo_cambio' => 'a']);
        $this->assertFalse($entrada->isValid());
    }

    /**
     * @covers ::estado
     * @group relaciones
     */
    public function testEstado()
    {
        $entrada = factory(App\Entrada::class, 'full')->make();
        $estado = $entrada->estado;
        $this->assertInstanceOf(App\EstadoEntrada::class, $estado);
    }

    /**
     * @covers ::estado
     * @group relaciones
     */
    public function testEstadoAssociate()
    {
        $entrada = factory(App\Entrada::class, 'noestado')->make(['estado_entrada_id' => null]);
        $estado = factory(App\EstadoEntrada::class)->create();
        $entrada->estado()->associate($estado);
        $entrada->save();
        $this->assertInstanceOf(App\EstadoEntrada::class, $entrada->estado);
        $this->assertSame(1, count($entrada->estado));
    }

    /**
     * @covers ::proveedor
     * @group relaciones
     */
    public function testProveedor()
    {
        $entrada = factory(App\Entrada::class, 'full')->make();
        $proveedor = $entrada->proveedor;
        $this->assertInstanceOf(App\Proveedor::class, $proveedor);
    }

    /**
     * @covers ::proveedor
     * @group relaciones
     */
    public function testProveedorAssociate()
    {
        $entrada = factory(App\Entrada::class, 'noproveedor')->make();
        $proveedor = factory(App\Proveedor::class)->create();
        $entrada->proveedor()->associate($proveedor);
        $entrada->save();
        $this->assertInstanceOf(App\Proveedor::class, $entrada->proveedor);
        $this->assertSame(1, count($entrada->proveedor));
    }

    /**
     * @covers ::razonSocial
     * @group relaciones
     */
    public function testRazonSocial()
    {
        $entrada = factory(App\Entrada::class, 'full')->make();
        $rse = $entrada->razonSocial;
        $this->assertInstanceOf(App\RazonSocialEmisor::class, $rse);
    }

    /**
     * @covers ::razonSocial
     * @group relaciones
     */
    public function testRazonSocialAssociate()
    {
        $entrada = factory(App\Entrada::class, 'norazonsocial')->make();
        $rse = factory(App\RazonSocialEmisor::class, 'full')->create();
        $entrada->razonSocial()->associate($rse);
        $entrada->save();
        $this->assertInstanceOf(App\RazonSocialEmisor::class, $entrada->razonSocial);
        $this->assertSame(1, count($entrada->razonSocial));
    }

    /**
     * @covers ::empleado
     * @group relaciones
     */
    public function testEmpleado()
    {
        $entrada = factory(App\Entrada::class, 'full')->make();
        $empleado = $entrada->empleado;
        $this->assertInstanceOf(App\Empleado::class, $empleado);
    }

    /**
     * @covers ::empleado
     * @group relaciones
     */
    public function testEmpleadoAssociate()
    {
        $entrada = factory(App\Entrada::class, 'noempleado')->make();
        $empleado = factory(App\Empleado::class)->create();
        $entrada->empleado()->associate($empleado);
        $entrada->save();
        $this->assertInstanceOf(App\Empleado::class, $entrada->empleado);
        $this->assertSame(1, count($entrada->empleado));
    }

    /**
     * @covers ::detalles
     * @group relaciones
     */
    public function testDetalles()
    {
        $entrada = factory(App\Entrada::class, 'full')->create();
        $ed = factory(App\EntradaDetalle::class, 'full')->create(['entrada_id' => $entrada->id]);
        $detalles = $entrada->detalles;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $detalles);
        $this->assertInstanceOf(App\EntradaDetalle::class, $detalles[0]);
        $this->assertCount(1, $detalles);
    }

    /**
     * @covers ::crearDetalle
     * @group feature-entradas
     */
    public function testCrearDetalleConParametrosDeDetalleEsExitoso()
    {
        $producto = $this->setUpProducto();
        $sucursal = App\Sucursal::last();
        $entrada = $this->setUpEntrada();
        $detalle = [
            'costo' => 1.0,
            'cantidad' => 1,
            'importe' => 1.0,
            'producto_id' => $producto->id,
            'sucursal_id' => $sucursal->id,
            'upc' => $producto->upc
        ];

        $this->assertInstanceOf(App\EntradaDetalle::class, $entrada->crearDetalle($detalle));
    }

    /**
     * @covers ::crearDetalle
     * @group feature-entradas
     */
    public function testCrearDetalleConParametroIncorrectoNoEsExitoso()
    {
        $producto = $this->setUpProducto();
        $sucursal = App\Sucursal::last();
        $entrada = $this->setUpEntrada();
        $detalle = [
            'costo' => 1.0,
            'cantidad' => -1,
            'importe' => 1.0,
            'producto_id' => $producto->id,
            'sucursal_id' => $sucursal->id,
            'upc' => $producto->upc
        ];

        $this->assertFalse($entrada->crearDetalle($detalle));
    }

    /**
     * @covers ::crearDetalle
     * @group feature-entradas
     */
    public function testCrearDetalleConParametrosFaltantesNoExitoso()
    {
        $producto = $this->setUpProducto();
        $sucursal = App\Sucursal::last();
        $entrada = $this->setUpEntrada();
        $detalle = [
            'costo' => 1.0,
            'importe' => 1.0,
            'producto_id' => $producto->id,
            'sucursal_id' => $sucursal->id,
            'upc' => $producto->upc
        ];

        $this->assertFalse($entrada->crearDetalle($detalle));
    }

    /**
     * @covers ::quitarDetalle
     * @group feature-entradas
     */
    public function testQuitarDetalleConDetalleCorrectoEsExitoso()
    {
        $producto = $this->setUpProducto();
        $entrada = $this->setUpEntrada();
        $this->setUpDetalle();

        $detalle = App\EntradaDetalle::last()->id;

        $this->assertTrue($entrada->quitarDetalle($detalle));
    }

    /**
     * @covers ::quitarDetalle
     * @group feature-entradas
     */
    public function testQuitarDetalleConDetalleIncorrectoNoEsExitoso()
    {
        $producto = $this->setUpProducto();
        $entrada = $this->setUpEntrada();
        $this->setUpDetalle();

        $detalle = App\EntradaDetalle::last()->id + 1;

        $this->assertFalse($entrada->quitarDetalle($detalle));
    }

    private function setUpProducto()
    {
        $producto = factory(App\Producto::class)->create();
        $sucursal = factory(App\Sucursal::class)->create();
        $producto->addSucursal($sucursal);

        $productoSucursal = $producto->productosSucursales()->where('sucursal_id', $sucursal->id)->first();
        $productoSucursal->existencia()->create([
            'cantidad' => 100,
            'cantidad_apartado' => 0,
            'cantidad_pretransferencia' => 0,
            'cantidad_transferencia' => 0,
            'cantidad_garantia_cliente' => 0,
            'cantidad_garantia_zegucom' => 0
        ]);
        return $producto;
    }

    private function setUpEntrada()
    {
        $this->setUpEstados();
        $producto = App\Producto::last();
        $sucursal = App\Sucursal::last();


        $entrada = new App\Entrada([
            'factura_externa_numero' => 'ABDC-1234-XXXX',
            'moneda' => 'PESOS',
            'tipo_cambio' => 1.00,
            'estado_entrada_id' => App\EstadoEntrada::creando()->id,
            'proveedor_id' => $sucursal->proveedor_id,
            'empleado_id' => factory(App\Empleado::class)->create(['sucursal_id' => $sucursal->id])->id,
            'razon_social_id' => factory(App\RazonSocialEmisor::class, 'full')->create()->id
        ]);
        $entrada->save();
        return $entrada;
    }

    private function setUpDetalle($cantidad = 5, $costo = 1, $importe = null)
    {
        $producto = App\Producto::last();
        $sucursal = App\Sucursal::last();
        $entrada = App\Entrada::last();
        $importe = floatval($cantidad * $costo);

        $detalle = [
            'costo' => $costo,
            'cantidad' => $cantidad,
            'importe' => $importe,
            'producto_id' => $producto->id,
            'sucursal_id' => $sucursal->id,
            'upc' => $producto->upc
        ];

        return $entrada->crearDetalle($detalle);
    }

    private function setUpEstados()
    {
        $estadoEntradaCreando = new App\EstadoEntrada(['nombre' => 'Creando']);
        $estadoEntradaCargando = new App\EstadoEntrada(['nombre' => 'Cargando']);
        $estadoEntradaCargado = new App\EstadoEntrada(['nombre' => 'Cargado']);

        $estadoEntradaCreando->save();
        $estadoEntradaCargando->save();
        $estadoEntradaCargado->save();
    }
}

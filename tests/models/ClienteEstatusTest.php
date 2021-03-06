<?php

/**
 * @coversDefaultClass \App\ClienteEstatus
 */
class ClienteEstatusTest extends TestCase {

    /**
     * @coversNothing
     */
    public function testModeloEsValido()
    {
        $clienteE = factory(App\ClienteEstatus::class)->make();
        $this->assertTrue($clienteE->isValid());
        $this->assertTrue($clienteE->save());
    }

    /**
     * @coversNothing
     * @group modelo_actualizable
     */
    public function testModeloEsActualizable()
    {
        $ce = factory(App\ClienteEstatus::class)->create();
        $ce->nombre = 'MC Hammer';
        $this->assertTrue($ce->isValid('update'));
        $this->assertTrue($ce->save());
        $this->assertSame('MC Hammer', $ce->nombre);
    }

    /**
     * @coversNothing
     */
    public function testNombreEsRequerido()
    {
        $clienteE = factory(App\ClienteEstatus::class)->make(['nombre' => null]);
        $this->assertFalse($clienteE->isValid());
    }

    /**
     * @coversNothing
     */
    public function testNombreNoPuedeSerMasDe45Caracteres()
    {
        $clienteE = factory(App\ClienteEstatus::class, 'longname')->make();
        $this->assertFalse($clienteE->isValid());
    }

    /**
     * @covers ::clientes
     * @group relaciones
     */
    public function testClientes()
    {
        $estatus = factory(App\ClienteEstatus::class)->create();
        $cliente = factory(App\Cliente::class, 'full')->create();
        $estatus->clientes()->save($cliente);
        $clientes = $estatus->clientes;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $clientes);
        $this->assertInstanceOf(App\Cliente::class, $clientes[0]);
    }
}

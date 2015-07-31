<?php

/**
 * @coversDefaultClass \App\Cliente
 */
class ClienteTest extends TestCase {

    /**
     * @coversNothing
     */
    public function testModeloEsValido()
    {
        $cliente = factory(App\Cliente::class)->make();
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     * @group modelo_actualizable
     */
    public function testModeloEsActualizable()
    {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $cliente->nombre = 'MC Hammer';
        $this->assertTrue($cliente->isValid('update'));
        $this->assertTrue($cliente->save());
    }

    /**
     * @coversNothing
     */
    public function testEmailEsObligatorio()
    {
        $cliente = factory(App\Cliente::class)->make(['email' => null]);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testEmailEsDeFormatoValido()
    {
        $cliente = factory(App\Cliente::class)->make(['email' => 'aaa']);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testEmailNoEsLargo()
    {
        $cliente = factory(App\Cliente::class, 'longemail')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testEmailEsUnico()
    {
        $cliente = factory(App\Cliente::class, 'full')->make();
        $dup = clone $cliente;
        $cliente->save();
        $this->assertFalse($dup->save());
    }

    /**
     * @coversNothing
     */
    public function testUsuarioEsObligatorio()
    {
        $cliente = factory(App\Cliente::class)->make(['usuario' => null]);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testUsuarioNoEsLargo()
    {
        $cliente = factory(App\Cliente::class, 'longusername')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testPasswordEsExactamente64Caracteres()
    {
        $cliente = factory(App\Cliente::class)->make();
        $this->assertSame(64, strlen($cliente->password));
        $cliente = factory(App\Cliente::class, 'longpassword')->make();
        $this->assertFalse($cliente->isValid());
        $cliente = factory(App\Cliente::class, 'shortpassword')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testNombreEsObligatorio()
    {
        $cliente = factory(App\Cliente::class)->make(['nombre' => null]);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testNombreNoEsLargo()
    {
        $cliente = factory(App\Cliente::class, 'longname')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaDeNacimientoNoEsObligatoria()
    {
        $cliente = factory(App\Cliente::class)->make(['fecha_nacimiento' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaDeNacimientoEsTimestamp()
    {
        $cliente = factory(App\Cliente::class)->make(['fecha_nacimiento' => 'asd']);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testSexoEsObligatorio()
    {
        $cliente = factory(App\Cliente::class)->make(['sexo' => null]);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testSexoEsHombreOMujer()
    {
        $cliente = factory(App\Cliente::class)->make(['sexo' => 'aaa']);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testOcupacionNoEsObligatoria()
    {
        $cliente = factory(App\Cliente::class)->make(['ocupacion' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testOcupacionNoPuedeSerLarga()
    {
        $cliente = factory(App\Cliente::class, 'longocc')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaVerificacionCorreoEsOpcional()
    {
        $cliente = factory(App\Cliente::class)->make(['fecha_verificacion_correo' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaVerificacionCorreoEsTimestamp()
    {
        $cliente = factory(App\Cliente::class)->make(['fecha_verificacion_correo' => 'aaa']);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaExpiraClubZegucomEsOpcional()
    {
        $cliente = factory(App\Cliente::class)->make(['fecha_expira_club_zegucom' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaExpiraClubZegucomEsTimestamp()
    {
        $cliente = factory(App\Cliente::class)->make(['fecha_expira_club_zegucom' => 'aaa']);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testReferenciaOtroEsOpcional()
    {
        $cliente = factory(App\Cliente::class)->make(['referencia_otro' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testReferenciaOtroNoEsLargo()
    {
        $cliente = factory(App\Cliente::class, 'longref')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @covers ::estatus
     */
    public function testEstatus()
    {
        $estatus = factory(App\ClienteEstatus::class)->create();
        $cliente = factory(App\Cliente::class)->make();
        $cliente->estatus()->associate($estatus);
        $this->assertInstanceOf(App\ClienteEstatus::class, $cliente->estatus);
    }

    /**
     * @covers ::referencia
     */
    public function testReferencia()
    {
        $referencia = factory(App\ClienteReferencia::class)->create();
        $cliente = factory(App\Cliente::class)->make();
        $cliente->referencia()->associate($referencia);
        $this->assertInstanceOf(App\ClienteReferencia::class, $cliente->referencia);
    }

    /**
     * @covers ::comentarios
     */
    public function testComentarios()
    {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $empleado = factory(App\Empleado::class)->create();
        $cliente->empleados()->attach($empleado, ['comentario' => "Balalalala"]);
        $comentarios = $cliente->comentarios;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $comentarios);
        $this->assertInstanceOf(App\ClienteComentario::class, $comentarios[0]);
    }

    /**
     * @covers ::autoriza
     */
    public function testAutorizaConCliente()
    {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $autorizado = factory(App\Cliente::class, 'full')->create();
        $this->assertTrue($cliente->autoriza($autorizado));
    }

    /**
     * @covers ::autoriza
     */
    public function testAutorizaConNombre()
    {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $autorizado = "Neil deGrasse Tyson";
        $this->assertTrue($cliente->autoriza($autorizado));
    }

    /**
     * @covers ::autorizaciones
     */
    public function testAutorizaciones()
    {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $autorizado = factory(App\Cliente::class, 'full')->create();

        factory(App\ClienteAutorizacion::class)->create([
            'cliente_id' => $cliente->id,
            'cliente_autorizado_id' => $autorizado->id,
            'nombre_autorizado' => null]);
        $autorizaciones = $cliente->autorizaciones;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $autorizaciones);
        $this->assertInstanceOf(App\ClienteAutorizacion::class, $autorizaciones[0]);
    }

    /**
     * @covers ::empleado
     */
    public function testEmpleado()
    {
        $cliente = factory(App\Cliente::class)->make();
        $empleado = factory(App\Empleado::class)->create();
        $cliente->empleado()->associate($empleado);
        $this->assertInstanceOf(App\Empleado::class, $cliente->empleado);
    }

    /**
     * @covers ::vendedor
     */
    public function testVendedor()
    {
        $cliente = factory(App\Cliente::class)->make();
        $empleado = factory(App\Empleado::class)->create();
        $cliente->vendedor()->associate($empleado);
        $this->assertInstanceOf(App\Empleado::class, $cliente->vendedor);
    }

    /**
     * @covers ::sucursal
     */
    public function testSucursal()
    {
        $cliente = factory(App\Cliente::class)->make();
        $sucursal = factory(App\Sucursal::class)->create();
        $cliente->sucursal()->associate($sucursal);
        $this->assertInstanceOf(App\Sucursal::class, $cliente->sucursal);
    }

    /**
     * @covers ::paginasWebDistribuidores
     */
    public function testPaginasWebDistribuidores()
    {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $pwd = factory(App\PaginaWebDistribuidor::class)->make();
        $cliente->paginasWebDistribuidores()->save($pwd);
        $pwds = $cliente->paginasWebDistribuidores;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $pwds);
        $this->assertInstanceOf(App\PaginaWebDistribuidor::class, $pwds[0]);
    }

    /**
     * @covers ::domicilios
     */
    public function testDomicilios()
    {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $domicilio = factory(App\Domicilio::class)->create();
        $cliente->domicilios()->attach($domicilio);
        $domicilios = $cliente->domicilios;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $domicilios);
        $this->assertInstanceOf(App\Domicilio::class, $domicilios[0]);
    }

    /**
     * @covers ::serviciosSoportes
     */
    public function testServiciosSoportes()
    {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $servicios_soportes = factory(App\ServicioSoporte::class, 5)->create([
            'cliente_id' => $cliente->id
        ]);
        $servicios_soportes_resultado = $cliente->serviciosSoportes;
        for ($i = 0; $i < 5; $i ++)
        {
            $this->assertEquals($servicios_soportes[$i]->id, $servicios_soportes_resultado[$i]->id);
        }
    }

    /**
     * @covers ::rmas
     */
    public function testRmas(){
        $cliente = factory(App\Cliente::class, 'full')->create();
        $rmas = factory(App\Rma::class, 5)->create([
            'cliente_id' => $cliente->id
        ]);
        $rmas_resultado = $cliente->rmas;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $rmas_resultado);
        $this->assertInstanceOf(App\Rma::class, $rmas_resultado[0]);
    }
}

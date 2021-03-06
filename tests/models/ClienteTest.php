<?php
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @coversDefaultClass \App\Cliente
 */
class ClienteTest extends TestCase {

    use DatabaseTransactions;

    /**
     * @coversNothing
     */
    public function testModeloEsValido() {
        $cliente = factory(App\Cliente::class)->make();
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     * @group modelo_actualizable
     */
    public function testModeloEsActualizable() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $cliente->nombre = 'Anthony Hoskins';
        $this->assertTrue($cliente->isValid('update'));
        $this->assertTrue($cliente->save());
        $this->assertSame('Anthony Hoskins', $cliente->nombre);
    }

    /**
     * @coversNothing
     */
    public function testUsuarioEsObligatorio() {
        $cliente = factory(App\Cliente::class)->make(['usuario' => null]);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testUsuarioEsUnico() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $cliente_test = factory(App\Cliente::class, 'full')->make();
        $usuario = $cliente_test->usuario;
        $cliente_test->usuario = $cliente->usuario;
        $this->assertFalse($cliente_test->isValid());
        $cliente_test->usuario = $usuario;
        $this->assertTrue($cliente_test->isValid());
    }


    /**
     * @coversNothing
     */
    public function testUsuarioNoEsLargo() {
        $cliente = factory(App\Cliente::class, 'longusername')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testNombreEsObligatorio() {
        $cliente = factory(App\Cliente::class)->make(['nombre' => null]);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testNombreNoEsLargo() {
        $cliente = factory(App\Cliente::class, 'longname')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaDeNacimientoNoEsObligatoria() {
        $cliente = factory(App\Cliente::class)->make(['fecha_nacimiento' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaDeNacimientoEsTimestamp() {
        $cliente = factory(App\Cliente::class)->make(['fecha_nacimiento' => 'asd']);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testSexoEsObligatorio() {
        $cliente = factory(App\Cliente::class)->make(['sexo' => null]);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testSexoEsHombreOMujer() {
        $cliente = factory(App\Cliente::class)->make(['sexo' => 'aaa']);
        $this->assertFalse($cliente->isValid());
        $cliente->sexo = 'HOMBRE';
        $this->assertTrue($cliente->isValid());
        $cliente->sexo = 'MUJER';
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testOcupacionNoEsObligatoria() {
        $cliente = factory(App\Cliente::class)->make(['ocupacion' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testOcupacionNoPuedeSerLarga() {
        $cliente = factory(App\Cliente::class, 'longocc')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaVerificacionCorreoEsOpcional() {
        $cliente = factory(App\Cliente::class)->make(['fecha_verificacion_correo' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaVerificacionCorreoEsTimestamp() {
        $cliente = factory(App\Cliente::class)->make(['fecha_verificacion_correo' => 'aaa']);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaExpiraClubZegucomEsOpcional() {
        $cliente = factory(App\Cliente::class)->make(['fecha_expira_club_zegucom' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testFechaExpiraClubZegucomEsTimestamp() {
        $cliente = factory(App\Cliente::class)->make(['fecha_expira_club_zegucom' => 'aaa']);
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testReferenciaOtroEsOpcional() {
        $cliente = factory(App\Cliente::class)->make(['referencia_otro' => null]);
        $this->assertTrue($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testReferenciaOtroNoEsLargo() {
        $cliente = factory(App\Cliente::class, 'longref')->make();
        $this->assertFalse($cliente->isValid());
    }

    /**
     * @coversNothing
     */
    public function testCuandoNoSeProporcionaNombreDeUsuarioSeGeneraUnoBasadoEnTimestamp() {
        $cliente = factory(App\Cliente::class, 'full')->make();
        unset($cliente->usuario);
        $this->assertTrue($cliente->save(), $cliente->errors);
        $this->assertNotEmpty($cliente->usuario);
        $expected = substr(microtime(true), 0, 6);
        $test = substr($cliente->usuario, 0, 6);
        $this->assertSame($expected, $test);
    }

    /**
     * @covers ::estatus
     * @group relaciones
     */
    public function testEstatus() {
        $estatus = factory(App\ClienteEstatus::class)->create();
        $cliente = factory(App\Cliente::class)->make();
        $cliente->estatus()->associate($estatus);
        $this->assertInstanceOf(App\ClienteEstatus::class, $cliente->estatus);
    }

    /**
     * @covers ::referencia
     * @group relaciones
     */
    public function testReferencia() {
        $referencia = factory(App\ClienteReferencia::class)->create();
        $cliente = factory(App\Cliente::class)->make();
        $cliente->referencia()->associate($referencia);
        $this->assertInstanceOf(App\ClienteReferencia::class, $cliente->referencia);
    }

    /**
     * @covers ::comentarios
     * @group relaciones
     */
    public function testComentarios() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $empleado = factory(App\Empleado::class)->create();
        $cliente->empleados()->attach($empleado, ['comentario' => "Balalalala"]);
        $comentarios = $cliente->comentarios;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $comentarios);
        $this->assertInstanceOf(App\ClienteComentario::class, $comentarios[0]);
    }

    /**
     * @covers ::autorizaciones
     * @group relaciones
     */
    public function testAutorizaciones() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $autorizado = factory(App\Cliente::class, 'full')->create();

        factory(App\ClienteAutorizacion::class)->create([
            'cliente_id'            => $cliente->id,
            'cliente_autorizado_id' => $autorizado->id,
            'nombre_autorizado'     => null
        ]);
        $autorizaciones = $cliente->autorizaciones;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $autorizaciones);
        $this->assertInstanceOf(App\ClienteAutorizacion::class, $autorizaciones[0]);
    }

    /**
     * @covers ::empleado
     * @group relaciones
     */
    public function testEmpleado() {
        $cliente = factory(App\Cliente::class)->make();
        $empleado = factory(App\Empleado::class)->create();
        $cliente->empleado()->associate($empleado);
        $this->assertInstanceOf(App\Empleado::class, $cliente->empleado);
    }

    /**
     * @covers ::vendedor
     * @group relaciones
     */
    public function testVendedor() {
        $cliente = factory(App\Cliente::class)->make();
        $empleado = factory(App\Empleado::class)->create();
        $cliente->vendedor()->associate($empleado);
        $this->assertInstanceOf(App\Empleado::class, $cliente->vendedor);
    }

    /**
     * @covers ::sucursal
     * @group relaciones
     */
    public function testSucursal() {
        $cliente = factory(App\Cliente::class)->make();
        $sucursal = factory(App\Sucursal::class)->create();
        $cliente->sucursal()->associate($sucursal);
        $this->assertInstanceOf(App\Sucursal::class, $cliente->sucursal);
    }

    /**
     * @covers ::paginasWebDistribuidores
     * @group relaciones
     */
    public function testPaginasWebDistribuidores() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $pwd = factory(App\PaginaWebDistribuidor::class)->make();
        $cliente->paginasWebDistribuidores()->save($pwd);
        $pwds = $cliente->paginasWebDistribuidores;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $pwds);
        $this->assertInstanceOf(App\PaginaWebDistribuidor::class, $pwds[0]);
    }

    /**
     * @covers ::domicilios
     * @group relaciones
     */
    public function testDomicilios() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $domicilio = factory(App\Domicilio::class)->create();
        $cliente->domicilios()->attach($domicilio);
        $domicilios = $cliente->domicilios;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $domicilios);
        $this->assertInstanceOf(App\Domicilio::class, $domicilios[0]);
    }

    /**
     * @covers ::serviciosSoportes
     * @group relaciones
     */
    public function testServiciosSoportes() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $servicios_soportes = factory(App\ServicioSoporte::class, 5)->create([
            'cliente_id' => $cliente->id
        ]);
        $servicios_soportes_resultado = $cliente->serviciosSoportes;
        for ($i = 0; $i < 5; $i ++) {
            $this->assertEquals($servicios_soportes[$i]->id, $servicios_soportes_resultado[$i]->id);
        }
    }

    /**
     * @covers ::rmas
     * @group relaciones
     */
    public function testRmas() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        factory(App\Rma::class, 5)->create([
            'cliente_id' => $cliente->id
        ]);
        $rmas_resultado = $cliente->rmas;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $rmas_resultado);
        $this->assertInstanceOf(App\Rma::class, $rmas_resultado[0]);
    }

    /**
     * @covers ::razonesSociales
     * @group relaciones
     */
    public function testRazonesSociales() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        factory(App\RazonSocialReceptor::class, 'full')->create([
            'cliente_id' => $cliente->id]);
        $rsrs = $cliente->razonesSociales;
        $this->assertInstanceOf(Illuminate\Database\Eloquent\Collection::class, $rsrs);
        $this->assertInstanceOf(App\RazonSocialReceptor::class, $rsrs[0]);
        $this->assertCount(1, $rsrs);
    }

    /**
     * @covers ::user
     * @group relaciones
     */
    public function testUser() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        factory(App\User::class)->create([
            'morphable_id'   => $cliente->id,
            'morphable_type' => get_class($cliente)
        ]);
        $this->assertInstanceOf(App\User::class, $cliente->user);
    }

    /**
     * @covers ::tabuladores
     * @group relaciones
     */
    public function testTabuladores() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        factory(App\Tabulador::class)->create([
            'cliente_id' => $cliente->id
        ]);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $cliente->tabuladores);
        $this->assertInstanceOf(App\Tabulador::class, $cliente->tabuladores->first());
    }

    /**
     * @covers ::rol
     * @group relaciones
     */
    public function testRol() {
        $rol = factory(App\Rol::class)->create();
        $cliente = factory(App\Cliente::class, 'full')->create([
            'rol_id' => $rol->id
        ]);
        $this->assertInstanceOf(App\Rol::class, $cliente->rol);
        $this->assertSame($rol->id, $cliente->rol->id);
    }

    /**
     * @coversNothing
     * @group eventos
     */
    public function testCuandoSeCreaUnClienteSeCreanTabuladoresPorSucursalInterna() {
        factory(App\Sucursal::class, 'interna', 10)->create();
        $cliente = factory(App\Cliente::class, 'full')->make();
        $data = $cliente->toArray();
        $data['tabulador'] = 5;
        $this->assertTrue($cliente->guardar($data));

        $tabuladores_nuevos = $cliente->tabuladores;
        $cantidad_sucursales_internas = App\Sucursal::whereHas('proveedor', function ($query) {
            $query->where('externo', false);
        })->get()->count();

        $this->assertSame($cantidad_sucursales_internas, $tabuladores_nuevos->count());
        foreach ($tabuladores_nuevos as $tabulador) {
            $this->assertSame(5, $tabulador->valor_original);
        }
    }

    /**
     * @covers ::actualizar
     * @covers ::guardarDomicilios
     * @covers ::crearNuevosDomicilios
     * @covers ::actualizarDomicilios
     * @covers ::eliminarDomicilios
     * @covers ::actualizarTabuladores
     */
    public function testCuandoSeEditaUnClienteSeGuardanCambiosEnLosModelosRelacionados() {
        $cliente = $this->setUpClienteConRelaciones();
        $telefono = factory(App\Telefono::class)->make();
        $cliente['domicilios'][0]['telefonos'][0]['action'] = 1;
        $cliente['domicilios'][0]['telefonos'][0]['numero'] = $telefono->numero;
        $cliente['domicilios'][0]['telefonos'][1]['action'] = 2;
        $telefono_eliminar = $cliente['domicilios'][0]['telefonos'][1];

        $cliente_test = App\Cliente::find($cliente['id']);
        $tel_id = $cliente['domicilios'][0]['telefonos'][0]['id'];

        $this->assertTrue($cliente_test->actualizar($cliente), $cliente_test->errors);
        $this->assertSame($telefono->numero, App\Telefono::where('id', $tel_id)->first()->numero);
        $this->assertEmpty(App\Telefono::find($telefono_eliminar['id']));

    }

    /**
     * @covers ::guardar
     */
    public function testCuandoSeCreaUnClienteNuevoSeCreaSuUsario() {
        $cliente = factory(App\Cliente::class, 'full')->make();
        $user = factory(App\User::class)->make();
        $data = array_merge($cliente->toArray(), [
            'email'     => $user->email,
            'tabulador' => 1
        ]);

        $this->assertTrue($cliente->guardar($data));
        $this->assertNotEmpty(App\User::whereEmail($user->email)->first());
    }

    /**
     * @covers ::actualizar
     */
    public function testSeCreaUsuarioEnClienteActualizadoSiAntesNoExistia() {
        $cliente = factory(App\Cliente::class, 'full')->create();
        $cliente->load('user');
        $user = factory(App\User::class)->make([
            'remember_token' => null,
            'morphable_id'   => null,
            'morphable_type' => null
        ])->toArray();
        $data = array_merge($cliente->toArray(),[
            'user' => $user
        ]);

        $this->assertTrue($cliente->actualizar($data));
        $this->assertNotEmpty(App\User::whereEmail($user['email'])->first());
        $cliente->load('user');
        $this->assertSame($user['email'], $cliente->user->email);
    }

    /**
     * @covers ::guardar
     */
    public function testCuandoSeCreaUnClienteNuevoSinEmailSeAsignaUnoPorDefaultASuUsuario() {
        $cliente = factory(App\Cliente::class, 'full')->make();
        $data = $cliente->toArray();
        $this->assertTrue($cliente->guardar($data));
        $this->assertNotEmpty(App\User::whereEmail($cliente->usuario . '@' . env('STUB_EMAIL_DOMAIN', 'clientes.grupodicotech.com.mx'))->first());
    }

    private function setUpClienteConRelaciones() {
        $domicilio = factory(App\Domicilio::class)->create();
        factory(App\Telefono::class, 5)->create([
            'domicilio_id' => $domicilio->id
        ]);
        factory(App\Sucursal::class, 5, 'interna')->create();
        $cliente = factory(App\Cliente::class, 'full')->create();
        $cliente->domicilios()->save($domicilio);

        return App\Cliente::with('domicilios.telefonos', 'domicilios.codigoPostal', 'tabuladores.sucursal', 'user')->find($cliente->id)->toArray();
    }
}

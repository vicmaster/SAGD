<?php


class MargenTest extends TestCase
{
    protected $margen;

    /**
     * @covers Margen::()
     */
    public function testModeloEsValido()
    {
        $margen = factory(App\Margen::class)->make();
        $this->assertTrue($margen->isValid());
        $this->assertTrue($margen->save());
    }

    /**
     * @covers Margen::NombreNoPuedeSerNulo()
     */
    public function testNombreNoPuedeSerNulo()
    {
        $margen = factory(App\Margen::class)->make(['nombre' => '']);
        $this->assertFalse($margen->isValid());
        $this->assertFalse($margen->save());
    }

    /**
     * @covers Margen::NombreNoPuedeSerMuyLargo()
     */
    public function testNombreNoPuedeSerMuyLargo()
    {
        $margen = factory(App\Margen::class, 'longname')->make();
        $this->assertFalse($margen->isValid());
        $this->assertFalse($margen->save());
    }

    /**
     * @covers Margen::ValoresDecimalesHaenDefaultACero()
     */
    public function testValoresDecimalesHaenDefaultACero()
    {
        $margen = factory(App\Margen::class, 'nulldecimals')->make();
        $this->assertTrue($margen->isValid());
        $this->assertTrue($margen->save());
        $this->assertSame(0.0, $margen->valor, 'message');
        $this->assertSame(0.0, $margen['valor_webservice_p1']);
        $this->assertSame(0.0, $margen['valor_webservice_p8']);
    }

    /**
     * @covers Margen::DecimalesNoPuedenSerNegativos()
     */
    public function testDecimalesNoPuedenSerNegativos()
    {
        $margen = factory(App\Margen::class, 'negativedecimals')->make();
        $this->assertFalse($margen->isValid());
        $this->assertFalse($margen->save());
    }

    /**
     * @covers Margen::DecimalesNoPuedenSerMayorAUno()
     */
    public function testDecimalesNoPuedenSerMayorAUno()
    {
        $margen = factory(App\Margen::class, 'overonedecimal')->make();
        $this->assertFalse($margen->isValid());
        $this->assertFalse($margen->save());
    }
}

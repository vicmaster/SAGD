<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\ClienteComentario::class, function ($faker)
{
    return [
        'comentario' => $faker->text(200),
        'cliente_id' => $faker->randomDigit,
        'empleado_id' => $faker->randomDigit,
    ];
});

$factory->defineAs(App\ClienteComentario::class, 'longcomment', function($faker) use ($factory){
    $cc = $factory->raw(App\ClienteComentario::class);
    $cc['comentario'] = $faker->regexify('[a]{201}');
    return $cc;
});

$factory->defineAs(App\ClienteComentario::class, 'full', function($faker) use ($factory){
    $cc = $factory->raw(App\ClienteComentario::class);
    $cc = array_merge($cc, [
        'cliente_id' => factory(App\Cliente::class, 'full')->create()->id,
        'empleado_id' => factory(App\Empleado::class)->create()->id,
    ]);
    return $cc;
});

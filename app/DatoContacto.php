<?php

namespace App;


class DatoContacto extends LGGModel {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'datos_contactos';

    public $timestamps = false;
    protected $fillable = ['direccion', 'telefono', 'email', 'skype', 'fotografia_url', 'empleado_id'];
    public static $rules = [
        'direccion'      => 'string|max:100',
        'telefono'       => 'string|max:20',
        'email'          => 'email|unique:datos_contactos',
        'skype'          => 'string',
        'fotografia_url' => 'url',
        'empleado_id'    => 'required|integer'
    ];

    public $updateRules = [];

    /**
     * Define the model hooks
     * @codeCoverageIgnore
     */
    public static function boot()
    {
        DatoContacto::creating(function ($dato_contacto)
        {
            if (!$dato_contacto->isValid())
            {
                return false;
            }

            return true;
        });
        DatoContacto::updating(function($dato_contacto){
            $dato_contacto->updateRules = self::$rules;
            $dato_contacto->updateRules['email'] .= ',email,'.$dato_contacto->id;
            return $dato_contacto->isValid('update');
        });
    }

    /**
     * Obtiene el empleado asociado al dato de contacto
     * @return App\Empleado
     */
    public function empleado()
    {
        return $this->belongsTo('App\Empleado');
    }

}
<?php

namespace App\Policies;


use App\Http\Controllers\Api\V1\ClienteComentarioController;
use App\User;

class ClienteComentarioControllerPolicy
{
    /**
     * Determinar si el usuario puede listar los ClienteComentarios
     *
     * @param  User  $user
     * @param  ClienteComentarioController $controller
     * @return bool
     */
    public function index(User $user, ClienteComentarioController $controller)
    {
        $controller = $this->normalizeControllerName($controller);
        $permisos = $user->morphable->permisos();
        $permiso = $permisos->where('controlador', $controller)->where('accion', 'index')->first();
        return !empty($permiso);
    }

    /**
     * Determinar si el usuario puede guardar un ClienteComentario
     *
     * @param  User  $user
     * @param  ClienteComentarioController $controller
     * @return bool
     */
    public function store(User $user, ClienteComentarioController $controller)
    {
        $controller = $this->normalizeControllerName($controller);
        $permisos = $user->morphable->permisos();
        $permiso = $permisos->where('controlador', $controller)->where('accion', 'store')->first();
        return !empty($permiso);
    }

    /**
     * Determinar si el usuario puede ver un ClienteComentario
     *
     * @param  User  $user
     * @param  ClienteComentarioController $controller
     * @return bool
     */
    public function show(User $user, ClienteComentarioController $controller)
    {
        $controller = $this->normalizeControllerName($controller);
        $permisos = $user->morphable->permisos();
        $permiso = $permisos->where('controlador', $controller)->where('accion', 'show')->first();
        return !empty($permiso);
    }

    /**
     * Determinar si el usuario puede actualizar un ClienteComentario
     *
     * @param  User  $user
     * @param  ClienteComentarioController $controller
     * @return bool
     */
    public function update(User $user, ClienteComentarioController $controller)
    {
        $controller = $this->normalizeControllerName($controller);
        $permisos = $user->morphable->permisos();
        $permiso = $permisos->where('controlador', $controller)->where('accion', 'update')->first();
        return !empty($permiso);
    }

    /**
     * Determinar si el usuario puede eliminar un ClienteComentario
     *
     * @param  User  $user
     * @param  ClienteComentarioController $controller
     * @return bool
     */
    public function destroy(User $user, ClienteComentarioController $controller)
    {
        $controller = $this->normalizeControllerName($controller);
        $permisos = $user->morphable->permisos();
        $permiso = $permisos->where('controlador', $controller)->where('accion', 'destroy')->first();
        return !empty($permiso);
    }


    /**
     * Normaliza el nombre del controlador a su nombre de clase unicamente
     * @param $controller
     * @return string
     */
    private function normalizeControllerName($controller)
    {
        $className = get_class($controller);
        $controllerName = [];
        preg_match('/(\w+)$/', $className, $controllerName);
        return $controllerName[0];
    }
}

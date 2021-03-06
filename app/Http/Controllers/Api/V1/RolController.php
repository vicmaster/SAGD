<?php

namespace App\Http\Controllers\Api\V1;


use App\Empleado;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Permiso;
use App\Rol;
use Illuminate\Http\Request;

class RolController extends Controller {

    protected $rol;
    protected $permiso;

    public function __construct(Rol $rol, Permiso $permiso, Empleado $empleado) {
        $this->rol = $rol;
        $this->permiso = $permiso;
        $this->empleado = $empleado;
        $this->middleware('jwt.auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $this->authorize($this);

        return $this->rol->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request) {
        $this->authorize($this);
        $params = $request->all();
        $this->rol->fill($params);
        if ($this->rol->save()) {
            return response()->json([
                'message' => 'Rol creado exitosamente',
                'rol'     => $this->rol->self()
            ], 201,
                ['Location' => route('api.v1.rol.show', $this->rol->getId())]);
        } else {
            return response()->json([
                'message' => 'Rol no creado',
                'error'   => $this->rol->errors
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id) {
        $this->authorize($this);
        $this->rol = $this->rol->find($id);
        if ($this->rol) {
            return response()->json([
                'message' => 'Rol obtenido exitosamente',
                'rol'     => $this->rol->self()
            ], 200);
        } else {
            return response()->json([
                'message' => 'Rol no encontrado o no existente',
                'error'   => 'No encontrado'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int $id
     * @return Response
     */
    public function update(Request $request, $id) {
        $this->authorize($this);
        $params = $request->all();
        $this->rol = $this->rol->find($id);
        if (empty($this->rol)) {
            return response()->json([
                'message' => 'No se pudo realizar la actualizacion del rol',
                'error'   => 'Rol no encontrado'
            ], 404);
        } elseif ($this->rol->update($params)) {
            return response()->json([
                'message' => 'Rol se actualizo correctamente'
            ], 200);
        } else {
            return response()->json([
                'message' => 'No se pudo realizar la actualizacion del rol',
                'error'   => $this->rol->errors
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id) {
        $this->authorize($this);
        $this->rol = $this->rol->find($id);
        if (empty($this->rol)) {
            return response()->json([
                'message' => 'No se pudo eliminar el rol',
                'error'   => 'Rol no encontrado'
            ], 404);
        } elseif ($this->rol->delete()) {
            return response()->json([
                'message' => 'Rol eliminado correctamente',
            ], 200);
        } else {
            return response()->json([
                'message' => 'No se pudo eliminar el rol',
                'error'   => $this->rol->errors
            ], 400);
        }
    }

    /**
     * Regresa todos los Permisos que tienen los Roles generales
     *
     * @return Response
     */
    public function generales() {
        $this->authorize($this);

        return $this->rol->permisosRoles();
    }

    /**
     * Regresa todos los Permisos de todos los Empleados
     *
     * @return Response
     */
    public function individuales() {
        $this->authorize($this);

        return $this->rol->permisosIndividuales();
    }

    /**
     * Agrega un Permiso a un Rol
     * @param Request $request
     * @param int $rol
     * @param int $permiso
     * @return Response
     */
    public function attach(Request $request, $rol, $permiso) {
        $this->authorize($this);
        $this->rol = $this->rol->find($rol);
        $this->permiso = $this->permiso->find($permiso);
        if ($this->rol && $this->permiso) {
            $this->rol->permisos()->attach($this->permiso->id);

            return response()->json([
                'message' => 'Permiso asignado a rol exitosamente'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Rol o Permiso no encontrado, intente nuevamente',
                'error'   => 'No se asigno permiso a rol'
            ], 400);
        }
    }

    /**
     * Remueve un Permiso de un Rol
     * @param Request $request
     * @param int $rol
     * @param int $permiso
     * @return Response
     */
    public function detach(Request $request, $rol, $permiso) {
        $this->authorize($this);
        $this->rol = $this->rol->find($rol);
        $this->permiso = $this->permiso->find($permiso);
        if ($this->rol && $this->permiso) {
            $this->rol->permisos()->detach($this->permiso->id);

            return response()->json([
                'message' => 'Permiso removido del rol exitosamente'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Rol o Permiso no encontrado, intente nuevamente',
                'error'   => 'No se removio permiso del rol'
            ], 400);
        }
    }

    /**
     * Agrega un Empleado a un Rol
     * @param Request $request
     * @param int $id
     * @param int $empleado
     * @return Response
     */
    public function attachEmpleado(Request $request, $id, $empleado) {
        $this->authorize($this);
        $this->rol = $this->rol->find($id);
        $this->empleado = $this->empleado->find($empleado);
        if ($this->rol && $this->empleado) {
            $this->rol->empleados()->attach($this->empleado->id);

            return response()->json([
                'message' => 'Empleado asignado a rol exitosamente'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Rol o Empleado no encontrado, intente nuevamente',
                'error'   => 'No se asigno empleado a rol'
            ], 400);
        }
    }

    /**
     * Remueve un Empleado de un Rol
     * @param Request $request
     * @param int $rol
     * @param int $empleado
     * @return Response
     */
    public function detachEmpleado(Request $request, $rol, $empleado) {
        $this->authorize($this);
        $this->rol = $this->rol->find($rol);
        $this->empleado = $this->empleado->find($empleado);
        if ($this->rol && $this->empleado) {
            $this->rol->empleados()->detach($this->empleado->id);

            return response()->json([
                'message' => 'Empleado removido del rol exitosamente'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Rol o Empleado no encontrado, intente nuevamente',
                'error'   => 'No se removio empleado del rol'
            ], 400);
        }
    }

    /**
     * Regresa los empleados del rol especificado
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function empleados(Request $request, $id) {
        $this->authorize($this);
        $this->rol = $this->rol->find($id);
        if ($this->rol) {
            return response()->json([
                'empleados' => $this->rol->empleados
            ], 200);
        } else {
            return response()->json([
                'message' => 'No se pudo encontrar el rol',
                'error'   => 'Rol no existente'
            ], 404);
        }
    }

    /**
     * Regresa los roles que se le pueden asignar a clientes
     * @param Request $request
     * @return Response
     */
    public function rolesClientes(Request $request) {
        $this->authorize($this);
        $roles = $this->rol->whereIn('nombre', ['USUARIO FINAL', 'DISTRIBUIDOR'])->get();
        if(empty($roles)){
            return response()->json([
                'message' => 'No se encontraron roles para clientes'
            ], 404);
        }else{
            return response()->json($roles, 200);
        }
    }
}


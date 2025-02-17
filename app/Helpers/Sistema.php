<?php

use App\Models\Admin\Permiso;

if(!function_exists('getMenuActivo')){
    function getMenuActivo($ruta){
        if(request()->is($ruta) || request()->is($ruta. '/*')){
            return 'active';
        }else{
            return '';
        }
    }
}

if(!function_exists('canUser')){
    function can($permiso, $redirect = true){
        if(session()->get('rol_nombre') == 'admin'){
            return true;
        }else{
            $rolId = session()->get('rol_id');
            $permisos = cache()->tags('permiso')->rememberForever("permiso.rolid.$rolId", function(){
                return Permiso::whereHas('roles', function($query){
                    $query->where('rol_id', session()->get('rol_id'));
                })->get()->pluck('slug')->toArray();
            });  
            if (!in_array($permiso, $permisos)) {
                if ($redirect) {
                    if(!request()->ajax())
                        return redirect()->route('inicio')->with('mensaje', 'No tienes permiso para entrar en este modulo')->send();
                    abort(403, 'No tiene permiso');                  
                } else {
                    return false;
                }
            }   
            return true;
        }
    }
}
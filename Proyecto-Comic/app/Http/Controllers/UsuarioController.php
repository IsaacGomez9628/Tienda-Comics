<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['show', 'editPerfil', 'updatePerfil']);
    }
    
    public function index()
    {
        $usuarios = Usuario::with(['persona', 'rol'])->get();
        return view('usuarios.index', compact('usuarios'));
    }
    
    public function create()
    {
        $roles = Rol::where('id_estatus', 1)->get();
        return view('usuarios.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email',
            'telefono' => 'nullable|string|max:15',
            'fecha_nacimiento' => 'nullable|date',
            'nombre_usuario' => 'required|string|unique:usuarios,nombre_usuario',
            'password' => 'required|string|min:8|confirmed',
            'id_rol' => 'required|exists:roles,id_rol'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Crear persona
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'id_estatus' => 1
            ]);
            
            // Crear user para autenticación
            $user = User::create([
                'name' => $persona->nombreCompleto(),
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            
            // Crear usuario
            $usuario = Usuario::create([
                'id_persona' => $persona->id_persona,
                'id_user' => $user->id,
                'nombre_usuario' => $request->nombre_usuario,
                'contrasena' => Hash::make($request->password),
                'id_rol' => $request->id_rol,
                'id_estatus' => 1
            ]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::user()->usuario->id_usuario,
                'crear',
                'usuarios',
                $usuario->id_usuario,
                null,
                "Creación de usuario {$usuario->nombre_usuario}"
            );
            
            DB::commit();
            
            return redirect()->route('usuarios.index')
                            ->with('success', 'Usuario creado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function show($id)
    {
        $usuario = Usuario::with(['persona', 'rol'])->findOrFail($id);

        dd([
            'class_name' => get_class(Auth::user()),
            'has_usuario' => Auth::user()->usuario ? true : false,
            'has_esAdmin_method' => method_exists(Auth::user(), 'esAdmin'),
            'usuario_info' => Auth::user()->usuario ? [
                'id' => Auth::user()->usuario->id_usuario,
                'has_rol' => Auth::user()->usuario->rol ? true : false
            ] : null
        ]);
        
        $isAdmin = false;
        $currentUser = Auth::user();
        
        if ($currentUser && method_exists($currentUser, 'usuario') && $currentUser->usuario) {
            $isAdmin = $currentUser->usuario && 
                      $currentUser->usuario->rol && 
                      $currentUser->usuario->rol->nombre === 'Administrador';
        }
        
        // Verificar si es el usuario actual o es administrador
        if ($currentUser && $currentUser->usuario && 
            $currentUser->usuario->id_usuario != $id && !$isAdmin) {
            return redirect()->route('dashboard')
                            ->with('error', 'No tienes permisos para ver este usuario');
        }
        
        return view('usuarios.show', compact('usuario'));
    }
    
    public function edit($id)
    {
        $usuario = Usuario::with('persona')->findOrFail($id);
        $roles = Rol::where('id_estatus', 1)->get();
        
        return view('usuarios.edit', compact('usuario', 'roles'));
    }
    
    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email,' . $usuario->user->id,
            'telefono' => 'nullable|string|max:15',
            'fecha_nacimiento' => 'nullable|date',
            'nombre_usuario' => 'required|string|unique:usuarios,nombre_usuario,' . $id . ',id_usuario',
            'id_rol' => 'required|exists:roles,id_rol',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Actualizar persona
            $persona = Persona::findOrFail($usuario->id_persona);
            $persona->update([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'email' => $request->email
            ]);
            
            // Actualizar user
            $user = User::findOrFail($usuario->id_user);
            $userData = [
                'name' => $persona->nombreCompleto(),
                'email' => $request->email
            ];
            
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
            $user->update($userData);
            
            // Actualizar usuario
            $usuarioData = [
                'nombre_usuario' => $request->nombre_usuario,
                'id_rol' => $request->id_rol
            ];
            
            if ($request->filled('password')) {
                $usuarioData['contrasena'] = Hash::make($request->password);
            }
            
            $usuario->update($usuarioData);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::user()->usuario->id_usuario,
                'actualizar',
                'usuarios',
                $usuario->id_usuario,
                null,
                "Actualización de usuario {$usuario->nombre_usuario}"
            );
            
            DB::commit();
            
            return redirect()->route('usuarios.index')
                            ->with('success', 'Usuario actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el usuario: ' . $e->getMessage()])
                         ->withInput();
        }
    }
    
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        
        // No permitir eliminar el propio usuario
        if (Auth::user()->usuario->id_usuario == $id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }
        
        DB::beginTransaction();
        
        try {
            // Cambiar estatus a inactivo
            $usuario->update(['id_estatus' => 2]); // 2 = Inactivo
            
            $persona = Persona::findOrFail($usuario->id_persona);
            $persona->update(['id_estatus' => 2]);
            
            // Registrar movimiento
            Movimiento::registrar(
                Auth::user()->usuario->id_usuario,
                'desactivar',
                'usuarios',
                $usuario->id_usuario,
                null,
                "Desactivación de usuario {$usuario->nombre_usuario}"
            );
            
            DB::commit();
            
            return redirect()->route('usuarios.index')
                            ->with('success', 'Usuario desactivado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al desactivar el usuario: ' . $e->getMessage()]);
        }
    }
    
    public function editPerfil()
    {
        $usuario = Auth::user()->usuario;
        return view('usuarios.perfil', compact('usuario'));
    }
    
    public function updatePerfil(Request $request)
    {
        $usuario = Auth::user()->usuario;
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'email' => 'required|email|unique:users,email,' . $usuario->user->id,
            'telefono' => 'nullable|string|max:15',
            'fecha_nacimiento' => 'nullable|date',
            'password_actual' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Verificar contraseña actual si se quiere cambiar
            if ($request->filled('password')) {
                if (!Hash::check($request->password_actual, Auth::user()->password)) {
                    return back()->withErrors([
                        'password_actual' => 'La contraseña actual no es correcta.'
                    ])->withInput();
                }
            }
            
            // Actualizar persona
            $persona = Persona::findOrFail($usuario->id_persona);
            $persona->update([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
                'email' => $request->email
            ]);
            
            // Actualizar user
            $user = Auth::user();
            $userData = [
                'name' => $persona->nombreCompleto(),
                'email' => $request->email
            ];
            
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            
           
            $user->update($userData);
            
            // Actualizar contraseña del usuario si se proporcionó
            if ($request->filled('password')) {
                $usuario->update([
                    'contrasena' => Hash::make($request->password)
                ]);
            }
            
            // Registrar movimiento
            Movimiento::registrar(
                $usuario->id_usuario,
                'actualizar',
                'usuarios',
                $usuario->id_usuario,
                null,
                "Actualización de perfil de usuario"
            );
            
            DB::commit();
            
            return redirect()->route('perfil.edit')
                            ->with('success', 'Perfil actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el perfil: ' . $e->getMessage()])
                         ->withInput();
        }
    }
}
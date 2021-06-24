<?php
namespace App\Http\Controllers;
use App\{
    Http\Requests\CreateUserRequest, User, UserProfile
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function  index()
    {

        $users = DB::table('users')->get();

        $title = 'Listado de usuarios';

        return view('users.index', compact('title', 'users'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $user = User::all();
        return view('users.create',compact('user'));
    }


    //*Metodo para guardar y validar el usuario*//
    public function store(CreateUserRequest $request)
    {
        $request->createUser();

          return redirect()->route('users.index');
    }


    //*Metodo para editar el usuario*//
    public function edit(User $user, UserProfile $userProfile)
    {
        return view('users.edit', [
            'user' => $user,
            'userProfile' => $userProfile
        ]);
    }


    //*Metodo para actualizar el usuario*//
    public function update(User $user)
    {
      $data = request()->validate([
          'name' => 'required',
          'email' =>  ['required', 'email', Rule::unique('users')->ignore($user->id)],
          'password' =>['nullable', 'alpha_num', 'min:6'],
      ], [
          'name.required' => 'El campo nombre es obligatorio',
          'email.required' => 'El campo Email es obligatorio',
          'email.unique' =>  'Este Email ya esta registrado',
          'password.required' => 'El campo contraseña es obligatorio',
      ]);

        if ($data['password'] != null) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

      $user->update($data);

      return redirect()->route('users.show',['user' => $user]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }
}

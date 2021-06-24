@extends('layout')

@section('title', "Usuario {$user->id}")

@section('content')

    <div class="card" style="max-width: 27rem;">
        <div class="card-header">
            <h1>Usuario #{{ $user->id }}</h1>
        </div>

        <div class="card-body">

            <h5 class="card-title">Nombre del usuario:</h5>
            <p class="card-text"> {{ $user->name }}</p>

            <h5 class="card-title">Correo electrónico:</h5>
            <p class="card-text"> {{ $user->email }}</p>

            <a class="btn btn-primary" href="{{ route('users.index') }}" role="button">Regresar al listado de usuarios</a>
        </div>
    </div>
@endsection
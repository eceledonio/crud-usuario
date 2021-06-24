<?php
namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersModuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_shows_the_users_list()
    {
        factory(User::class)->create([
            'name' => 'Joel',
        ]);

        factory(User::class)->create([
            'name' => 'Ellie',
        ]);

        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('Listado de usuarios')
            ->assertSee('Joel')
            ->assertSee('Ellie');
    }

    /** @test */
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }

    /** @test */
    function it_displays_the_users_details()
    {
        $user = factory(User::class)->create([
            'name' => 'Enmanuel Celedonio'
        ]);
        $this->get("/usuarios/{$user->id}")
            ->assertStatus(200)
            ->assertSee('Enmanuel Celedonio');
    }

    /** @test */
    function it_displays_a_404_error_if_the_user_is_not_found()
    {
        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }

    /** @test */
    function it_loads_the_new_users_page()
    {
        $this->withoutExceptionHandling();
        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear nuevo usuario');
    }

    /** @test */
    function it_creates_a_new_user()
    {
        $this->withoutExceptionHandling();

        $this->post('/usuarios/',[
            'name' => 'Enmanuel',
            'email' => 'enmanuel@styde.net',
            'password' => 'laravel',
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/enmanuel.ecm',
        ])->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Enmanuel',
            'email' => 'enmanuel@styde.net',
            'password' => 'laravel',
        ]);

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/enmanuel.ecm',
            'user_id' => User::findByEmail('enmanuel@styde.net')->id,

        ]);
    }

        /** @test */
    function the_name_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => '',
                'email' => 'enmanuelo@styde.net',
                'password' => 'laravel'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Enmanuel Celedonio',
                'email' => '',
                'password' => 'laravel'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email' => 'El campo Email es obligatorio']);
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_must_be_valid()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Enmanuel Celedonio',
                'email' => 'correo-no-valido',
                'password' => 'laravel'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email' => 'El campo Email debe contener un formato correcto: correo@example.com']);
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_email_must_be_unique()
    {
        factory(User::class)->create([
            'email' => 'enmanuel@styde.net'
        ]);
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Enmanuel Celedonio',
                'email' => 'enmanuel@styde.net',
                'password' => 'laravel'
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email' => 'Este Email ya esta registrado']);
        $this->assertEquals(1, User::count());
    }

    /** @test */
    function the_password_is_required()
    {

        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Enmanuel Celedonio',
                'email' => 'enmanuel@styde.net',
                'password' => ''
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password' => 'El campo contraseña es obligatorio']);
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function the_required_password_must_have_more_than_six_characters()
    {


        $this->from('usuarios/nuevo')
            ->post('/usuarios/', [
                'name' => 'Enmanuel Celedonio',
                'email' => 'enmanuel@styde.net',
                'password' => ''
            ])
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password']);
        $this->assertEquals(0, User::count());
    }

    /** @test */
    function it_loads_the_edit_user_page()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();
        $this->get("/usuarios/{$user->id}/editar") // usuarios/5/editar
             ->assertStatus(200)
             ->assertViewIs('users.edit')
             ->assertSee('Editar Usuario')
             ->assertViewHas('user', function ($viewUser) use ($user) {
                return $viewUser->id === $user->id;
            });
    }


    /** @test */
    function it_update_a_user()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->put("/usuarios/{$user->id}", [
            'name' => 'Enmanuel Celedonio',
            'email' => 'enmanuel@styde.net',
            'password' => 'laravel',
        ])->assertRedirect("usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Enmanuel Celedonio',
            'email' => 'enmanuel@styde.net',
            'password' => 'laravel',
        ]);
    }

    /** @test */
    function the_name_is_required_when_updating_a_user()
    {
        // $this->withoutExceptionHandling();

        $user=factory(User::class)->create();
        
        $this->from("usuarios/{$user->id}/editar")
        ->put("usuarios/{$user->id}", [
                'name' => '',
                'email' => 'enmanuel@styde.net',
                'password' => 'laravel'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', ['email' => 'enmanuel@styde.net']);
        
    }

          /** @test */
    function the_email_must_be_valid_when_updating_the_user()
    {
        $user = factory(User::class)->create();
        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Enmanuel Celedonio',
                'email' => 'correo-no-valido',
                'password' => 'laravel'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users', ['name' => 'Enmanuel Celedonio']);
    }

    /** @test */
    function the_email_must_be_unique_when_updating_the_user()
    {
       // $this->withoutExceptionHandling();

        factory(User::class)->create([
            'email' => 'existing-email@example.com',
        ]);
        $user = factory(User::class)->create([
            'email' => 'enmanuel@styde.net'
        ]);
        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Enmanuel Celedonio',
                'email' => 'existing-email@example.com',
                'password' => 'laravel'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);
    }

    /** @test */
    function the_users_email_can_stay_the_same_when_updating_the_user()
    {
        $user = factory(User::class)->create([
            'email' => 'enmanuel@styde.net'
        ]);
        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Enmanuel Celedonio',
                'email' => 'enmanuel@styde.net',
                'password' => '12345678'
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)
        $this->assertDatabaseHas('users', [
            'name' => 'Enmanuel Celedonio',
            'email' => 'enmanuel@styde.net',
        ]);
    }

    /** @test */
    function the_password_is_optional_when_updating_a_user()
    {
        $oldPassword = 'CLAVE_ANTERIOR';
        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword)
        ]);
        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Enmanuel Celedonio',
                'email' => 'enmanuel@styde.net',
                'password' => ''
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)
        $this->assertCredentials([
            'name' => 'Enmanuel Celedonio',
            'email' => 'enmanuel@styde.net',
            'password' => $oldPassword // VERY IMPORTANT!
        ]);
    }

    /** @test */
    function it_deletes_a_users()
    {
        //$this->withoutExceptionHandling();

        $user = factory(User::class)->create();

        $this->delete("usuarios/$user->id")
            ->assertRedirect('usuarios');

        $this->assertDatabaseMissing('users',[
           'id' => $user->id
        ]);
    }


}
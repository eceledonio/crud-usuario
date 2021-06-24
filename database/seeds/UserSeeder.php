<?php
use App\User;
use App\Profession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Generator as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $professionId = Profession::where('title', 'Desarrollador back-end')->value('id');

      factory(User::class)->create([
        'name' => 'Enmanuel Celedonio',
        'email' => 'enmanuel@styde.net',
        'password' => bcrypt('laravel'),
        'profession_id' => $professionId,
        'is_admin' => true,
    ]);

   
    return factory(App\User::class, 50)->create();

    }
}

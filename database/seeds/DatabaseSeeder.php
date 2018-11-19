<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    /*public function run()
    {
         $this->call(UsersTableSeeder::class);
    }*/


  public function run()
  {
			$input_roles = $this->command->ask('Enter roles in comma separate format.', 'Admin,User');
      $permissions = [
			'role-list',
			'role-create',
			'role-edit',
			'role-delete',
			'product-list',
			'product-create',
			'product-edit',
			'product-delete'
		];

		foreach ($permissions as $perms) {
			Permission::firstOrCreate(['name' => $perms]);
		}
    // Explode roles
    $roles_array = explode(',', $input_roles);

    // add roles
    foreach($roles_array as $role) {
      $role = Role::firstOrCreate(['name' => trim($role)]);

      if( $role->name == 'Admin' ) {
        // assign all permissions
        $role->syncPermissions(Permission::all());
        $this->command->info('Admin granted all the permissions');
      } else {
        // for others by default only read access
        $role->syncPermissions(Permission::where('name', 'LIKE', 'view_%')->get());
      }

      // create one user for each role
      $this->createUser($role);
    }

    $this->command->info('Roles ' . $input_roles . ' added successfully');
		$this->createUser('Admin');
  }

	public function run_backup()
	{
		// Ask for db migration refresh, default is no
		if ($this->command->confirm('Do you wish to refresh migration before seeding, it will clear all old data ?')) {
			// Call the php artisan migrate:refresh
			$this->command->call('migrate:refresh');
			$this->command->warn("Data cleared, starting from blank database.");
		}

		// Seed the default permissions
//		$permissions = Permission::defaultPermissions();

		$permissions = [
			'role-list',
			'role-create',
			'role-edit',
			'role-delete',
			'product-list',
			'product-create',
			'product-edit',
			'product-delete'
		];

		foreach ($permissions as $perms) {
			Permission::firstOrCreate(['name' => $perms]);
		}

		$this->command->info('Default Permissions added.');

		// Confirm roles needed
		if ($this->command->confirm('Create Roles for user, default is admin and user? [y|N]', true)) {

			// Ask for roles from input
			$input_roles = $this->command->ask('Enter roles in comma separate format.', 'Admin,User');

			// Explode roles
			$roles_array = explode(',', $input_roles);

			// add roles
			foreach($roles_array as $role) {
				$role = Role::firstOrCreate(['name' => trim($role)]);

				if( $role->name == 'Admin' ) {
					// assign all permissions
					$role->syncPermissions(Permission::all());
					$this->command->info('Admin granted all the permissions');
				} else {
					// for others by default only read access
					$role->syncPermissions(Permission::where('name', 'LIKE', 'view_%')->get());
				}

				// create one user for each role
				$this->createUser($role);
			}

			$this->command->info('Roles ' . $input_roles . ' added successfully');

		} else {
			Role::firstOrCreate(['name' => 'User']);
			$this->command->info('Added only default user role.');
		}

		// now lets seed some posts for demo
//		factory(\App\Post::class, 30)->create();
//		$this->command->info('Some Posts data seeded.');
		$this->command->warn('All done :)');
		$this->createUser('Admin');
	}

	/**
	 * Create a user with given role
	 *
	 * @param $role
	 */
	private function createUser($role)
	{
		$user = factory(User::class)->create();
		$user->assignRole('Admin');

		// if( $role->name == 'Admin' ) {
			$this->command->info('Here is your admin details to login:');
			$this->command->warn($user->email);
			$this->command->warn('Password is "secret"');
		// }
	}
}

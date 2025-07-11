<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $editor = Role::create(['name' => 'editor']);

        $editPosts = Permission::create(['name' => 'edit_post']);
        $deleteUsers = Permission::create(['name' => 'delete_user']);

        $admin->permissions()->attach([$editPosts->id, $deleteUsers->id]);
        $editor->permissions()->attach($editPosts->id);

        $user = User::find(1);
        $user->roles()->attach($admin->id);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'enquiry.view', 'enquiry.delete', 'enquiry.download_attachment',
            'user.manage', 'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate(User::ROLE_ADMIN, 'web');
        $admin->syncPermissions($permissions);

        // Editors can triage enquiries but cannot manage users or delete
        // records — least privilege for the common day-to-day role.
        $editor = Role::findOrCreate(User::ROLE_EDITOR, 'web');
        $editor->syncPermissions(['enquiry.view', 'enquiry.download_attachment']);

        if (app()->environment('production')) {
            $this->command->warn('Production detected: create the first admin with `php artisan admin:create`.');

            return;
        }

        $password = 'Local-Dev-Password-9!';

        $user = User::firstOrCreate(
            ['email' => 'admin@example.test'],
            [
                'name' => 'Local Admin',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'password_changed_at' => now(),
                'is_active' => true,
            ],
        );

        $user->syncRoles([User::ROLE_ADMIN]);

        $this->command->info("Local admin: admin@example.test / {$password}");
    }
}

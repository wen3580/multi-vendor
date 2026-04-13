<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $usersTable = config('admin.database.users_table');
        $rolesTable = config('admin.database.roles_table');
        $permissionsTable = config('admin.database.permissions_table');
        $menuTable = config('admin.database.menu_table');
        $roleUsersTable = config('admin.database.role_users_table');
        $rolePermissionsTable = config('admin.database.role_permissions_table');
        $roleMenuTable = config('admin.database.role_menu_table');
        $userPermissionsTable = config('admin.database.user_permissions_table');

        $db = DB::connection($connection);

        $db->transaction(function () use (
            $db,
            $usersTable,
            $rolesTable,
            $permissionsTable,
            $menuTable,
            $roleUsersTable,
            $rolePermissionsTable,
            $roleMenuTable,
            $userPermissionsTable
        ) {
            $db->table($roleMenuTable)->delete();
            $db->table($rolePermissionsTable)->delete();
            $db->table($roleUsersTable)->delete();
            $db->table($userPermissionsTable)->delete();
            $db->table($menuTable)->delete();
            $db->table($permissionsTable)->delete();
            $db->table($rolesTable)->delete();
            $db->table($usersTable)->delete();

            $db->table($usersTable)->insert([
                'id' => 1,
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'name' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $db->table($rolesTable)->insert([
                'id' => 1,
                'name' => 'Administrator',
                'slug' => 'administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $db->table($permissionsTable)->insert([
                [
                    'id' => 1,
                    'name' => 'All permission',
                    'slug' => '*',
                    'http_method' => '',
                    'http_path' => '*',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $db->table($menuTable)->insert([
                [
                    'id' => 1,
                    'parent_id' => 0,
                    'order' => 1,
                    'title' => 'Dashboard',
                    'icon' => 'fa-bar-chart',
                    'uri' => '/',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'parent_id' => 0,
                    'order' => 2,
                    'title' => 'Affiliates',
                    'icon' => 'fa-users',
                    'uri' => 'affiliates',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3,
                    'parent_id' => 0,
                    'order' => 3,
                    'title' => 'Applications',
                    'icon' => 'fa-file-text',
                    'uri' => 'applications',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 4,
                    'parent_id' => 0,
                    'order' => 4,
                    'title' => 'Attributions',
                    'icon' => 'fa-link',
                    'uri' => 'attributions',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 5,
                    'parent_id' => 0,
                    'order' => 5,
                    'title' => 'Commissions',
                    'icon' => 'fa-money',
                    'uri' => 'commissions',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 6,
                    'parent_id' => 0,
                    'order' => 6,
                    'title' => 'Coupons',
                    'icon' => 'fa-ticket',
                    'uri' => 'coupons',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 7,
                    'parent_id' => 0,
                    'order' => 7,
                    'title' => 'Payouts',
                    'icon' => 'fa-bank',
                    'uri' => 'payouts',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 8,
                    'parent_id' => 0,
                    'order' => 8,
                    'title' => 'Settings',
                    'icon' => 'fa-cog',
                    'uri' => 'settings',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 9,
                    'parent_id' => 0,
                    'order' => 9,
                    'title' => 'Webhook Logs',
                    'icon' => 'fa-exchange',
                    'uri' => 'logs/webhooks',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 10,
                    'parent_id' => 0,
                    'order' => 10,
                    'title' => 'Tracking Logs',
                    'icon' => 'fa-mouse-pointer',
                    'uri' => 'logs/tracking',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $db->table($roleUsersTable)->insert([
                'role_id' => 1,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $db->table($rolePermissionsTable)->insert([
                'role_id' => 1,
                'permission_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $roleMenus = [];
            for ($id = 1; $id <= 10; $id++) {
                $roleMenus[] = [
                    'role_id' => 1,
                    'menu_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $db->table($roleMenuTable)->insert($roleMenus);
        });
    }
}

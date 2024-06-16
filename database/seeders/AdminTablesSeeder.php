<?php

namespace Database\Seeders;

use Dcat\Admin\Models;
use Illuminate\Database\Seeder;
use DB;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // base tables
        Models\Menu::truncate();
        Models\Menu::insert(
            [
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "extension" => "",
                    "icon" => "feather icon-bar-chart-2",
                    "id" => 1,
                    "order" => 1,
                    "parent_id" => 0,
                    "show" => 1,
                    "title" => "Index",
                    "updated_at" => NULL,
                    "uri" => "/"
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "extension" => "",
                    "icon" => "feather icon-settings",
                    "id" => 2,
                    "order" => 2,
                    "parent_id" => 0,
                    "show" => 1,
                    "title" => "Admin",
                    "updated_at" => NULL,
                    "uri" => ""
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "extension" => "",
                    "icon" => "",
                    "id" => 3,
                    "order" => 3,
                    "parent_id" => 2,
                    "show" => 1,
                    "title" => "Users",
                    "updated_at" => NULL,
                    "uri" => "auth/users"
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "extension" => "",
                    "icon" => "",
                    "id" => 4,
                    "order" => 4,
                    "parent_id" => 2,
                    "show" => 1,
                    "title" => "Roles",
                    "updated_at" => NULL,
                    "uri" => "auth/roles"
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "extension" => "",
                    "icon" => "",
                    "id" => 5,
                    "order" => 5,
                    "parent_id" => 2,
                    "show" => 1,
                    "title" => "Permission",
                    "updated_at" => NULL,
                    "uri" => "auth/permissions"
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "extension" => "",
                    "icon" => "",
                    "id" => 6,
                    "order" => 6,
                    "parent_id" => 2,
                    "show" => 1,
                    "title" => "Menu",
                    "updated_at" => NULL,
                    "uri" => "auth/menu"
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "extension" => "",
                    "icon" => "",
                    "id" => 7,
                    "order" => 7,
                    "parent_id" => 2,
                    "show" => 1,
                    "title" => "Extensions",
                    "updated_at" => NULL,
                    "uri" => "auth/extensions"
                ]
            ]
        );

        Models\Permission::truncate();
        Models\Permission::insert(
            [
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "http_method" => "",
                    "http_path" => "",
                    "id" => 1,
                    "name" => "Auth management",
                    "order" => 1,
                    "parent_id" => 0,
                    "slug" => "auth-management",
                    "updated_at" => NULL
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "http_method" => "",
                    "http_path" => "/auth/users*",
                    "id" => 2,
                    "name" => "Users",
                    "order" => 2,
                    "parent_id" => 1,
                    "slug" => "users",
                    "updated_at" => NULL
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "http_method" => "",
                    "http_path" => "/auth/roles*",
                    "id" => 3,
                    "name" => "Roles",
                    "order" => 3,
                    "parent_id" => 1,
                    "slug" => "roles",
                    "updated_at" => NULL
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "http_method" => "",
                    "http_path" => "/auth/permissions*",
                    "id" => 4,
                    "name" => "Permissions",
                    "order" => 4,
                    "parent_id" => 1,
                    "slug" => "permissions",
                    "updated_at" => NULL
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "http_method" => "",
                    "http_path" => "/auth/menu*",
                    "id" => 5,
                    "name" => "Menu",
                    "order" => 5,
                    "parent_id" => 1,
                    "slug" => "menu",
                    "updated_at" => NULL
                ],
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "http_method" => "",
                    "http_path" => "/auth/extensions*",
                    "id" => 6,
                    "name" => "Extension",
                    "order" => 6,
                    "parent_id" => 1,
                    "slug" => "extension",
                    "updated_at" => NULL
                ]
            ]
        );

        Models\Role::truncate();
        Models\Role::insert(
            [
                [
                    "created_at" => "2024-06-07 11:08:49",
                    "id" => 1,
                    "name" => "Administrator",
                    "slug" => "administrator",
                    "updated_at" => "2024-06-07 11:08:49"
                ]
            ]
        );

        Models\Setting::truncate();
		Models\Setting::insert(
			[

            ]
		);

		Models\Extension::truncate();
		Models\Extension::insert(
			[

            ]
		);

		Models\ExtensionHistory::truncate();
		Models\ExtensionHistory::insert(
			[

            ]
		);

        // pivot tables
        DB::table('admin_permission_menu')->truncate();
		DB::table('admin_permission_menu')->insert(
			[

            ]
		);

        DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert(
            [

            ]
        );

        DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert(
            [

            ]
        );

        // finish
    }
}

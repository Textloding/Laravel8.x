<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class CustomMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 插入自定义菜单数据
        Menu::create([
            'parent_id' => 0,
            'order' => 8,
            'title' => '控制中心',
            'icon' => 'fa-connectdevelop',
            'uri' => '/api-switches',
            'extension' => '',
            'show' => 1,
            'created_at' => '2024-03-15 13:59:59',
            'updated_at' => '2024-03-15 13:59:59',
        ]);
    }
}

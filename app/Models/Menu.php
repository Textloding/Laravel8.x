<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'admin_menu';

    protected $fillable = [
        'parent_id',
        'order',
        'title',
        'icon',
        'uri',
        'extension',
        'show',
        'created_at',
        'updated_at',
    ];
}

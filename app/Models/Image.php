<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory,HasDateTimeFormatter;

    const IMAGES_TYPE_AVATAR = 'avatar';
    const IMAGES_TYPE_TOPIC = 'topic';
    public static $imagesTypeMap = [
        self::IMAGES_TYPE_AVATAR    => '头像',
        self::IMAGES_TYPE_TOPIC    => '话题',
    ];


    protected $fillable = ['type', 'path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

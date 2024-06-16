<?php

namespace App\Admin\Repositories;

use App\Models\ChatLog as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class ChatLog extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}

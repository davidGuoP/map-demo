<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DataRouteRepository
{
    use BaseRepository;

    protected static $table = 'data_route';
}
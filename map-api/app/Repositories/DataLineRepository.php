<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DataLineRepository
{
    use BaseRepository;

    protected static $table = 'data_line';

    public function getPageData($name = '')
    {
        $db = DB::table('data_line as a')
            ->leftJoin('data_route as b', 'a.route_id', '=', 'b.id')
            ->select('a.*', 'b.name as route_name');
        if (!empty($name)) {
            $db = $db->where('a.name', 'like', '%' . $name . '%');
        }
        return $db->get();
    }

    public function getLineOneData($id)
    {
        $db = DB::table('data_line as a')
            ->leftJoin('data_route as b', 'a.route_id', '=', 'b.id')
            ->select('a.*', 'b.name as route_name');
        if (!empty($id)) {
            $db = $db->where('a.id', $id);
        }
        return $db->first();
    }
}
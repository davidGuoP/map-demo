<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DataBarMarkerRepository
{
    use BaseRepository;

    protected static $table = 'data_bar_marker';

    public function getPageData($name = '', $id = 0)
    {
        $db = DB::table('data_bar_marker as a')
            ->leftJoin('data_route as b', 'a.route_id', '=', 'b.id')
            ->select('a.*', 'b.name as route_name', 'b.type as route_type');
        if (!empty($name)) {
            $db = $db->where('a.name', 'like', '%' . $name . '%');
        }
        if (!empty($id)) {
            $db = $db->where('a.id', $id);
        }
        return $db->get();
    }

    public function getRoutePageData($name = '', $id = 0)
    {
        $db = DB::table('data_bar_marker as a')
            ->leftJoin('data_route as b', 'a.route_id', '=', 'b.id')
            ->select('a.*', 'b.name as route_name', 'b.type as route_type');
        if (!empty($name)) {
            $db = $db->where('a.name', 'like', '%' . $name . '%');
        }
        if (!empty($id)) {
            $db = $db->where('a.route_id', $id);
        }
        return $db->get();
    }

    public function getRouteOneData($id = 0)
    {
        $db = DB::table('data_bar_marker as a')
            ->leftJoin('data_route as b', 'a.route_id', '=', 'b.id')
            ->select('a.*', 'b.name as route_name', 'b.type as route_type');
        if (!empty($id)) {
            $db = $db->where('a.id', $id);
        }
        return $db->first();
    }
}
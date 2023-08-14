<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DataVolumeMarkerRepository
{
    use BaseRepository;

    protected static $table = 'data_volume_marker';

    public function getPageData($name = '', $barId = [])
    {
        $db = DB::table('data_volume_marker as a')
            ->leftJoin('data_line as b', 'a.line_id', '=', 'b.id')
            ->select('a.*', 'b.name as line_name');
        if (!empty($name)) {
            $db = $db->where('a.name', 'like', '%' . $name . '%');
        }
        if (!empty($barId)) {
            $db = $db->whereIn('a.id', $barId);
        }
        return $db->get();
    }

    public function getLinePageData($name = '', $barId = '')
    {
        $db = DB::table('data_volume_marker as a')
            ->leftJoin('data_line as b', 'a.line_id', '=', 'b.id')
            ->leftJoin('data_route as c', 'b.route_id', '=', 'c.id')
            ->select('a.*', 'b.name as line_name', 'c.name as route_name');
        if (!empty($name)) {
            $db = $db->where('a.name', 'like', '%' . $name . '%');
        }
        if (!empty($barId)) {
            $db = $db->where('a.line_id', $barId);
        }
        return $db->get();
    }

    public function getLineOneData($id = 0)
    {
        $db = DB::table('data_volume_marker as a')
            ->leftJoin('data_line as b', 'a.line_id', '=', 'b.id')
            ->select('a.*', 'b.name as line_name');
        if (!empty($id)) {
            $db = $db->where('a.id', $id);
        }
        return $db->first();
    }

    public function getWhereInVolume($value = [], $select)
    {
        return DB::table('data_volume_marker')->whereIn('id', $value)->select($select)->get();
    }
}
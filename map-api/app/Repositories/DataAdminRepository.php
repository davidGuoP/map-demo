<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DataAdminRepository
{
    use BaseRepository;

    protected static $table = 'data_admin';

    public function getPageData($where = [])
    {
        $db = DB::table('data_admin as a')
            ->leftJoin('data_role as b', 'a.role_id', '=', 'b.id')
            ->select('a.*', 'b.name as role_name');
        if (!empty($where)) {
            $db = $db->where($where);
        }
        return $db->get();
    }
}
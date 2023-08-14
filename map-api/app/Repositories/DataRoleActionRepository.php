<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class DataRoleActionRepository
{
    use BaseRepository;

    protected static $table = 'data_role_action';

    /**
     * 获取所有数据
     *
     * @param array $where
     * @param array $select
     * @return mixed
     * @author gpc
     */
    public function getInAllData($where = [], array $select = [], $key, $value = [])
    {
        $db = DB::table(static::$table);
        if (!empty($value)) {
            $db = $db->whereIn($key, $value);
        }
        if (empty($where)) {
            if (empty($select)) {
                return $db->get();
            }
            return $db->select($select)->get();
        }

        if (empty($select)) {
            return $db->where($where)->get();
        } else {
            return $db
                ->where($where)
                ->select($select)
                ->get();
        }
    }
}
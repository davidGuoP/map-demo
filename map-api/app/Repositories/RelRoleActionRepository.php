<?php
namespace App\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class RelRoleActionRepository
{
    use BaseRepository;

    protected static $table = 'rel_role_action';

    public function getRoleActionData($roleId)
    {
        $db = DB::table('rel_role_action as a')
            ->leftJoin('data_role_action as b', 'a.role_action_id', '=', 'b.id')
            ->select('a.*', 'b.action', 'b.type');
        if (!empty($roleId)) {
            $db = $db->where('a.role_id', $roleId);
        }
        return $db->get();
    }
}
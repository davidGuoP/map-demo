<?php

namespace App\Http\Controllers\Admin;

use App\Library\Tools\Common\Common;
use App\Repositories\DataAdminRepository;
use App\Repositories\DataRoleRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    private static $dataRoleRepository = null;
    private static $dataAdminRepository = null;

    public function __construct(
        DataRoleRepository $dataRoleRepository,
        DataAdminRepository $dataAdminRepository
    )
    {
        self::$dataRoleRepository = $dataRoleRepository;
        self::$dataAdminRepository = $dataAdminRepository;
    }

    public function index(Request $request) {
        $where = [];

        $count = self::$dataAdminRepository->getCount($where);
        if (empty($count)) {
            return [
                'ServerNo' => 200, 'ResultData' => [
                    'total' => 0,
                    'data' => [],
                    'date' => [],
                ]
            ];
        }
        $data = self::$dataAdminRepository->getPageData($where);

        return ['ServerNo' => 200, 'ResultData' => [
            'total' => $count,
            'data' => $data,
            'date' => $data,
        ]];
    }

    public function save(Request $request)
    {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['name']) || empty($input['roleId']) || empty($input['password'])) {
            return ['ServerNo' => 400, 'ResultData' => '用户名或角色或密码不能为空'];
        }
        // 查询姓名是否存在
        $data = self::$dataAdminRepository->getCount(['username' => $input['name']]);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '用户名重复'];
        }
        // 更新新密码
        $pwd = Common::md($input['password']);
        $result = self::$dataAdminRepository->addData(['guid' => Common::getUuid(), 'username' => $input['name'], 'role_id' => $input['roleId'], 'password' => $pwd, 'update_time' => time(), 'status' => 1, 'token' => '']);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }

    public function update(Request $request)
    {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['id']) || empty($input['name']) || empty($input['actions'])) {
            return ['ServerNo' => 400, 'ResultData' => '角色id或角色名称或权限不能为空'];
        }

        DB::beginTransaction();
        try {
            $roleId = self::$dataRoleRepository->updateData(['id' => $input['id']], ['name' => $input['name'], 'update_time' => time()]);
            // 删除所有action
            self::$relRoleActionRepository->deleteData(['role_id' => $input['id']]);
            $actions = [];
            foreach ($input['actions'] as $k => $v) {
                $actions[$k] = [
                    'role_action_id' => $v,
                    'role_id' => $roleId,
                ];
            }
            $actionRes   = self::$relRoleActionRepository->insertData($actions);
            if (!empty($roleId) && !empty($actionRes)) {
                DB::commit();
                return ['ServerNo' => 200, 'ResultData' => '操作成功'];
            } else {
                DB::rollBack();
                return ['ServerNo' => 400, 'ResultData' => '操作失败'];
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return ['ServerNo' => 400, 'ResultData' => '操作失败'];
        }
    }

    public function delete(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['id'])) {
            return ['ServerNo' => 400, 'ResultData' => 'id不能为空'];
        }

        // 查询最新的一条数据
        $result = self::$dataAdminRepository->deleteData(['id' => $input['id']]);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }
}

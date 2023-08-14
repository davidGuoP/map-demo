<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\DataRoleActionRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class RoleActionController extends BaseController
{
    private static $dataRoleActionRepository = null;

    public function __construct(DataRoleActionRepository $dataRoleActionRepository)
    {
        self::$dataRoleActionRepository = $dataRoleActionRepository;
    }

    public function parentSave(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['name'])) {
            return ['ServerNo' => 400, 'ResultData' => '模块名称不能为空'];
        }

        if (empty($input['id'])) {
            // 查询姓名是否存在
            $data = self::$dataRoleActionRepository->getCount(['name' => $input['name']]);
            if ($data) {
                return ['ServerNo' => 400, 'ResultData' => '模块名称重复'];
            }
            // 查询最新的一条数据
            $result = self::$dataRoleActionRepository->addData(['name' => $input['name'], 'type' => 3]);
            // 返回结果
            if ($result) {
                return ['ServerNo' => 200, 'ResultData' => '更新成功'];
            } else {
                return ['ServerNo' => 400, 'ResultData' => '更新失败'];
            }
        } else {
            // 查询姓名是否存在
            $data = self::$dataRoleActionRepository->getNoCount(['name' => $input['name']], $input['id']);
            if ($data) {
                return ['ServerNo' => 400, 'ResultData' => '模块名称重复'];
            }
            // 查询最新的一条数据
            self::$dataRoleActionRepository->updateData(['id' => $input['id']], ['name' => $input['name']]);
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
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
        $result = self::$dataRoleActionRepository->deleteData(['id' => $input['id']]);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }

    public function childrenSave(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['name']) || empty($input['type'] || empty($input['action']))) {
            return ['ServerNo' => 400, 'ResultData' => '权限名称或类型或权限值不能为空'];
        }

        $data = self::$dataRoleActionRepository->getCount(['name' => $input['name']]);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '权限名称重复'];
        }

        // 查询最新的一条数据
        $result = self::$dataRoleActionRepository->addData(['name' => $input['name'], 'type' => $input['type'], 'action' => $input['action'], 'parent_id' => $input['parentId']]);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }

    public function childrenUpdate(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['id']) || empty($input['name']) || empty($input['type'] || empty($input['action']))) {
            return ['ServerNo' => 400, 'ResultData' => '权限名称或类型或权限值不能为空'];
        }

        // 查询姓名是否存在
        $data = self::$dataRoleActionRepository->getNoCount(['name' => $input['name']], $input['id']);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '权限名称重复'];
        }

        // 查询最新的一条数据
        $result = self::$dataRoleActionRepository->updateData(['id' => $input['id']], ['name' => $input['name'], 'type' => $input['type'], 'action' => $input['action']]);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $where = [];
        $whereIn = [];
        if (!empty($input['type'])) {
            if ($input['type'] === 2) {
                $whereIn = [1, 2];
            } else {
                $where['type'] = $input['type'];
            }
        }
        if (!empty($input['parentId'])) {
            $where['parent_id'] = $input['parentId'];
        }

        $count = self::$dataRoleActionRepository->getCount($where);
        if (empty($count)) {
            return [
                'ServerNo' => 200, 'ResultData' => [
                    'total' => 0,
                    'data' => [],
                ]
            ];
        }
        $data = self::$dataRoleActionRepository->getInAllData($where, [], 'type', $whereIn);

        return ['ServerNo' => 200, 'ResultData' => [
            'total' => $count,
            'data' => $data,
        ]];
    }

    public function list(Request $request)
    {
        $where = [];

        $count = self::$dataRoleActionRepository->getCount($where);
        if (empty($count)) {
            return [
                'ServerNo' => 200, 'ResultData' => [
                    'total' => 0,
                    'data' => [],
                ]
            ];
        }
        $data = self::$dataRoleActionRepository->getAllData($where);

        return ['ServerNo' => 200, 'ResultData' => $this->getTree($data)];
    }

    public function getTree($arr, $id = 0, $lev = 0)
    {
        // 获取子孙树
        if (empty($arr)) {
            return [];
        }
        $tree = [];
        foreach ($arr as $v) {
            if ($v->parent_id == $id) {
                $v->level = $lev;
                $tree[] = $v;
                $tree = array_merge($tree, self::getTree($arr, $v->id, $lev + 1));
            }
        }
        return $tree;
    }
}

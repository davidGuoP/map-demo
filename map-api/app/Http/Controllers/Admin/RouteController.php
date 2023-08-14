<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\DataBarMarkerRepository;
use App\Repositories\DataRouteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RouteController extends Controller
{
    private static $dataRouteRepository = null;
    private static $dataBarMarkerRepository = null;

    public function __construct(
        DataRouteRepository $dataRouteRepository,
        DataBarMarkerRepository $dataBarMarkerRepository
    )
    {
        self::$dataRouteRepository = $dataRouteRepository;
        self::$dataBarMarkerRepository = $dataBarMarkerRepository;
    }

    public function index(Request $request) {
        $input = $request->input();
        $where = [];

        $count = self::$dataRouteRepository->getLikeCount($where, 'name', $input['name']);
        if (empty($count)) {
            return [
                'ServerNo' => 200, 'ResultData' => [
                    'total' => 0,
                    'data' => [],
                ]
            ];
        }
        $data = self::$dataRouteRepository->getLikeAllData($where, [], 'name', $input['name']);

        return ['ServerNo' => 200, 'ResultData' => [
            'total' => $count,
            'data' => $data,
        ]];
    }

    public function save(Request $request)
    {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['name']) || empty($input['type']) || empty($input['lineName'])) {
            return ['ServerNo' => 400, 'ResultData' => '非必填字段为空'];
        }
        // 查询姓名是否存在
        $data = self::$dataRouteRepository->getCount(['name' => $input['name']]);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '通道名称重复'];
        }
        $result = self::$dataRouteRepository->addData([
            'name' => $input['name'],
            'type' => $input['type'],
            'line_name' => $input['lineName'],
            'file_path' => !empty($input['fileList']) ? $input['fileList'][0] : '',
            'position' => empty($input['position']) ? '' : $input['position']
        ]);
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
        if (empty($input['id']) || empty($input['name']) || empty($input['type']) || empty($input['lineName'])) {
            return ['ServerNo' => 400, 'ResultData' => '非必填字段为空'];
        }
        // 查询姓名是否存在
        $data = self::$dataRouteRepository->getNoCount(['name' => $input['name']], $input['id']);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '通道名称重复'];
        }
        $result = self::$dataRouteRepository->updateData(['id' => $input['id']], [
            'name' => $input['name'],
            'type' => $input['type'],
            'line_name' => $input['lineName'],
            'file_path' => !empty($input['fileList']) ? $input['fileList'][0] : '',
            'position' => empty($input['position']) ? '' : $input['position']
        ]);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }

    public function info(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['id'])) {
            return ['ServerNo' => 400, 'ResultData' => 'id不能为空'];
        }

        // 查询最新的一条数据
        $result = self::$dataRouteRepository->getOneData(['id' => $input['id']]);
        // 编辑时用的坐标
        $bars = self::$dataBarMarkerRepository->getAllData(['route_id' => $input['id']], ['longitude']);
        $result->bars = $bars;
        // 查询所有关联过的柱状标识器
        $bars = self::$dataBarMarkerRepository->getRoutePageData('', $result->id);
        $result->table = $bars;
        return ['ServerNo' => 200, 'ResultData' => $result];
    }

    public function delete(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['id'])) {
            return ['ServerNo' => 400, 'ResultData' => 'id不能为空'];
        }

        // 查询最新的一条数据
        $result = self::$dataRouteRepository->deleteData(['id' => $input['id']]);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }
}

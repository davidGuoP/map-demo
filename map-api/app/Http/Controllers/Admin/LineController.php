<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\DataLineRepository;
use App\Repositories\DataVolumeMarkerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LineController extends Controller
{
    private static $dataLineRepository = null;
    private static $dataVolumeMarkerRepository = null;

    public function __construct(
        DataLineRepository $dataLineRepository,
        DataVolumeMarkerRepository $dataVolumeMarkerRepository
    )
    {
        self::$dataLineRepository = $dataLineRepository;
        self::$dataVolumeMarkerRepository = $dataVolumeMarkerRepository;
    }

    public function index(Request $request) {
        $input = $request->input();
        $where = [];

        $count = self::$dataLineRepository->getLikeCount($where, 'name', $input['name']);
        if (empty($count)) {
            return [
                'ServerNo' => 200, 'ResultData' => [
                    'total' => 0,
                    'data' => [],
                ]
            ];
        }
        $data = self::$dataLineRepository->getPageData($input['name']);

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
        if (empty($input['name']) || empty($input['routeId']) || empty($input['lineName']) || empty($input['start']) || empty($input['end'])) {
            return ['ServerNo' => 400, 'ResultData' => '非必填字段为空'];
        }
        // 查询姓名是否存在
        $data = self::$dataLineRepository->getCount(['name' => $input['name']]);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '通道名称重复'];
        }
        $result = self::$dataLineRepository->addData([
            'name' => $input['name'],
            'route_id' => $input['routeId'],
            'line_name' => $input['lineName'],
            'start' => $input['start'],
            'end' => $input['end'],
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
        if (empty($input['id']) || empty($input['routeId']) || empty($input['lineName']) || empty($input['start']) || empty($input['end'])) {
            return ['ServerNo' => 400, 'ResultData' => '非必填字段为空'];
        }
        // 查询姓名是否存在
        $data = self::$dataLineRepository->getNoCount(['name' => $input['name']], $input['id']);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '电缆名称重复'];
        }
        $result = self::$dataLineRepository->updateData(['id' => $input['id']], [
            'name' => $input['name'],
            'route_id' => $input['routeId'],
            'line_name' => $input['lineName'],
            'start' => $input['start'],
            'end' => $input['end'],
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

        // 查询最新的一条数据line_id
        $result = self::$dataLineRepository->getLineOneData($input['id']);
        // 编辑时用的坐标
        $volumes = self::$dataVolumeMarkerRepository->getAllData(['line_id' => $input['id']], ['longitude']);
        $result->volumes = $volumes;
        // 查询所有关联过的柱状标识器
        $volumes = self::$dataVolumeMarkerRepository->getLinePageData('', $result->id);
        $result->table = $volumes;
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
        $result = self::$dataLineRepository->deleteData(['id' => $input['id']]);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }
}

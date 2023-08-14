<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\DataBarMarkerRepository;
use App\Repositories\DataVolumeMarkerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarMarkerController extends Controller
{
    private static $dataBarMarkerRepository = null;
    private static $dataVolumeMarkerRepository = null;

    public function __construct(
        DataBarMarkerRepository $dataBarMarkerRepository,
        DataVolumeMarkerRepository $dataVolumeMarkerRepository
    )
    {
        self::$dataBarMarkerRepository = $dataBarMarkerRepository;
        self::$dataVolumeMarkerRepository = $dataVolumeMarkerRepository;
    }

    public function index(Request $request) {
        $input = $request->input();
        $where = [];

        $count = self::$dataBarMarkerRepository->getLikeCount($where, 'name', $input['name']);
        if (empty($count)) {
            return [
                'ServerNo' => 200, 'ResultData' => [
                    'total' => 0,
                    'data' => [],
                ]
            ];
        }
        $data = self::$dataBarMarkerRepository->getPageData($input['name']);

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
        if (empty($input['code']) || empty($input['name']) || empty($input['routeId']) || empty($input['longitude'])) {
            return ['ServerNo' => 400, 'ResultData' => '非必填字段为空'];
        }
        // 查询姓名是否存在
        $data = self::$dataBarMarkerRepository->getCount(['name' => $input['name']]);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '柱状名称重复'];
        }

        DB::beginTransaction();
        try {
            $result = self::$dataBarMarkerRepository->addData([
                'code' => $input['code'],
                'name' => $input['name'],
                'route_id' => $input['routeId'],
                'volume_ids' => $input['volumeIds'] ? implode(",", $input['volumeIds']) : '',
                'longitude' => $input['longitude'],
                'file_path' => $input['fileList'] ? $input['fileList'][0] : ''
            ]);
            // 查询柱状是否有关联
            if (!empty($input['volumeIds'])) {
                // 将所有的片状添加关系
                $result = self::$dataVolumeMarkerRepository->updateWhereInData($input['volumeIds'], ['bar_id' => $result]);
            }

            if (!empty($result)) {
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

    public function update(Request $request)
    {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['id']) || empty($input['code']) || empty($input['name']) || empty($input['routeId']) || empty($input['longitude'])) {
            return ['ServerNo' => 400, 'ResultData' => '非必填字段为空'];
        }
        // 查询姓名是否存在
        $data = self::$dataBarMarkerRepository->getNoCount(['name' => $input['name']], $input['id']);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '通道名称重复'];
        }

        $bar = self::$dataBarMarkerRepository->getOneData(['id' => $input['id']]);

        DB::beginTransaction();
        try {
            $result = self::$dataBarMarkerRepository->updateData(['id' => $input['id']], [
                'code' => $input['code'],
                'name' => $input['name'],
                'route_id' => $input['routeId'],
                'volume_ids' => $input['volumeIds'] ? implode(",", $input['volumeIds']) : '',
                'longitude' => $input['longitude'],
                'file_path' => $input['fileList'] ? $input['fileList'][0] : '',
                'update_time' => time()
            ]);
            // 查询柱状是否有关联
            if (implode(',', $input['volumeIds']) != $bar->volume_ids) {
                // 算交集
                $intersect = array_intersect($input['volumeIds'], explode(',', $bar->volume_ids));
                // 传入的值与交集算差集，这些是需要添加的
                $inputDiff = array_diff($input['volumeIds'], $intersect);
                if (!empty($inputDiff)) {
                    foreach($inputDiff as $k=>$v){
                        if(!$v) unset($inputDiff[$k]);
                    }
                    // 将所有的片状添加关系
                    if (!empty($inputDiff)) {
                        $result = self::$dataVolumeMarkerRepository->updateWhereInData($inputDiff, ['bar_id' => $input['id']]);
                    }
                }
                // 本来的值与交集算差集，这些是需要删除的
                $diff = array_diff(explode(',', $bar->volume_ids), $intersect);
                if (!empty($diff)) {
                    foreach($diff as $k=>$v){
                        if(!$v) unset($diff[$k]);
                    }
                    // 将所有的片状添加关系
                    if (!empty($diff)) {
                        $result = self::$dataVolumeMarkerRepository->updateWhereInData($diff, ['bar_id' => '']);
                    }
                }
            }

            if (!empty($result)) {
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

    public function info(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['id'])) {
            return ['ServerNo' => 400, 'ResultData' => 'id不能为空'];
        }

        // 查询最新的一条数据
        $result = self::$dataBarMarkerRepository->getRouteOneData($input['id']);
        if ($result && $result->volume_ids) {
            // 查询所有绑定的片标识器
            $result->table = self::$dataVolumeMarkerRepository->getPageData('', explode(',', $result->volume_ids));
        }
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
        $result = self::$dataBarMarkerRepository->deleteData(['id' => $input['id']]);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }
}

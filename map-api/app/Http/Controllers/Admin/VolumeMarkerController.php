<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\DataBarMarkerRepository;
use App\Repositories\DataVolumeMarkerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VolumeMarkerController extends Controller
{
    private static $dataVolumeMarkerRepository = null;
    private static $dataBarMarkerRepository = null;

    public function __construct(
        DataVolumeMarkerRepository $dataVolumeMarkerRepository,
        DataBarMarkerRepository  $dataBarMarkerRepository
    )
    {
        self::$dataVolumeMarkerRepository = $dataVolumeMarkerRepository;
        self::$dataBarMarkerRepository = $dataBarMarkerRepository;
    }

    public function index(Request $request) {
        $input = $request->input();
        $where = [];

        $count = self::$dataVolumeMarkerRepository->getLikeCount($where, 'name', $input['name']);
        if (empty($count)) {
            return [
                'ServerNo' => 200, 'ResultData' => [
                    'total' => 0,
                    'data' => [],
                ]
            ];
        }
        $data = self::$dataVolumeMarkerRepository->getPageData($input['name']);

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
        if (empty($input['code']) || empty($input['name']) || empty($input['lineId']) || empty($input['longitude'])) {
            return ['ServerNo' => 400, 'ResultData' => '非必填字段为空'];
        }
        // 查询姓名是否存在
        $data = self::$dataVolumeMarkerRepository->getCount(['name' => $input['name']]);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '柱状名称重复'];
        }

        DB::beginTransaction();
        try {
            $result = self::$dataVolumeMarkerRepository->addData([
                'code' => $input['code'],
                'name' => $input['name'],
                'line_id' => $input['lineId'],
                'bar_id' => empty($input['barId']) ? '' : $input['barId'],
                'longitude' => $input['longitude'],
                'file_path' => $input['fileList'] ? $input['fileList'][0] : ''
            ]);
            // 查询柱状是否有关联
            if (!empty($input['barId'])) {
                $bar = self::$dataBarMarkerRepository->getOneData(['id' => $input['barId']]);
                $volumeIds = explode(',', $bar->volume_ids);
                array_push($volumeIds, $result);
                foreach($volumeIds as $k=>$v){
                    if(!$v) unset($volumeIds[$k]);
                }
                $result = self::$dataBarMarkerRepository->updateData(['id' => $input['barId']], ['volume_ids' => implode(',', $volumeIds)]);
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
        if (empty($input['id']) || empty($input['code']) || empty($input['name']) || empty($input['lineId']) || empty($input['longitude'])) {
            return ['ServerNo' => 400, 'ResultData' => '非必填字段为空'];
        }
        // 查询姓名是否存在
        $data = self::$dataVolumeMarkerRepository->getNoCount(['name' => $input['name']], $input['id']);
        if ($data) {
            return ['ServerNo' => 400, 'ResultData' => '标识器名称重复'];
        }
        // 查询最新的一条数据
        $volume = self::$dataVolumeMarkerRepository->getLineOneData($input['id']);

        DB::beginTransaction();
        try {
            $result = self::$dataVolumeMarkerRepository->updateData(['id' => $input['id']], [
                'code' => $input['code'],
                'name' => $input['name'],
                'bar_id' => empty($input['barId']) ? '' : $input['barId'],
                'longitude' => $input['longitude'],
                'file_path' => $input['fileList'] ? $input['fileList'][0] : '',
                'update_time' => time()
            ]);
            // 查询柱状是否有关联
            if ($input['barId'] != $volume->bar_id) {
                // 如果bar有值
                if ($volume->bar_id) {
                    // 只要旧记录有bar_id,去掉关联表记录
                    $bar = self::$dataBarMarkerRepository->getOneData(['id' => $volume->bar_id]);
                    if ($bar) {
                        $volumeIds = explode(',', $bar->volume_ids);
                        $key = array_search($volume->id, $volumeIds);
                        if ($key !== false) {
                            array_splice($volumeIds, $key, 1);
                        }
                        foreach($volumeIds as $k=>$v){
                            if(!$v) unset($volumeIds[$k]);
                        }
                        if ($bar->volume_ids != implode(',', $volumeIds)) {
                            $result = self::$dataBarMarkerRepository->updateData(['id' => $volume->bar_id], ['volume_ids' => implode(',', $volumeIds)]);
                        }
                    }
                }

                // 传参有值,则加入
                if (!empty($input['barId'])) {
                    $bar1 = self::$dataBarMarkerRepository->getOneData(['id' => $input['barId']]);
                    if ($bar1) {
                        $volumeIds1 = explode(',', $bar1->volume_ids);
                        if (!empty($volume->id)) {
                            $key = array_search($volume->id, $volumeIds1);
                            if ($key !== false) {
                                array_splice($volumeIds1, $key, 1);
                            }
                        }
                        array_push($volumeIds1, $volume->id);
                        foreach($volumeIds1 as $k=>$v){
                            if(!$v) unset($volumeIds1[$k]);
                        }
                        if ($bar1->volume_ids != implode(',', $volumeIds1)) {
                            $result = self::$dataBarMarkerRepository->updateData(['id' => $input['barId']], ['volume_ids' => implode(',', $volumeIds1)]);
                        }
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
        $result = self::$dataVolumeMarkerRepository->getLineOneData($input['id']);
        // 查询所有的柱状
        if ($result && $result->bar_id) {
            $result->table = self::$dataBarMarkerRepository->getPageData('', $result->bar_id);
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
        $result = self::$dataVolumeMarkerRepository->deleteData(['id' => $input['id']]);
        // 返回结果
        if ($result) {
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            return ['ServerNo' => 400, 'ResultData' => '更新失败'];
        }
    }
}

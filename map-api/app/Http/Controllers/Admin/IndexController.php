<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\DataBarMarkerRepository;
use App\Repositories\DataLineRepository;
use App\Repositories\DataRouteRepository;
use App\Http\Controllers\Controller;
use App\Repositories\DataVolumeMarkerRepository;

class IndexController extends Controller
{
    private static $dataRouteRepository = null;
    private static $dataLineRepository = null;
    private static $dataBarMarkerRepository = null;
    private static $dataVolumeMarkerRepository = null;

    public function __construct(
        DataRouteRepository $dataRouteRepository,
        DataLineRepository $dataLineRepository,
        DataBarMarkerRepository $dataBarMarkerRepository,
        DataVolumeMarkerRepository  $dataVolumeMarkerRepository
    )
    {
        self::$dataRouteRepository = $dataRouteRepository;
        self::$dataLineRepository = $dataLineRepository;
        self::$dataBarMarkerRepository = $dataBarMarkerRepository;
        self::$dataVolumeMarkerRepository = $dataVolumeMarkerRepository;
    }

    public function index() {
        $routeCount = self::$dataRouteRepository->getCount();

        $lineCount = self::$dataLineRepository->getCount();

        $barCount = self::$dataBarMarkerRepository->getCount();

        $volumeCount = self::$dataVolumeMarkerRepository->getCount();

        return [
            'ServerNo' => 200, 'ResultData' => [
                'total' => 0,
                'data' => [
                    'route_count' => $routeCount,
                    'line_count' => $lineCount,
                    'bar_count' => $barCount,
                    'volume_count' => $volumeCount
                ],
            ]
        ];
    }

    public function list()
    {
        // 查询所有的数字通道
        $routes = self::$dataRouteRepository->getAllData([], ['id', 'name', 'position']);
        // 查询下面所有的柱状图
        foreach ($routes as $v) {
            $bars = self::$dataBarMarkerRepository->getAllData(['route_id' => $v->id], ['id', 'name', 'longitude', 'volume_ids']);
            foreach ($bars as $item) {
                $item->type = 'bar';
                if ($item->volume_ids) {
                    // 查询所有的片的坐标
                    $volumes = self::$dataVolumeMarkerRepository->getWhereInVolume(explode(',', $item->volume_ids), ['id', 'name', 'longitude']);
                    foreach ($volumes as $value) {
                        $value->type = 'volume';
                    }
                    $item->volume_longitude = $volumes;
                }
            }
            $v->type = 'route';
            $v->children = $bars;
        }

        // 查询所有数字电缆
        $lines = self::$dataLineRepository->getPageData([], ['id', 'name', 'position']);
        // 查询下面所有的片状图
        foreach ($lines as $v) {
            $volumes = self::$dataVolumeMarkerRepository->getAllData(['line_id' => $v->id], ['id', 'name', 'longitude']);
            foreach ($volumes as $item) {
                $item->type = 'volume';
            }
            $v->type = 'line';
            $v->children = $volumes;
        }

        return [
            'ServerNo' => 200, 'ResultData' => [
                'total' => 0,
                'data' => [
                    'routes' => $routes,
                    'lines' => $lines,
                ],
            ]
        ];
    }
}

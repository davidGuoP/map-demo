<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\DataWebNameRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class WebNameController extends BaseController
{
    private static $dataWebNameRepository = null;

    public function __construct(DataWebNameRepository $dataWebNameRepository)
    {
        self::$dataWebNameRepository = $dataWebNameRepository;
    }

    public function info() {
        // 查询最新的一条数据
        $result = self::$dataWebNameRepository->getFirstData();
        // 返回结果
        return ['ServerNo' => 200, 'ResultData' => $result];
    }

    public function save(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['name'])) {
            return ['ServerNo' => 400, 'ResultData' => '网站名称不能为空'];
        }

        // 查询最新的一条数据
        $data = self::$dataWebNameRepository->getFirstData();
        $updateData = ['name' => $input['name']];
        if ($data) {
            self::$dataWebNameRepository->updateData(['id' => $data->id], $updateData);
            return ['ServerNo' => 200, 'ResultData' => '更新成功'];
        } else {
            $result = self::$dataWebNameRepository->addData($updateData);

            // 返回结果
            if ($result) {
                return ['ServerNo' => 200, 'ResultData' => '更新成功'];
            } else {
                return ['ServerNo' => 400, 'ResultData' => '更新失败'];
            }
        }
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')){
            //获取文件
            $file = $request->file('file');
            $time = date('Ymd',time());
            // 文件是否上传成功
            if ($file->isValid()) {
                // 获取文件相关信息
                $originalName = $file->getClientOriginalName(); // 文件原名
                $ext = $file->getClientOriginalExtension();     // 扩展名
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                $type = $file->getClientMimeType();     // image/jpeg
                // 上传文件
                $filename = uniqid();
                $data['pic_path'] = 'map/'.$filename.'.'.$ext;
                // 这里的uploads是配置文件的名称
                $bool = Storage::disk('local')->put('/'.$data['pic_path'], file_get_contents($realPath));
                //判断是否创建成功
                if (!$bool)
                {
                    return ['ServerNo' => 400, 'ResultData' => '添加文件失败'];
                } else {
                    return ['ServerNo' => 200, 'ResultData' => '/'.$data['pic_path']];
                }
            }
        }
    }

    public function download(Request $request)
    {
        // return Storage::disk('local')->download($request->input(['name']));
        $path= $request->input()['name'];
        if(Storage::exists($path))
        {
            $file=Storage::get($path);
            $arr = explode('.', $path);
            $response = Response::create($file, 200);
            $response->header("Content-Type", end($arr));
            return $response;
        }
        abort(404);
        // return Storage::(storage_path('app'). $request->input()['name']);
        // return response()->download(storage_path('app'). $request->input()['name']);
//        return (new Response(storage_path('app'). $request->input()['name'], 200))
//                ->header(
//                    'Content-Disposition',
//                    'attachment; filename="' .  $request->input()['name'] . '"'
//                )->header(
//                    'Content-Type',
//                    $request->input()['name']
//                );
    }
}

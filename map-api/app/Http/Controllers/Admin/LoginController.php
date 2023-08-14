<?php

namespace App\Http\Controllers\Admin;

use App\Library\Tools\Common\Common;
use App\Repositories\DataAdminRepository;
use App\Repositories\DataRoleActionRepository;
use App\Repositories\DataRoleRepository;
use App\Repositories\RelRoleActionRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class LoginController extends BaseController
{
    private static $adminRepository = null;
    private static $dataRoleActionRepository = null;
    private static $dataRoleRepository = null;
    private static $relRoleActionRepository = null;
    protected $redis;

    public function __construct(
        DataAdminRepository $adminRepository,
        DataRoleActionRepository $dataRoleActionRepository,
        DataRoleRepository $dataRoleRepository,
        RelRoleActionRepository $relRoleActionRepository)
    {
        self::$adminRepository = $adminRepository;
        self::$dataRoleActionRepository = $dataRoleActionRepository;
        self::$dataRoleRepository = $dataRoleRepository;
        self::$relRoleActionRepository = $relRoleActionRepository;
    }

    public function login(Request $request) {
        // 获取数据
        $input = $request->input();
        // 数据验证
        if (empty($input['username']) || empty($input['password'])) {
            return ['ServerNo' => 400, 'ResultData' => '用户名与密码不能为空'];
        }

        // 密码加盐
        $password = Common::md($input['password']);
        // 查询数据库
        $data = self::$adminRepository->getOneData(['username' => $input['username']]);
        // 判断用户是否存在或密码是否正确
        if (empty($data) || ($password != $data->password)) {
            return ['ServerNo' => 400, 'ResultData' => '用户名与密码不正确'];
        }

        // 查看非管理员是否有权限
        if ($data->username !== 'admin') {
            if (empty($data->role_id)) {
                return ['ServerNo' => 400, 'ResultData' => '该账号没有设置权限,请联系管理员'];
            }
            $action = self::$relRoleActionRepository->getCount(['role_id' => $data->role_id]);
            if (empty($action)) {
                return ['ServerNo' => 400, 'ResultData' => '该账号没有设置权限,请联系管理员'];
            }
        }

        // 更新token
        $token = Common::getUuid();
        $result = self::$adminRepository->updateData(['guid' => $data->guid], ['token' => $token, 'login_time' => time()]);
        if (empty($result)) {
            return ['ServerNo' => 400, 'ResultData' => '登陆失败,请重新登录!'];
        }

        // 返回结果
        return ['ServerNo' => 200, 'ResultData' => [
            'guid'     => $data->guid,
            'username' => $data->username,
            'token'    => $token
        ]];
    }

    public function info(Request $request)
    {
        $input = $request->all();
        // 查询用户
        $result = self::$adminRepository->getOneData(['guid' => $input['guid']]);
        if ($result->token != $input['token']) {
            return ['ServerNo' => 501, 'ResultData' => '用户在其它地方登录'];
        }
        // 查找所有的action
        $roleAction = $result->role_id ? self::$relRoleActionRepository->getRoleActionData($result->role_id) : [];

        return ['ServerNo' => 200, 'ResultData' => [
            'name' => $result->username,
            'role_action' =>$roleAction
        ]];
    }

    public function editPassword(Request $request)
    {
        $input = $request->all();
        // 数据验证
        if (empty($input['oldPassword']) || empty($input['newPassword'])) {
            return ['ServerNo' => 400, 'ResultData' => '新旧密码不能为空'];
        }
        // 检查旧密码是否正确
        $password = Common::md($input['oldPassword']);
        // 查询数据库
        $result = self::$adminRepository->getOneData(['guid' => $input['guid']]);
        // 判断用户是否存在或密码是否正确
        if (empty($result) || ($password != $result->password)) {
            return ['ServerNo' => 400, 'ResultData' => '旧密码不正确'];
        }
        // 更新新密码
        $newPwd = Common::md($input['newPassword']);
        $result = self::$adminRepository->updateData(['guid' => $input['guid']], ['password' => $newPwd]);
        if ($result) {
            return ['ServerNo' => 501, 'ResultData' => '修改密码失败'];
        }
        return ['ServerNo' => 200, 'ResultData' => '修改密码成功'];
    }
}

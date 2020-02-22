<?php
/**
 * Project: 鱼跃CMS - Enterprise cms developed by catfish cms.
 * Producer: catfish cms [ http://www.catfish-cms.com ]
 * Author: A.J <804644245@qq.com>
 * License: http://www.yuyue-cms.com/page/agreement.html
 * Copyright: http://www.yuyue-cms.com All rights reserved.
 */
namespace app\login\controller;

use catfishcms\Catfish;

class CatfishCMS
{
    protected function checkUser()
    {
        if(!is_file(APP_PATH . 'database.php')){
            Catfish::redirect(Catfish::oUrl('install/Index/index'));
            exit();
        }
        if(!Catfish::hasSession('user_id'))
        {
            $this->options();
        }
        else{
            $user_type = Catfish::getSession('user_type');
            if(in_array($user_type,Catfish::department('admin'))){
                Catfish::redirect('admin/Index/index');
                exit();
            }
            else{
                Catfish::redirect('user/Index/index');
                exit();
            }
        }
    }
    private function options()
    {
        $data_options = Catfish::autoload();
        foreach($data_options as $key => $val)
        {
            if($val['name'] == 'copyright' || $val['name'] == 'statistics')
            {
                Catfish::allot($val['name'], unserialize($val['value']));
            }
            else
            {
                Catfish::allot($val['name'], $val['value']);
            }
        }
    }
    protected function chklogin($captcha)
    {
        if(Catfish::getPost('captcha') !== false)
        {
            $rule = [
                'user' => 'require',
                'pwd' => 'require',
                'captcha|'.Catfish::lang('Captcha')=>'require|captcha'
            ];
        }
        else
        {
            $rule = [
                'user' => 'require',
                'pwd' => 'require'
            ];
        }
        $msg = [
            'user.require' => Catfish::lang('The user name must be filled in'),
            'pwd.require' => Catfish::lang('Password must be filled in')
        ];
        if($captcha == 1)
        {
            $data = [
                'user' => Catfish::getPost('user'),
                'pwd' => Catfish::getPost('pwd'),
                'captcha' => Catfish::getPost('captcha')
            ];
        }
        else
        {
            $data = [
                'user' => Catfish::getPost('user'),
                'pwd' => Catfish::getPost('pwd')
            ];
        }
        $validate = Catfish::validate($rule, $msg, $data);
        if($validate !== true)
        {
            return $validate;
        }
        else{
            return $data;
        }
    }
    protected function logined(&$user, &$data)
    {
        $ip = Catfish::ip();
        Catfish::db('users')
            ->where('id', $user['id'])
            ->update(['lip' => $ip]);
        Catfish::db('login')->insert([
            'userid' => $user['id'],
            'loginip' => $ip,
            'logintime' => Catfish::now()
        ]);
        Catfish::setSession('user_id',$user['id']);
        Catfish::setSession('user',$data['user']);
        Catfish::setSession('user_type',$user['utype']);
        if(Catfish::getPost('remember'))
        {
            Catfish::setCookie('user_id',$user['id'],604800);
            Catfish::setCookie('user',$data['user'],604800);
            $cookie_user_p = Catfish::getCache('cookie_user_p');
            if($cookie_user_p == false)
            {
                $cookie_user_p = md5(time());
                Catfish::setCache('cookie_user_p',$cookie_user_p,604800);
            }
            Catfish::setCookie('user_p',md5($cookie_user_p.$user['password'].$user['randomcode']),604800);
        }
    }
    protected function chkretpwd()
    {
        $rule = [
            'user' => 'require',
            'email' => 'require|email',
            'captcha|'.Catfish::lang('Captcha')=>'require|captcha'
        ];
        $msg = [
            'user.require' => Catfish::lang('The user name must be filled in'),
            'email.require' => Catfish::lang('E-mail address is required'),
            'email.email' => Catfish::lang('The e-mail format is incorrect')
        ];
        $data = [
            'user' => Catfish::getPost('user'),
            'email' => Catfish::getPost('email'),
            'captcha' => Catfish::getPost('captcha')
        ];
        $validate = Catfish::validate($rule, $msg, $data);
        if($validate !== true)
        {
            return $validate;
        }
        else{
            return $data;
        }
    }
}
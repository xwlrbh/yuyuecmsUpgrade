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
class Index extends CatfishCMS
{
    public function index()
    {
        $this->checkUser();
        $captcha = Catfish::get('captcha');
        if(Catfish::getPost('user') !== false)
        {
            $data = $this->chklogin($captcha);
            if(!is_array($data)){
                Catfish::error($data);
            }
            else{
                $user = Catfish::db('users')->where('yonghu',$data['user'])->field('id,password,randomcode,status,utype')->find();
                if(empty($user))
                {
                    Catfish::error(Catfish::lang('Username error'));
                    return false;
                }
                if($user['password'] != md5($data['pwd'].$user['randomcode']))
                {
                    Catfish::error(Catfish::lang('Password error'));
                    return false;
                }
                if($user['status'] == 0)
                {
                    Catfish::error(Catfish::lang('Account has been disabled, please contact the administrator'));
                    return false;
                }
                $params = [
                    'logined' => true,
                    'user' => $data['user'],
                    'password' => $data['pwd'],
                    'result' => ''
                ];
                $this->plantHook('login', $params);
                if($params['logined']){
                    $this->logined($user, $data);
                }
                else{
                    Catfish::error($params['result']);
                    return false;
                }
            }
        }
        if(Catfish::hasSession('user_id')){
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
        Catfish::allot('yanzheng', $captcha);
        $estis = Catfish::get('emailsettings');
        if(empty($estis)){
            Catfish::allot('retpwd', 0);
        }
        else{
            Catfish::allot('retpwd', 1);
        }
        $view = Catfish::output();
        return $view;
    }
    public function denglu()
    {
        if(Catfish::getPost('user') !== false)
        {
            $captcha = Catfish::get('captcha');
            if(Catfish::getPost('captcha') !== false || $captcha == 1){
                $captcha = 1;
            }
            $data = $this->chklogin($captcha);
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $user = Catfish::db('users')->where('yonghu',$data['user'])->field('id,password,randomcode,status,utype')->find();
                if(empty($user))
                {
                    echo Catfish::lang('Username error');
                    exit();
                }
                if($user['password'] != md5($data['pwd'].$user['randomcode']))
                {
                    echo Catfish::lang('Password error');
                    exit();
                }
                if($user['status'] == 0)
                {
                    echo Catfish::lang('Account has been disabled, please contact the administrator');
                    exit();
                }
                $params = [
                    'logined' => true,
                    'user' => $data['user'],
                    'password' => $data['pwd'],
                    'result' => ''
                ];
                $this->plantHook('login', $params);
                if($params['logined']){
                    $this->logined($user, $data);
                    echo 'ok';
                }
                else{
                    echo $params['result'];
                }
                exit();
            }
        }
    }
    public function register()
    {
        $this->checkUser();
        if(Catfish::getPost('user') !== false && $this->allowLogin == 1)
        {
            $rule = [
                'user' => 'require',
                'pwd' => 'require',
                'repeat' => 'require',
                'email' => 'require|email'
            ];
            $msg = [
                'user.require' => Catfish::lang('The user name must be filled in'),
                'pwd.require' => Catfish::lang('Password must be filled in'),
                'repeat.require' => Catfish::lang('Confirm password is required'),
                'email.require' => Catfish::lang('E-mail address is required'),
                'email.email' => Catfish::lang('The e-mail format is incorrect')
            ];
            $data = [
                'user' => Catfish::getPost('user'),
                'pwd' => Catfish::getPost('pwd'),
                'repeat' => Catfish::getPost('repeat'),
                'email' => Catfish::getPost('email')
            ];
            $validate = Catfish::validate($rule, $msg, $data);
            if($validate !== true)
            {
                Catfish::error($validate);
                return false;
            }
            if(Catfish::getPost('pwd') != Catfish::getPost('repeat'))
            {
                Catfish::error(Catfish::lang('Confirm the password must be the same as the password'));
                return false;
            }
            $filter = Catfish::get('filtername');
            if(!empty($filter)){
                $filter = Catfish::toComma($filter);
                $filter = explode(',', $filter);
                if(in_array($data['user'], $filter)){
                    Catfish::error(Catfish::lang('Please use a different username'));
                    return false;
                }
            }
            $user = Catfish::db('users')->where('yonghu',$data['user'])->field('id')->find();
            if(!empty($user))
            {
                Catfish::error(Catfish::lang('User name has been registered'));
                return false;
            }
            $create_date = Catfish::now();
            $rmd = md5($create_date . '_' . rand());
            Catfish::db('users')->insert([
                'yonghu' => $data['user'],
                'password' => md5($data['pwd'].$rmd),
                'nicheng' => substr($rmd,0,6),
                'email' => $data['email'],
                'createtime' => $create_date,
                'randomcode' => $rmd,
                'utype' => 'visitor'
            ]);
            Catfish::success(Catfish::lang('User registration is successful'), Catfish::url('login/Index/index'));
        }
        if($this->allowLogin == 0){
            Catfish::redirect('index/Index/error');
            exit();
        }
        $view = Catfish::output();
        return $view;
    }
    public function retpwd()
    {
        $this->checkUser();
        if(Catfish::getPost('user') !== false)
        {
            $data = $this->chkretpwd();
            if(!is_array($data)){
                Catfish::error($data);
            }
            else{
                $user = Catfish::db('users')->where('yonghu',$data['user'])->field('id,email,randomcode')->find();
                if(empty($user)){
                    Catfish::error(Catfish::lang('Username error'));
                }
                elseif($user['email'] != $data['email']){
                    Catfish::error(Catfish::lang('Email error'));
                }
                $newpwd = uniqid();
                Catfish::db('users')->where('id',$user['id'])->update([
                    'password' => md5($newpwd.$user['randomcode'])
                ]);
                Catfish::sendmail($data['email'], $data['user'], Catfish::lang('Retrieve password'), Catfish::lang('This is your new password, please change your password immediately after login.'). '<br><br>'.Catfish::lang('Password').': '.$newpwd);
                Catfish::success(Catfish::lang('The new password has been sent to your email address'), Catfish::url('login/Index/index'));
            }
        }
        $view = Catfish::output();
        return $view;
    }
    public function quit()
    {
        Catfish::deleteSession('user_id');
        Catfish::deleteSession('user');
        Catfish::deleteSession('user_type');
        Catfish::deleteCookie('user_id');
        Catfish::deleteCookie('user');
        Catfish::deleteCookie('user_p');
        Catfish::redirect('index/Index/index');
        exit();
    }
}
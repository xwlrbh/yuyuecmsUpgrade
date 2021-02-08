<?php
/**
 * Project: 鱼跃CMS - Enterprise cms developed by catfish cms.
 * Producer: catfish cms [ http://www.catfish-cms.com ]
 * Author: A.J <804644245@qq.com>
 * License: http://www.yuyue-cms.com/page/agreement.html
 * Copyright: http://www.yuyue-cms.com All rights reserved.
 */
namespace app\user\controller;
use catfishcms\Catfish;
class CatfishCMS
{
    protected $template = 'default';
    protected function checkUser()
    {
        if(!Catfish::hasSession('user_id') && Catfish::hasCookie('user_id')){
            $cookie_user_p = Catfish::getCache('cookie_user_p');
            if($cookie_user_p !== false && Catfish::hasCookie('user_p')){
                $user = Catfish::db('users')->where('id',Catfish::getCookie('user_id'))->field('id,yonghu,password,randomcode,status,utype')->find();
                if(!empty($user) && $user['status'] == 1 && Catfish::getCookie('user_p') == md5($cookie_user_p.$user['password'].$user['randomcode'])){
                    Catfish::setSession('user_id',$user['id']);
                    Catfish::setSession('user',$user['yonghu']);
                    Catfish::setSession('user_type',$user['utype']);
                }
            }
        }
        if(!Catfish::hasSession('user_id'))
        {
            Catfish::redirect('login/Index/index');
            exit();
        }
        elseif(!Catfish::checkUser()){
            Catfish::redirect('login/Index/quit');
            exit();
        }
        $this->options();
        Catfish::setConfig('paginate.type', 'bootstrap4');
    }
    protected function show($menuname = '', $current = '', $star = false, $template = null)
    {
        $dn = Catfish::rtmt()?Catfish::bd('IGQtbm9uZQ=='):'';
        Catfish::allot('menuname', $menuname);
        Catfish::allot('current', $current);
        Catfish::allot('star', $star);
        Catfish::allot('user', Catfish::getSession('user'));
        Catfish::allot('tuichu', Catfish::url('login/Index/quit'));
        Catfish::allot('yuyuecms', '<a href="'.base64_decode('aHR0cDovL3d3dy55dXl1ZS1jbXMuY29t').'" class="text-muted'.$dn.'" target="_blank">'.base64_decode('6bG86LeDQ01T').'</a>');
        return Catfish::output($template);
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
            elseif($val['name'] == 'template'){
                $this->template = $val['value'];
                Catfish::allot($val['name'], $val['value']);
            }
            elseif($val['name'] == 'domain'){
                $root = Catfish::domainAmend($val['value']);
                Catfish::allot($val['name'], $root);
                $dm = Catfish::url('/');
                if(strpos($dm,'/index.php') !== false)
                {
                    $root .= 'index.php/';
                }
                Catfish::allot('root', $root);
            }
            else
            {
                Catfish::allot($val['name'], $val['value']);
            }
        }
        $pluginsOpened = Catfish::get('plugins_opened');
        $pluginItem = [];
        if(!empty($pluginsOpened)){
            $pluginsOpened = unserialize($pluginsOpened);
            foreach($pluginsOpened as $key => $val){
                $params = [
                    'pluginName' => $val
                ];
                $this->userHook($val, 'addUserPlugin', $params);
                if(isset($params['item'])){
                    $this->getext($params['item'], $pluginItem);
                }
            }
        }
        $uftheme = ucfirst($this->template);
        if(is_file(ROOT_PATH.'public' . DS . 'theme' . DS . $this->template . DS . $uftheme .'.php')){
            $params = [
                'pluginName' => ''
            ];
            $this->themeHook('addUserPlugin', $params, $this->template);
            if(isset($params['item'])){
                $this->getext($params['item'], $pluginItem, $this->template);
            }
        }
        $hasPlugin = count($pluginItem);
        Catfish::allot('hasPlugin', $hasPlugin);
        Catfish::allot('pluginItem', $pluginItem);
        Catfish::allot('verification', Catfish::verifyCode());
    }
    private function validatePost(&$rule, &$msg, &$data)
    {
        $validate = Catfish::validate($rule, $msg, $data);
        if($validate !== true)
        {
            return $validate;
        }
        else{
            return $data;
        }
    }
    protected function editprofilePost()
    {
        $rule = [
            'email' => 'require|email'
        ];
        $msg = [
            'email.require' => Catfish::lang('E-mail address is required'),
            'email.email' => Catfish::lang('The e-mail format is incorrect')
        ];
        $data = [
            'email' => Catfish::getPost('email')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function changepasswordPost()
    {
        $rule = [
            'oldPassword' => 'require',
            'newPassword' => 'require|min:8',
            'repeat' => 'require'
        ];
        $msg = [
            'oldPassword.require' => Catfish::lang('The original password must be filled in'),
            'newPassword.require' => Catfish::lang('The new password must be filled in'),
            'newPassword.min' => Catfish::lang('The new password can not be shorter than 8 characters'),
            'repeat.require' => Catfish::lang('Confirm the new password must be filled out')
        ];
        $data = [
            'oldPassword' => Catfish::getPost('oldPassword'),
            'newPassword' => Catfish::getPost('newPassword'),
            'repeat' => Catfish::getPost('repeat'),
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function userHook($pluginName, $hook, &$params = [])
    {
        $ufpluginName = ucfirst($pluginName);
        $pluginPath = ROOT_PATH.'plugins' . DS . $pluginName . DS . $ufpluginName .'.php';
        if(is_file($pluginPath)){
            return Catfish::execHook('plugin\\' . $pluginName . '\\' . $ufpluginName, $hook, $params);
        }
        return false;
    }
    protected function untoup($str)
    {
        $strArr = explode('-', $str);
        if(is_array($strArr) && count($strArr) > 0){
            $str = array_shift($strArr);
            $strArr = array_map(function($v){
                return ucfirst($v);
            }, $strArr);
            $str .= implode('', $strArr);
        }
        return $str;
    }
    private function getext($itemArr, &$pluginItem, $theme = '_theme')
    {
        foreach($itemArr as $ikey => $ival){
            $ival['alias'] = Catfish::lang($ival['alias']);
            $ival['url'] = Catfish::url('user/Index/plugin', ['name' => strtolower(preg_replace('/([A-Z])/', '-${1}', $ival['name'])), 'func' => strtolower(preg_replace('/([A-Z])/', '-${1}', $ival['function'])), 'plugin' => strtolower(preg_replace('/([A-Z])/', '-${1}', $ival['plugin'])), 'theme' => strtolower(preg_replace('/([A-Z])/', '-${1}', $theme)), 'alias' => urlencode($ival['alias'])]);
            if($ival['way'] == 'top'){
                unset($ival['way']);
                array_unshift($pluginItem,$ival);
            }
            else{
                unset($ival['way']);
                $pluginItem[] = $ival;
            }
        }
    }
    protected function themeHook($hook, &$params = [], $theme = '')
    {
        if(empty($theme)){
            $theme = $this->template;
        }
        $uftheme = ucfirst($theme);
        if(is_file(ROOT_PATH.'public' . DS . 'theme' . DS . $theme . DS . $uftheme .'.php')){
            return Catfish::execHook('theme\\' . $theme . '\\' . $uftheme, $hook, $params);
        }
        return false;
    }
}
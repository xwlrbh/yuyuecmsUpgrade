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
class Index extends CatfishCMS
{
    public function index()
    {
        $this->checkUser();
        $catfishItem = Catfish::db('users')->where('id',Catfish::getSession('user_id'))->field('id,nicheng')->find();
        Catfish::allot('nicheng', $catfishItem['nicheng']);
        return $this->show(Catfish::lang('Welcome'), 'welcome');
    }
    public function editprofile()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->editprofilePost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $shengri = Catfish::getPost('shengri');
                if(empty($shengri)){
                    $shengri = '0000-00-00';
                }
                Catfish::db('users')
                    ->where('id', Catfish::getSession('user_id'))
                    ->update([
                        'nicheng' => Catfish::getPost('nicheng'),
                        'email' => $data['email'],
                        'url' => Catfish::getPost('url'),
                        'xingbie' => Catfish::getPost('xingbie'),
                        'shengri' => $shengri,
                        'qianming' => Catfish::getPost('qianming'),
                        'shouji' => Catfish::getPost('shouji')
                    ]);
            }
        }
        $catfishItem = Catfish::db('users')->where('id',Catfish::getSession('user_id'))->field('id,nicheng,email,url,xingbie,shengri,qianming,shouji')->find();
        if(empty($catfishItem['shengri']) || $catfishItem['shengri'] == '0000-00-00'){
            $catfishItem['shengri'] = '';
        }
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Edit my profile'), 'editprofile', true);
    }
    public function editavatar()
    {
        $this->checkUser();
        $catfishItem = Catfish::db('users')->where('id',Catfish::getSession('user_id'))->field('id,touxiang')->find();
        if(!empty($catfishItem['touxiang'])){
            $catfishItem['touxiang'] = Catfish::domain().$catfishItem['touxiang'];
        }
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Edit avatar'), 'editavatar');
    }
    public function changepassword()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->changepasswordPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                if($data['newPassword'] != $data['repeat']){
                    Catfish::error(Catfish::lang('Confirm that the new password and the new password do not match'));
                    return false;
                }
                $catfishItem = Catfish::db('users')->where('id',Catfish::getSession('user_id'))->field('password,randomcode')->find();
                if($catfishItem['password'] != md5($data['oldPassword'].$catfishItem['randomcode'])){
                    Catfish::error(Catfish::lang('The original password is wrong'));
                    return false;
                }
                Catfish::db('users')
                    ->where('id', Catfish::getSession('user_id'))
                    ->update([
                        'password' => md5($data['newPassword'].$catfishItem['randomcode'])
                    ]);
            }
        }
        return $this->show(Catfish::lang('Change password'), 'changepassword', true);
    }
    public function uploadavatar()
    {
        if(Catfish::isPost()){
            $file = request()->file('file');
            $validate = [
                'ext' => 'jpg,png,gif,jpeg'
            ];
            $file->validate($validate);
            $info = $file->move(ROOT_PATH . 'data' . DS . 'uploads');
            if($info){
                $catfishItem = Catfish::db('users')->where('id',Catfish::getSession('user_id'))->field('id,touxiang')->find();
                if(Catfish::isDataPath($catfishItem['touxiang'])){
                    @unlink(ROOT_PATH . $catfishItem['touxiang']);
                }
                $repath = 'data/uploads/'.str_replace('\\','/',$info->getSaveName());
                Catfish::db('users')
                    ->where('id', Catfish::getSession('user_id'))
                    ->update([
                        'touxiang' => $repath
                    ]);
                echo $repath;
            }else{
                echo $file->getError();
            }
        }
        exit();
    }
    public function mycollection()
    {
        $this->checkUser();
        $catfish = Catfish::db('user_favorites')->field('id,title,url,description,createtime')->where('uid',Catfish::getSession('user_id'))->paginate(10);
        $catfishcms = $catfish->items();
        foreach($catfishcms as $key => $val)
        {
            $tmparr = explode('/find/',$val['url']);
            $tmp = explode('/',$tmparr[0]);
            $tbl = end($tmp);
            $catfishtmp = Catfish::db($tbl)->where('id',$tmparr[1])->field('alias')->find();
            if(!empty($catfishtmp['alias'])){
                $tmparr[1] = $catfishtmp['alias'];
            }
            $href = Catfish::url($tmparr[0],['find'=>$tmparr[1]]);
            $catfishcms[$key]['url'] = $href;
        }
        Catfish::allot('catfishcms', $catfishcms);
        Catfish::allot('pages', $catfish->render());
        return $this->show(Catfish::lang('My collection'), 'mycollection');
    }
    public function removeshoucang()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            Catfish::db('user_favorites')
                ->where('id',$id)
                ->delete();
            echo 'ok';
            exit();
        }
    }
}
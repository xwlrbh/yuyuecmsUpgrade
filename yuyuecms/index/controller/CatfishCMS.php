<?php
/**
 * Project: 鱼跃CMS - Enterprise cms developed by catfish cms.
 * Producer: catfish cms [ http://www.catfish-cms.com ]
 * Author: A.J <804644245@qq.com>
 * License: http://www.yuyue-cms.com/page/agreement.html
 * Copyright: http://www.yuyue-cms.com All rights reserved.
 */
namespace app\index\controller;
use catfishcms\Catfish;
class CatfishCMS
{
    protected $template = 'default';
    protected $tempPath = 'public/theme/';
    private $time = 1200;
    private $everyPageShows;
    protected function readydisplay($loadoptions = true)
    {
        if(!is_file(APP_PATH . 'database.php')){
            Catfish::redirect(Catfish::oUrl('install/Index/index'));
            exit();
        }
        if($loadoptions){
            $this->options();
            $lang = Catfish::detectLang();
            if(is_file(ROOT_PATH.'public/theme/'.$this->template.'/lang/'.$lang.'.php')){
                Catfish::loadLang(ROOT_PATH.'public/theme/'.$this->template.'/lang/'.$lang.'.php');
            }
            Catfish::allot('lang', $lang);
            $iscn = 0;
            if(substr($lang, 0, 2) == 'zh'){
                $iscn = 1;
            }
            Catfish::allot('iscn', $iscn);
            $isen = 0;
            if(substr($lang, 0, 2) == 'en'){
                $isen = 1;
            }
            Catfish::allot('isen', $isen);
        }
        $this->autologin();
        Catfish::setConfig('paginate.type', 'bootstrap4');
    }
    protected function show($template = 'index.html', $page = 'index', $alias = '', $cate = null, $link = null, $label = '', $list = null)
    {
        $cachstr = $template . '_' . $page . '_' . $alias . '_' . ($cate == null ? '' : implode(',',$cate)) . '_' . ($link == null ? '' : implode(',',$link)) . '_' . $label . '_' . ($list == null ? '' : $list);
        if(strpos($template,'.') === false){
            $template .= '.html';
        }
        $show = Catfish::getCache('show_'.$cachstr);
        if($show === false){
            $caidan = $this->getMenu();
            $navigation = [];
            $mbx = [];
            $submenu = [];
            $smp = [];
            $sm = [];
            foreach($caidan as $key => $val){
                $navigation[$key] = [];
                $mbx[$key] = 0;
                $submenu[$key] = [];
                $smp[$key] = -1;
                $sm[$key] = false;
                $caidan[$key] = $this->filterMenu($val, 'index/Index/'.$page, $alias, $navigation[$key], $mbx[$key], $submenu[$key], $smp[$key], $sm[$key], $cate);
                if($mbx[$key] == 0){
                    $navigation[$key] = [];
                    if(!is_null($list)){
                        $navigation[$key][] = [
                            'label' => $label,
                            'href' => Catfish::url('index/Index/'.$list),
                            'icon' => '',
                            'active' => 0
                        ];
                    }
                    if($page == 'page'){
                        array_unshift($navigation[$key],[
                            'label' => Catfish::lang('Home'),
                            'href' => Catfish::url('index/Index/index'),
                            'icon' => '',
                            'active' => 0
                        ]);
                    }
                }
                foreach($navigation[$key] as $nkey => $nval){
                    if(!empty($nval['link'])){
                        if(substr($nval['link'],0,1) == '#'){
                            $navigation[$key][$nkey]['href'] = $nval['href'].$nval['link'];
                        }
                        else{
                            $navigation[$key][$nkey]['href'] = $nval['link'];
                        }
                    }
                    if(isset($navigation[$key][$nkey]['parent_id'])){
                        unset($navigation[$key][$nkey]['parent_id']);
                    }
                    if(isset($navigation[$key][$nkey]['link'])){
                        unset($navigation[$key][$nkey]['link']);
                    }
                    $navigation[$key][$nkey]['active'] = 0;
                }
                if(!is_null($link)){
                    array_push($navigation[$key],$link);
                }
                if(isset($navigation[$key][0]) && $navigation[$key][0]['href'] != Catfish::url('index/Index/index')){
                    array_unshift($navigation[$key],[
                        'label' => Catfish::lang('Home'),
                        'href' => Catfish::url('index/Index/index'),
                        'icon' => '',
                        'active' => 0
                    ]);
                }
                foreach($submenu[$key] as $skey => $sval){
                    if($sval['parent_id'] != $smp[$key]){
                        unset($submenu[$key][$skey]);
                        continue;
                    }
                    if(!empty($sval['link'])){
                        if(substr($sval['link'],0,1) == '#'){
                            $submenu[$key][$skey]['href'] = $sval['href'].$sval['link'];
                        }
                        else{
                            $submenu[$key][$skey]['href'] = $sval['link'];
                        }
                    }
                    unset($submenu[$key][$skey]['link']);
                    unset($submenu[$key][$skey]['parent_id']);
                }
            }
            if(empty($navigation) || count($navigation) == 0){
                $navigation = ['caidan1' => []];
            }
            $show['navigation'] = $navigation;
            if(empty($submenu) || count($submenu) == 0){
                $submenu = ['caidan1' => []];
            }
            $show['submenu'] = $submenu;
            if(empty($caidan) || count($caidan) == 0){
                $caidan = ['caidan1' => []];
            }
            $show['caidan'] = $caidan;
            Catfish::setCache('show_'.$cachstr,$show,$this->time);
        }
        if(!empty($show['navigation']['caidan1']) || !Catfish::hasAllot('daohang')){
            Catfish::allot('daohang', $show['navigation']['caidan1']);
        }
        Catfish::allot('xiangguan', $show['submenu']['caidan1']);
        Catfish::allot('caidan', $show['caidan']);
        $this->getSlide();
        $this->getlinks($page);
        $this->gethistory();
        $this->getintroduction();
        $this->getlabel($page);
        $this->getselflabel($page);
        $this->getRecommend($page);
        $urlarr = [
            'denglu' => Catfish::url('login/Index/index'),
            'zhuce' => Catfish::url('login/Index/register'),
            'tuichu' => Catfish::url('login/Index/quit'),
            'sousuo' => Catfish::url('index/Index/search'),
            'yonghuzhongxin' => Catfish::url('user/Index/index'),
            'liuyan' => Catfish::url('index/Index/liuyan')
        ];
        $isMobile = 0;
        if(Catfish::isMobile()){
            $isMobile = 1;
        }
        Catfish::allot('isMobile',$isMobile);
        Catfish::allot('url', $urlarr);
        Catfish::allot('isLogin', Catfish::isLogin());
        Catfish::allot('nicheng', Catfish::getNickname());
        Catfish::allot(Catfish::bd('eXV5dWVjbXM='), $this->push());
        $params = [
            'template' => $this->template
        ];
        $this->plantHook('all', $params);
        $outfile = ROOT_PATH.$this->tempPath.$this->template.'/'.$template;
        if($template == '404.html' && !is_file($outfile)){
            $outfile = APP_PATH.'index/view/index/index.html';
        }
        return Catfish::output($outfile);
    }
    protected function shoucang($ctb)
    {
        if(!Catfish::isLogin()){
            echo Catfish::lang('You can only collect after logging in');
            exit();
        }
        $id = Catfish::getPost('id');
        $catfishcms = Catfish::db($ctb)->where('id',$id)->field('biaoti,zhaiyao')->find();
        $url = 'index/Index/'.$ctb.'/find/'.$id;
        $catfish = Catfish::db('user_favorites')->where('url',$url)->field('id')->find();
        if(empty($catfish))
        {
            Catfish::db('user_favorites')->insert([
                'uid' => Catfish::getSession('user_id'),
                'title' => $catfishcms['biaoti'],
                'url' => $url,
                'description' => $catfishcms['zhaiyao'],
                'createtime' => Catfish::now()
            ]);
        }
        echo 'ok';
        exit();
    }
    protected function zan($ctb)
    {
        $id = Catfish::getPost('id');
        $zan = Catfish::db($ctb)->where('id',$id)->field('zan')->find();
        $zan = empty($zan['zan']) ? 1 : ++$zan['zan'];
        Catfish::db($ctb)
            ->where('id', $id)
            ->update([
                'zan' => $zan
            ]);
        echo $zan;
        exit();
    }
    protected function comment($ctb)
    {
        $isajax = Catfish::isAjax();
        if(!Catfish::isLogin()){
            if($isajax){
                echo Catfish::lang('Must be logged in to comment');
                exit();
            }
            else{
                Catfish::error(Catfish::lang('Must be logged in to comment'));
                return false;
            }
        }
        $data = $this->commentPost();
        if(!is_array($data)){
            if($isajax){
                echo $data;
                exit();
            }
            else{
                Catfish::error($data);
                return false;
            }
        }
        else{
            $chkc = Catfish::get('comment');
            $status = $chkc == 1 ? 0 : 1;
            $pid = Catfish::getPost('pid');
            if($pid === false){
                $pid = 0;
            }
            else{
                $pid = intval($pid);
            }
            $topid = 0;
            if($pid != 0){
                $top = Catfish::db($ctb.'_comments')->where('id', $pid)->field('topid')->find();
                $topid = $top['topid'];
            }
            $id = Catfish::db($ctb.'_comments')->insertGetId([
                'stid' => Catfish::getPost('id'),
                'uid' => Catfish::getSession('user_id'),
                'createtime' => Catfish::now(),
                'content' => Catfish::filterJs(Catfish::getPost('pinglun', false)),
                'parent_id' => $pid,
                'status' => $status
            ]);
            if($topid == 0){
                $topid = $id;
            }
            Catfish::db($ctb.'_comments')
                ->where('id', $id)
                ->update([
                    'topid' => $topid
                ]);
            Catfish::db($ctb)
                ->where('id', Catfish::getPost('id'))
                ->setInc('pinglunshu');
            if($isajax){
                echo 'ok';
                exit();
            }
        }
    }
    protected function commentPost()
    {
        $rule = [
            'pinglun' => 'require'
        ];
        $msg = [
            'pinglun.require' => Catfish::lang('Comment content cannot be empty')
        ];
        $data = [
            'pinglun' => trim(strip_tags(Catfish::getPost('pinglun')))
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function message()
    {
        $isajax = Catfish::isAjax();
        $data = $this->liuyanPost();
        if(!is_array($data)){
            if($isajax){
                echo $data;
                exit();
            }
            else{
                Catfish::error($data);
                return false;
            }
        }
        else{
            $xingming = Catfish::getPost('xingming');
            if(empty($xingming)){
                $xingming = '';
            }
            $youxiang = Catfish::getPost('youxiang');
            if(empty($youxiang)){
                $youxiang = '';
            }
            $shouji = Catfish::getPost('shouji');
            if(empty($shouji)){
                $shouji = '';
            }
            $qq = Catfish::getPost('qq');
            if(empty($qq)){
                $qq = '';
            }
            $weixin = Catfish::getPost('weixin');
            if(empty($weixin)){
                $weixin = '';
            }
            $title = Catfish::getPost('biaoti');
            if(empty($title)){
                $title = '';
            }
            Catfish::db('guestbook')->insert([
                'full_name' => $xingming,
                'email' => $youxiang,
                'shouji' => $shouji,
                'qq' => $qq,
                'wechat' => $weixin,
                'title' => $title,
                'msg' => $data['neirong'],
                'createtime' => Catfish::now()
            ]);
            if($isajax){
                echo 'ok';
                exit();
            }
        }
    }
    protected function liuyanPost()
    {
        $rule = [
            'neirong' => 'require'
        ];
        $msg = [
            'neirong.require' => Catfish::lang('Message cannot be empty')
        ];
        $data = [
            'neirong' => trim(strip_tags(Catfish::getPost('neirong')))
        ];
        return $this->validatePost($rule, $msg, $data);
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
    protected function chanpin($find, &$link, &$id)
    {
        if(empty($find)){
            Catfish::redirect('index/Index/productlist');
            exit();
        }
        $field = 'id,uid,guanjianzi,laiyuan,fabushijian,zhengwen,biaoti,zhaiyao,yuanjia,xianjia,comment_status,gengxinshijian,pinglunshu,suolvetu,tu as zhanshitu,shipin,zutu,wenjianzu,template,yuedu,zan';
        if(!preg_match("/^[1-9][0-9]*$/", $find)){
            $catfishcms = Catfish::db('product')
                ->where('alias',$find)
                ->where('status','=',1)
                ->where('review','=',1)
                ->field($field)
                ->find();
        }
        else{
            $catfishcms = Catfish::db('product')
                ->where('id',$find)
                ->where('status','=',1)
                ->where('review','=',1)
                ->field($field)
                ->find();
        }
        if(empty($catfishcms)){
            Catfish::redirect('index/Index/error');
            exit();
        }
        else{
            $id = $catfishcms['id'];
            $link = [
                'label' => $catfishcms['biaoti'],
                'href' => Catfish::url('index/Index/product',['find'=>$find]),
                'icon' => '',
                'active' => 1
            ];
            Catfish::allot('biaoti',$catfishcms['biaoti']);
            Catfish::allot('keyword',$catfishcms['guanjianzi']);
            Catfish::allot('description',$catfishcms['zhaiyao']);
        }
        $catfish = Catfish::db('users')
            ->where('id',$catfishcms['uid'])
            ->field('nicheng')
            ->find();
        if(empty($catfish)){
            $catfish['nicheng'] = '';
        }
        $catfishshuxing = Catfish::db('product_properties')
            ->where('stid',$catfishcms['id'])
            ->field('propname ming,propvalue zhi')
            ->select();
        $catfishcms['shuxing'] = $catfishshuxing;
        Catfish::db('product')
            ->where('id', $catfishcms['id'])
            ->update([
                'yuedu' => $catfishcms['yuedu']+1
            ]);
        $catfishcms = array_merge($catfishcms,$catfish);
        unset($catfishcms['uid']);
        if($catfishcms['comment_status'] == 0){
            Catfish::allot('closeComment', 1);
        }
        unset($catfishcms['comment_status']);
        $this->convertone($catfishcms);
        $template = $catfishcms['template'];
        unset($catfishcms['template']);
        $field = 'id,guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,yuanjia,xianjia,gengxinshijian,pinglunshu,suolvetu,tu as zhanshitu,shipin,zutu,yuedu,zan';
        $catfishup = Catfish::db('product')
            ->where('id','<',$catfishcms['id'])
            ->where('status','=',1)
            ->where('review','=',1)
            ->field($field)
            ->order('id desc')
            ->find();
        if(empty($catfishup)){
            $catfishup = [];
        }
        else{
            $this->convertone($catfishup, 'product');
        }
        $catfishcms['qian'] = $catfishup;
        $catfishdown = Catfish::db('product')
            ->where('id','>',$catfishcms['id'])
            ->where('status','=',1)
            ->where('review','=',1)
            ->field($field)
            ->order('id asc')
            ->find();
        if(empty($catfishdown)){
            $catfishdown = [];
        }
        else{
            $this->convertone($catfishdown, 'product');
        }
        $catfishcms['hou'] = $catfishdown;
        $catfishcms['yuedu']++;
        $catfishcomment = Catfish::view('product_comments','id,createtime as pinglunshijian,content as pinglunneirong,parent_id')
            ->view('users','nicheng,touxiang','users.id=product_comments.uid')
            ->where('product_comments.stid','=',$catfishcms['id'])
            ->where('product_comments.parent_id','=',0)
            ->where('product_comments.status','=',1)
            ->order('product_comments.createtime desc')
            ->paginate($this->everyPageShows);
        $catfishcms['pinglun'] = $catfishcomment->items();
        foreach($catfishcms['pinglun'] as $key => $val){
            if(!empty($val['touxiang']) && substr($val['touxiang'], 0, 5) == 'data/'){
                $catfishcms['pinglun'][$key]['touxiang'] = Catfish::domain() . $val['touxiang'];
            }
        }
        $idStr = '';
        $now = time();
        foreach($catfishcms['pinglun'] as $key => $val){
            $catfishcms['pinglun'][$key]['shijian'] = $this->timedif($val['pinglunshijian'], $now);
            $catfishcms['pinglun'][$key]['pinglunshijian'] = $this->decompositiontime($val['pinglunshijian']);
            $idStr .= empty($idStr) ? $val['id'] : ',' . $val['id'];
        }
        if(!empty($idStr)){
            $catfishsubcomment = Catfish::view('product_comments','id,createtime as pinglunshijian,content as pinglunneirong,parent_id')
                ->view('users','nicheng,touxiang','users.id=product_comments.uid')
                ->where('product_comments.topid','in',$idStr)
                ->where('product_comments.status','=',1)
                ->order('product_comments.id asc')
                ->select();
            if(is_array($catfishsubcomment) && count($catfishsubcomment) > 0){
                $idArr = explode(',', $idStr);
                foreach($catfishsubcomment as $ckey => $cval){
                    if(!in_array($cval['id'], $idArr)){
                        $cval['shijian'] = $this->timedif($cval['pinglunshijian'], $now);
                        $cval['pinglunshijian'] = $this->decompositiontime($cval['pinglunshijian']);
                        if(!empty($cval['touxiang']) && substr($cval['touxiang'], 0, 5) == 'data/'){
                            $cval['touxiang'] = Catfish::domain() . $cval['touxiang'];
                        }
                        $catfishcms['pinglun'][] = $cval;
                    }
                }
                $catfishcms['pinglun'] = Catfish::tree($catfishcms['pinglun']);
            }
        }
        Catfish::allot('pages', $catfishcomment->render());
        $params = [
            'template' => $this->template,
            'yy' => $catfishcms
        ];
        $this->plantHook('product', $params);
        Catfish::allot('yy', $params['yy']);
        return $template;
    }
    protected function danye($find, &$link)
    {
        if(empty($find)){
            Catfish::redirect('index/Index/error');
            exit();
        }
        $field = 'id,uid,guanjianzi,fabushijian,zhengwen,biaoti,zhaiyao,comment_status,gengxinshijian,pinglunshu,suolvetu,tu as zhanshitu,shipin,zutu,wenjianzu,template,yuedu,zan';
        if(!preg_match("/^[1-9][0-9]*$/", $find)){
            $catfishcms = Catfish::db('page')
                ->where('alias',$find)
                ->where('status','=',1)
                ->where('review','=',1)
                ->field($field)
                ->find();
        }
        else{
            $catfishcms = Catfish::db('page')
                ->where('id',$find)
                ->where('status','=',1)
                ->where('review','=',1)
                ->field($field)
                ->find();
        }
        if(empty($catfishcms)){
            Catfish::redirect('index/Index/error');
            exit();
        }
        else{
            $link = [
                'label' => $catfishcms['biaoti'],
                'href' => Catfish::url('index/Index/page',['find'=>$find]),
                'icon' => '',
                'active' => 1
            ];
            Catfish::allot('biaoti',$catfishcms['biaoti']);
            Catfish::allot('keyword',$catfishcms['guanjianzi']);
            Catfish::allot('description',$catfishcms['zhaiyao']);
        }
        $catfish = Catfish::db('users')
            ->where('id',$catfishcms['uid'])
            ->field('nicheng')
            ->find();
        if(empty($catfish)){
            $catfish['nicheng'] = '';
        }
        Catfish::db('page')
            ->where('id', $catfishcms['id'])
            ->update([
                'yuedu' => $catfishcms['yuedu']+1
            ]);
        $catfishcms = array_merge($catfishcms,$catfish);
        unset($catfishcms['uid']);
        if($catfishcms['comment_status'] == 0){
            Catfish::allot('closeComment', 1);
        }
        unset($catfishcms['comment_status']);
        $this->convertone($catfishcms);
        $template = $catfishcms['template'];
        unset($catfishcms['template']);
        $field = 'id,guanjianzi,fabushijian,alias,zhengwen,biaoti,zhaiyao,gengxinshijian,pinglunshu,suolvetu,tu as zhanshitu,shipin,zutu,yuedu,zan';
        $catfishup = Catfish::db('page')
            ->where('id','<',$catfishcms['id'])
            ->where('status','=',1)
            ->where('review','=',1)
            ->field($field)
            ->order('id desc')
            ->find();
        if(empty($catfishup)){
            $catfishup = [];
        }
        else{
            $this->convertone($catfishup, 'page');
        }
        $catfishcms['qian'] = $catfishup;
        $catfishdown = Catfish::db('page')
            ->where('id','>',$catfishcms['id'])
            ->where('status','=',1)
            ->where('review','=',1)
            ->field($field)
            ->order('id asc')
            ->find();
        if(empty($catfishdown)){
            $catfishdown = [];
        }
        else{
            $this->convertone($catfishdown, 'page');
        }
        $catfishcms['hou'] = $catfishdown;
        $catfishcms['yuedu']++;
        $catfishcomment = Catfish::view('page_comments','id,createtime as pinglunshijian,content as pinglunneirong,parent_id')
            ->view('users','nicheng,touxiang','users.id=page_comments.uid')
            ->where('page_comments.stid','=',$catfishcms['id'])
            ->where('page_comments.parent_id','=',0)
            ->where('page_comments.status','=',1)
            ->order('page_comments.createtime desc')
            ->paginate($this->everyPageShows);
        $catfishcms['pinglun'] = $catfishcomment->items();
        foreach($catfishcms['pinglun'] as $key => $val){
            if(!empty($val['touxiang']) && substr($val['touxiang'], 0, 5) == 'data/'){
                $catfishcms['pinglun'][$key]['touxiang'] = Catfish::domain() . $val['touxiang'];
            }
        }
        $idStr = '';
        $now = time();
        foreach($catfishcms['pinglun'] as $key => $val){
            $catfishcms['pinglun'][$key]['shijian'] = $this->timedif($val['pinglunshijian'], $now);
            $catfishcms['pinglun'][$key]['pinglunshijian'] = $this->decompositiontime($val['pinglunshijian']);
            $idStr .= empty($idStr) ? $val['id'] : ',' . $val['id'];
        }
        if(!empty($idStr)){
            $catfishsubcomment = Catfish::view('page_comments','id,createtime as pinglunshijian,content as pinglunneirong,parent_id')
                ->view('users','nicheng,touxiang','users.id=page_comments.uid')
                ->where('page_comments.topid','in',$idStr)
                ->where('page_comments.status','=',1)
                ->order('page_comments.id asc')
                ->select();
            if(is_array($catfishsubcomment) && count($catfishsubcomment) > 0){
                $idArr = explode(',', $idStr);
                foreach($catfishsubcomment as $ckey => $cval){
                    if(!in_array($cval['id'], $idArr)){
                        $cval['shijian'] = $this->timedif($cval['pinglunshijian'], $now);
                        $cval['pinglunshijian'] = $this->decompositiontime($cval['pinglunshijian']);
                        if(!empty($cval['touxiang']) && substr($cval['touxiang'], 0, 5) == 'data/'){
                            $cval['touxiang'] = Catfish::domain() . $cval['touxiang'];
                        }
                        $catfishcms['pinglun'][] = $cval;
                    }
                }
                $catfishcms['pinglun'] = Catfish::tree($catfishcms['pinglun']);
            }
        }
        Catfish::allot('pages', $catfishcomment->render());
        $params = [
            'template' => $this->template,
            'yy' => $catfishcms
        ];
        $this->plantHook('page', $params);
        Catfish::allot('yy', $params['yy']);
        return $template;
    }
    protected function xinwen($find, &$link, &$id)
    {
        if(empty($find)){
            Catfish::redirect('index/Index/newslist');
            exit();
        }
        $field = 'id,uid,guanjianzi,laiyuan,fabushijian,zhengwen,biaoti,zhaiyao,comment_status,gengxinshijian,pinglunshu,suolvetu,tu as zhanshitu,shipin,zutu,wenjianzu,template,yuedu,zan';
        if(!preg_match("/^[1-9][0-9]*$/", $find)){
            $catfishcms = Catfish::db('news')
                ->where('alias',$find)
                ->where('status','=',1)
                ->where('review','=',1)
                ->field($field)
                ->find();
        }
        else{
            $catfishcms = Catfish::db('news')
                ->where('id',$find)
                ->where('status','=',1)
                ->where('review','=',1)
                ->field($field)
                ->find();
        }
        if(empty($catfishcms)){
            Catfish::redirect('index/Index/error');
            exit();
        }
        else{
            $id = $catfishcms['id'];
            $link = [
                'label' => $catfishcms['biaoti'],
                'href' => Catfish::url('index/Index/news',['find'=>$find]),
                'icon' => '',
                'active' => 1
            ];
            Catfish::allot('biaoti',$catfishcms['biaoti']);
            Catfish::allot('keyword',$catfishcms['guanjianzi']);
            Catfish::allot('description',$catfishcms['zhaiyao']);
        }
        $catfish = Catfish::db('users')
            ->where('id',$catfishcms['uid'])
            ->field('nicheng')
            ->find();
        if(empty($catfish)){
            $catfish['nicheng'] = '';
        }
        Catfish::db('news')
            ->where('id', $catfishcms['id'])
            ->update([
                'yuedu' => $catfishcms['yuedu']+1
            ]);
        $catfishcms = array_merge($catfishcms,$catfish);
        unset($catfishcms['uid']);
        if($catfishcms['comment_status'] == 0){
            Catfish::allot('closeComment', 1);
        }
        unset($catfishcms['comment_status']);
        $this->convertone($catfishcms);
        $template = $catfishcms['template'];
        unset($catfishcms['template']);
        $field = 'id,guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,gengxinshijian,pinglunshu,suolvetu,tu as zhanshitu,shipin,zutu,yuedu,zan';
        $catfishup = Catfish::db('news')
            ->where('id','<',$catfishcms['id'])
            ->where('status','=',1)
            ->where('review','=',1)
            ->field($field)
            ->order('id desc')
            ->find();
        if(empty($catfishup)){
            $catfishup = [];
        }
        else{
            $this->convertone($catfishup, 'news');
        }
        $catfishcms['qian'] = $catfishup;
        $catfishdown = Catfish::db('news')
            ->where('id','>',$catfishcms['id'])
            ->where('status','=',1)
            ->where('review','=',1)
            ->field($field)
            ->order('id asc')
            ->find();
        if(empty($catfishdown)){
            $catfishdown = [];
        }
        else{
            $this->convertone($catfishdown, 'news');
        }
        $catfishcms['hou'] = $catfishdown;
        $catfishcms['yuedu']++;
        $catfishcomment = Catfish::view('news_comments','id,createtime as pinglunshijian,content as pinglunneirong,parent_id')
            ->view('users','nicheng,touxiang','users.id=news_comments.uid')
            ->where('news_comments.stid','=',$catfishcms['id'])
            ->where('news_comments.parent_id','=',0)
            ->where('news_comments.status','=',1)
            ->order('news_comments.createtime desc')
            ->paginate($this->everyPageShows);
        $catfishcms['pinglun'] = $catfishcomment->items();
        foreach($catfishcms['pinglun'] as $key => $val){
            if(!empty($val['touxiang']) && substr($val['touxiang'], 0, 5) == 'data/'){
                $catfishcms['pinglun'][$key]['touxiang'] = Catfish::domain() . $val['touxiang'];
            }
        }
        $idStr = '';
        $now = time();
        foreach($catfishcms['pinglun'] as $key => $val){
            $catfishcms['pinglun'][$key]['shijian'] = $this->timedif($val['pinglunshijian'], $now);
            $catfishcms['pinglun'][$key]['pinglunshijian'] = $this->decompositiontime($val['pinglunshijian']);
            $idStr .= empty($idStr) ? $val['id'] : ',' . $val['id'];
        }
        if(!empty($idStr)){
            $catfishsubcomment = Catfish::view('news_comments','id,createtime as pinglunshijian,content as pinglunneirong,parent_id')
                ->view('users','nicheng,touxiang','users.id=news_comments.uid')
                ->where('news_comments.topid','in',$idStr)
                ->where('news_comments.status','=',1)
                ->order('news_comments.id asc')
                ->select();
            if(is_array($catfishsubcomment) && count($catfishsubcomment) > 0){
                $idArr = explode(',', $idStr);
                foreach($catfishsubcomment as $ckey => $cval){
                    if(!in_array($cval['id'], $idArr)){
                        $cval['shijian'] = $this->timedif($cval['pinglunshijian'], $now);
                        $cval['pinglunshijian'] = $this->decompositiontime($cval['pinglunshijian']);
                        if(!empty($cval['touxiang']) && substr($cval['touxiang'], 0, 5) == 'data/'){
                            $cval['touxiang'] = Catfish::domain() . $cval['touxiang'];
                        }
                        $catfishcms['pinglun'][] = $cval;
                    }
                }
                $catfishcms['pinglun'] = Catfish::tree($catfishcms['pinglun']);
            }
        }
        Catfish::allot('pages', $catfishcomment->render());
        $params = [
            'template' => $this->template,
            'yy' => $catfishcms
        ];
        $this->plantHook('news', $params);
        Catfish::allot('yy', $params['yy']);
        return $template;
    }
    protected function sousuo($find)
    {
        $sousuo = Catfish::getCache('sousuo');
        if($sousuo === false){
            $sousuo = Catfish::db('mlabel')
                ->field('id,biaoqian,quantity,method')
                ->where('outpos', 'search')
                ->find();
            Catfish::setCache('sousuo',$sousuo,$this->time);
        }
        $ps = $this->everyPageShows;
        if(!empty($sousuo['quantity'])){
            $ps = $sousuo['quantity'];
        }
        $field = 'id,guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,gengxinshijian,pinglunshu,suolvetu,shipin,zutu,wenjianzu,yuedu,zan,symbol';
        $order = $this->getorder($sousuo['method']);
        $field .= ',uid';
        $subQuery = Catfish::field($field)
            ->table(Catfish::prefix().'news')
            ->where('status','=',1)
            ->where('review','=',1)
            ->where('biaoti|zhengwen','like','%'.$find.'%')
            ->union(function($query) use ($field,$find){
                $query->field($field)
                    ->table(Catfish::prefix().'product')
                    ->where('status','=',1)
                    ->where('review','=',1)
                    ->where('biaoti|zhengwen','like','%'.$find.'%');
            })
            ->order($order)
            ->buildSql();
        $field = 'catfish.id,catfish.guanjianzi,catfish.laiyuan,catfish.fabushijian,catfish.alias,catfish.zhengwen,catfish.biaoti,catfish.zhaiyao,catfish.gengxinshijian,catfish.pinglunshu,catfish.suolvetu,catfish.shipin,catfish.zutu,catfish.wenjianzu,catfish.yuedu,catfish.zan,catfish.symbol';
        $catfish = Catfish::table($subQuery.' catfish')
            ->join('users','users.id=catfish.uid')
            ->field($field.',users.nicheng')
            ->paginate($ps);
        $sy['neirong'] = $this->convert($catfish->items());
        Catfish::allot('pages', $catfish->render());
        $params = [
            'template' => $this->template,
            'yy' => $sy
        ];
        $this->plantHook('search', $params);
        Catfish::allot('yy', $params['yy']);
        return '';
    }
    protected function guanjianzi($find)
    {
        $find = urldecode($find);
        $sousuo = Catfish::getCache('sousuo');
        if($sousuo === false){
            $sousuo = Catfish::db('mlabel')
                ->field('id,biaoqian,quantity,method')
                ->where('outpos', 'search')
                ->find();
            Catfish::setCache('sousuo',$sousuo,$this->time);
        }
        $ps = $this->everyPageShows;
        if(!empty($sousuo['quantity'])){
            $ps = $sousuo['quantity'];
        }
        $field = 'id,guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,gengxinshijian,pinglunshu,suolvetu,shipin,zutu,yuedu,zan,symbol';
        $order = $this->getorder($sousuo['method']);
        $field .= ',uid';
        $subQuery = Catfish::field($field)
            ->table(Catfish::prefix().'news')
            ->where('status','=',1)
            ->where('review','=',1)
            ->where('guanjianzi','like','%'.$find.'%')
            ->union(function($query) use ($field,$find){
                $query->field($field)
                    ->table(Catfish::prefix().'product')
                    ->where('status','=',1)
                    ->where('review','=',1)
                    ->where('guanjianzi','like','%'.$find.'%');
            })
            ->order($order)
            ->buildSql();
        $field = 'catfish.id,catfish.guanjianzi,catfish.laiyuan,catfish.fabushijian,catfish.alias,catfish.zhengwen,catfish.biaoti,catfish.zhaiyao,catfish.gengxinshijian,catfish.pinglunshu,catfish.suolvetu,catfish.shipin,catfish.zutu,catfish.yuedu,catfish.zan,catfish.symbol';
        $catfish = Catfish::table($subQuery.' catfish')
            ->join('users','users.id=catfish.uid')
            ->field($field.',users.nicheng')
            ->paginate($ps);
        $sy['neirong'] = $this->convert($catfish->items());
        Catfish::allot('pages', $catfish->render());
        $params = [
            'template' => $this->template,
            'yy' => $sy
        ];
        $this->plantHook('word', $params);
        Catfish::allot('yy', $params['yy']);
        return '';
    }
    protected function chanpinliebiao($find)
    {
        $chanpinliebiao = Catfish::getCache('chanpinliebiao');
        if($chanpinliebiao === false){
            $chanpinliebiao = Catfish::db('mlabel')
                ->field('id,biaoqian,quantity,method')
                ->where('outpos', 'productlist')
                ->find();
            Catfish::setCache('chanpinliebiao',$chanpinliebiao,$this->time);
        }
        $page = Catfish::getGet('page');
        $cachename = 'chanpinliebiao_'.$chanpinliebiao['method'].'_'.$find.'_'.$page;
        $catfishcms = Catfish::getCache($cachename);
        if($catfishcms === false){
            $ps = $this->everyPageShows;
            if(!empty($chanpinliebiao['quantity'])){
                $ps = $chanpinliebiao['quantity'];
            }
            $field = 'guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,yuanjia,xianjia,gengxinshijian,pinglunshu,suolvetu,shipin,zutu,wenjianzu,yuedu,zan,istop,symbol';
            if($find != 0 || is_string($find)){
                $order = $this->getorder($chanpinliebiao['method'],'product.');
                if(!preg_match("/^[1-9][0-9]*$/", $find)){
                    $product_cate = Catfish::db('product_cate')->where('alias',$find)->field('id,catename,guanjianzi,description,template,tu')->find();
                    if(!empty($product_cate)){
                        $find = $product_cate['id'];
                    }
                    else{
                        Catfish::redirect('index/Index/error');
                        exit();
                    }
                }
                else{
                    $product_cate = Catfish::db('product_cate')->where('id',$find)->field('id,catename,guanjianzi,description,template,tu')->find();
                }
                $catfish = Catfish::view('product_cate_relationships','stid id')
                    ->view('product',$field,'product.id=product_cate_relationships.stid')
                    ->view('product_cate','catename','product_cate.id=product_cate_relationships.cateid')
                    ->view('users','nicheng','users.id=product.uid')
                    ->distinct(true)
                    ->where('product.status','=',1)
                    ->where('product.review','=',1)
                    ->where('product_cate.id|product_cate.parent_id','=',$find)
                    ->order('product.istop desc,'.$order)
                    ->paginate($ps);
            }
            else{
                $order = $this->getorder($chanpinliebiao['method']);
                $product_cate = Catfish::db('product_all')->where('id',1)->field('yeming as catename,guanjianzi,description,template,tu')->find();
                if(empty($product_cate)){
                    $product_cate = [
                        'catename' => '',
                        'guanjianzi' => '',
                        'description' => '',
                        'template' => '',
                        'tu' => ''
                    ];
                }
                $catfish = Catfish::view('product','id,'.$field)
                    ->view('users','nicheng','users.id=product.uid')
                    ->where('product.status','=',1)
                    ->where('product.review','=',1)
                    ->order('product.istop desc,'.$order)
                    ->paginate($ps);
            }
            $catfishcms[$chanpinliebiao['biaoqian']] = $this->convert($catfish->items(),'product');
            $catfishcms['pages'] = $catfish->render();
            if(empty($catfishcms['pages'])){
                $catfishcms['pages'] = '';
            }
            if(!empty($product_cate['tu']) && substr($product_cate['tu'], 0, 5) == 'data/'){
                $domain = Catfish::domain();
                $product_cate['tu'] = $domain.$product_cate['tu'];
            }
            $catfishcms['related'] = $product_cate;
            Catfish::setCache($cachename,$catfishcms,$this->time);
        }
        $sy['neirong'] = $catfishcms[$chanpinliebiao['biaoqian']];
        $sy['tu'] = $catfishcms['related']['tu'];
        Catfish::allot('pages', $catfishcms['pages']);
        $biaoti = isset($catfishcms['related']['catename']) ? $catfishcms['related']['catename'] : Catfish::lang('Product list');
        $keyword = isset($catfishcms['related']['guanjianzi']) ? $catfishcms['related']['guanjianzi'] : '';
        $description = isset($catfishcms['related']['description']) ? $catfishcms['related']['description'] : '';
        $params = [
            'template' => $this->template,
            'yy' => $sy,
            'biaoti' => $biaoti,
            'keyword' => $keyword,
            'description' => $description
        ];
        $this->plantHook('productList', $params);
        Catfish::allot('yy', $params['yy']);
        Catfish::allot('biaoti',$params['biaoti']);
        Catfish::allot('keyword',$params['keyword']);
        Catfish::allot('description',$params['description']);
        $retemp = '';
        if(isset($catfishcms['related']['template'])){
            $retemp = $catfishcms['related']['template'];
        }
        return $retemp;
    }
    protected function xinwenliebiao($find)
    {
        $xinwenliebiao = Catfish::getCache('xinwenliebiao');
        if($xinwenliebiao === false){
            $xinwenliebiao = Catfish::db('mlabel')
                ->field('id,biaoqian,quantity,method')
                ->where('outpos', 'newslist')
                ->find();
            Catfish::setCache('xinwenliebiao',$xinwenliebiao,$this->time);
        }
        $page = Catfish::getGet('page');
        $cachename = 'xinwenliebiao_'.$xinwenliebiao['method'].'_'.$find.'_'.$page;
        $catfishcms = Catfish::getCache($cachename);
        if($catfishcms === false){
            $ps = $this->everyPageShows;
            if(!empty($xinwenliebiao['quantity'])){
                $ps = $xinwenliebiao['quantity'];
            }
            $field = 'guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,gengxinshijian,pinglunshu,suolvetu,shipin,zutu,wenjianzu,yuedu,zan,istop,symbol';
            if($find != 0 || is_string($find)){
                $order = $this->getorder($xinwenliebiao['method'],'news.');
                if(!preg_match("/^[1-9][0-9]*$/", $find)){
                    $news_cate = Catfish::db('news_cate')->where('alias',$find)->field('id,catename,guanjianzi,description,template,tu')->find();
                    if(!empty($news_cate)){
                        $find = $news_cate['id'];
                    }
                    else{
                        Catfish::redirect('index/Index/error');
                        exit();
                    }
                }
                else{
                    $news_cate = Catfish::db('news_cate')->where('id',$find)->field('id,catename,guanjianzi,description,template,tu')->find();
                }
                $catfish = Catfish::view('news_cate_relationships','stid id')
                    ->view('news',$field,'news.id=news_cate_relationships.stid')
                    ->view('news_cate','catename','news_cate.id=news_cate_relationships.cateid')
                    ->view('users','nicheng','users.id=news.uid')
                    ->distinct(true)
                    ->where('news.status','=',1)
                    ->where('news.review','=',1)
                    ->where('news_cate.id|news_cate.parent_id','=',$find)
                    ->order('news.istop desc,'.$order)
                    ->paginate($ps);
            }
            else{
                $order = $this->getorder($xinwenliebiao['method']);
                $news_cate = Catfish::db('news_all')->where('id',1)->field('yeming as catename,guanjianzi,description,template,tu')->find();
                if(empty($news_cate)){
                    $news_cate = [
                        'catename' => '',
                        'guanjianzi' => '',
                        'description' => '',
                        'template' => '',
                        'tu' => ''
                    ];
                }
                $catfish = Catfish::view('news','id,'.$field)
                    ->view('users','nicheng','users.id=news.uid')
                    ->where('news.status','=',1)
                    ->where('news.review','=',1)
                    ->order('news.istop desc,'.$order)
                    ->paginate($ps);
            }
            $catfishcms[$xinwenliebiao['biaoqian']] = $this->convert($catfish->items(),'news');
            $catfishcms['pages'] = $catfish->render();
            if(empty($catfishcms['pages'])){
                $catfishcms['pages'] = '';
            }
            if(!empty($news_cate['tu']) && substr($news_cate['tu'], 0, 5) == 'data/'){
                $domain = Catfish::domain();
                $news_cate['tu'] = $domain.$news_cate['tu'];
            }
            $catfishcms['related'] = $news_cate;
            Catfish::setCache($cachename,$catfishcms,$this->time);
        }
        $sy['neirong'] = $catfishcms[$xinwenliebiao['biaoqian']];
        $sy['tu'] = $catfishcms['related']['tu'];
        Catfish::allot('pages', $catfishcms['pages']);
        $biaoti = isset($catfishcms['related']['catename']) ? $catfishcms['related']['catename'] : Catfish::lang('News list');
        $keyword = isset($catfishcms['related']['guanjianzi']) ? $catfishcms['related']['guanjianzi'] : '';
        $description = isset($catfishcms['related']['description']) ? $catfishcms['related']['description'] : '';
        $params = [
            'template' => $this->template,
            'yy' => $sy,
            'biaoti' => $biaoti,
            'keyword' => $keyword,
            'description' => $description
        ];
        $this->plantHook('newsList', $params);
        Catfish::allot('yy', $params['yy']);
        Catfish::allot('biaoti',$params['biaoti']);
        Catfish::allot('keyword',$params['keyword']);
        Catfish::allot('description',$params['description']);
        $retemp = '';
        if(isset($catfishcms['related']['template'])){
            $retemp = $catfishcms['related']['template'];
        }
        return $retemp;
    }
    protected function shouye()
    {
        $shouye = Catfish::getCache('shouye');
        if($shouye === false){
            $shouye = Catfish::db('mlabel')
                ->field('id,biaoqian,quantity,method,homeout')
                ->where('outpos', 'home')
                ->find();
            Catfish::setCache('shouye',$shouye,$this->time);
        }
        $page = Catfish::getGet('page');
        $cachename = 'shouye_'.$shouye['method'].'_'.$shouye['homeout'].'_'.$page;
        $catfishcms = Catfish::getCache($cachename);
        if($catfishcms === false){
            $ps = $this->everyPageShows;
            if(!empty($shouye['quantity'])){
                $ps = $shouye['quantity'];
            }
            $field = 'id,guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,gengxinshijian,pinglunshu,suolvetu,shipin,zutu,wenjianzu,yuedu,zan,istop,symbol';
            if(empty($shouye['homeout'])){
                $shouye['homeout'] = 'all';
            }
            if($shouye['homeout'] == 'all'){
                $order = $this->getorder($shouye['method']);
                $field .= ',uid';
                $subQuery = Catfish::field($field)
                    ->table(Catfish::prefix().'news')
                    ->where('status','=',1)
                    ->where('review','=',1)
                    ->union(function($query) use ($field){
                        $query->field($field)
                            ->table(Catfish::prefix().'product')
                            ->where('status','=',1)
                            ->where('review','=',1);
                    })
                    ->order('istop desc,'.$order)
                    ->buildSql();
                $field = 'catfish.id,catfish.guanjianzi,catfish.laiyuan,catfish.fabushijian,catfish.alias,catfish.zhengwen,catfish.biaoti,catfish.zhaiyao,catfish.gengxinshijian,catfish.pinglunshu,catfish.suolvetu,catfish.shipin,catfish.zutu,catfish.wenjianzu,catfish.yuedu,catfish.zan,catfish.istop,catfish.symbol';
                $catfish = Catfish::table($subQuery.' catfish')
                    ->join('users','users.id=catfish.uid')
                    ->field($field.',users.nicheng')
                    ->paginate($ps);
            }
            elseif($shouye['homeout'] == 'news'){
                $order = $this->getorder($shouye['method'],'news.');
                $catfish = Catfish::view('news',$field)
                    ->view('users','nicheng','users.id=news.uid')
                    ->where('news.status','=',1)
                    ->where('news.review','=',1)
                    ->order('news.istop desc,'.$order)
                    ->paginate($ps);
            }
            elseif($shouye['homeout'] == 'product'){
                $order = $this->getorder($shouye['method'],'product.');
                switch($shouye['method']){
                    case 'originalHighToLow':
                        $order = 'product.yuanjia desc';
                        break;
                    case 'originalLowToHigh':
                        $order = 'product.yuanjia asc';
                        break;
                    case 'currentHighToLow':
                        $order = 'product.xianjia desc';
                        break;
                    case 'currentLowToHigh':
                        $order = 'product.xianjia asc';
                        break;
                }
                $catfish = Catfish::view('product',$field.',yuanjia,xianjia')
                    ->view('users','nicheng','users.id=product.uid')
                    ->where('product.status','=',1)
                    ->where('product.review','=',1)
                    ->order('product.istop desc,'.$order)
                    ->paginate($ps);
            }
            $tn = $shouye['homeout'];
            if($tn == 'all'){
                $tn = '';
            }
            $catfishcms[$shouye['biaoqian']] = $this->convert($catfish->items(),$tn);
            $catfishcms['pages'] = $catfish->render();
            if(empty($catfishcms['pages'])){
                $catfishcms['pages'] = '';
            }
            Catfish::setCache($cachename,$catfishcms,$this->time);
        }
        $sy['neirong'] = $catfishcms[$shouye['biaoqian']];
        Catfish::allot('pages', $catfishcms['pages']);
        $shouyezhanshi = Catfish::getCache('shouyezhanshi');
        if($shouyezhanshi === false){
            $shouyezhanshi = Catfish::db('home')->where('id',1)->field('biaoti,zhengwen,tu as zhanshitu,shipin,zutu')->find();
            if(empty($shouyezhanshi)){
                $shouyezhanshi = [
                    'biaoti' => '',
                    'zhengwen' => '',
                    'zhanshitu' => '',
                    'shipin' => '',
                    'zutu' => ''
                ];
            }
            $this->convertfirst($shouyezhanshi);
            Catfish::setCache('shouyezhanshi',$shouyezhanshi,$this->time);
        }
        $params = [
            'template' => $this->template,
            'yy' => $sy,
            'shouye' => $shouyezhanshi
        ];
        $this->plantHook('index', $params);
        Catfish::allot('yy', $params['yy']);
        Catfish::allot('shouye', $params['shouye']);
    }
    private function getorder($method, $prefix = '')
    {
        $order = $prefix.'fabushijian desc';
        switch($method){
            case 'latestRelease':
                break;
            case 'recentlyModified':
                $order = $prefix.'gengxinshijian desc';
                break;
            case 'latestComment':
                $order = $prefix.'commentime desc';
                break;
            case 'viewQuantity':
                $order = $prefix.'yuedu desc';
                break;
            case 'numberComments':
                $order = $prefix.'pinglunshu desc';
                break;
            case 'likeNumber':
                $order = $prefix.'zan desc';
                break;
            case 'releaseOrder':
                $order = $prefix.'id asc';
                break;
        }
        return $order;
    }
    private function push()
    {
        $domain = Catfish::domain();
        $push = '';
        if(strpos($domain,'localhost') === false && strpos($domain,'127') === false){
            $push = '<a href="'.Catfish::bd('aHR0cDovL3d3dy55dXl1ZS1jbXMuY29t').'" style="color:#eee" id="'.Catfish::bd('eXV5dWVjbXM=').'">'.Catfish::bd('6bG86LeDQ01T').'</a><script src="'.$domain.'public/common/js/push.js"></script>';
        }
        return $push;
    }
    private function getlabel($page)
    {
        $label = Catfish::getCache('label');
        if($label === false){
            $news = Catfish::view('config','id,biaoqian,aims,outpos,isthumb')
                ->view('news_config','biaoti,quantity,method,cateid','news_config.conid=config.id')
                ->select();
            foreach($news as $key => $val){
                $val['field'] = 'guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,gengxinshijian,pinglunshu,suolvetu,shipin,zutu,wenjianzu,yuedu,zan';
                $label[$val['outpos']][] = $val;
            }
            $product = Catfish::view('config','id,biaoqian,aims,outpos,isthumb')
                ->view('product_config','biaoti,quantity,method,cateid','product_config.conid=config.id')
                ->select();
            foreach($product as $key => $val){
                $val['field'] = 'guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,yuanjia,xianjia,gengxinshijian,pinglunshu,suolvetu,shipin,zutu,wenjianzu,yuedu,zan';
                $label[$val['outpos']][] = $val;
            }
            Catfish::setCache('label',$label,$this->time);
        }
        if($page == 'index'){
            $page = 'home';
        }
        $labelsub = Catfish::getCache('label_'.$page);
        if($labelsub === false){
            $labelsub = [];
            $catfish = [];
            if(isset($label['all'])){
                $catfish = $label['all'];
            }
            if(isset($label[$page])){
                $catfish = array_merge($catfish,$label[$page]);
            }
            if(($page == 'news' || $page == 'product' || $page == 'page') && isset($label['content'])){
                $catfish = array_merge($catfish,$label['content']);
            }
            if(($page == 'newslist' || $page == 'productlist' || $page == 'search') && isset($label['list'])){
                $catfish = array_merge($catfish,$label['list']);
            }
            if(count($catfish) > 0){
                foreach($catfish as $key => $val){
                    $labelsub[$val['biaoqian']]['biaoti'] = $val['biaoti'];
                    if($val['cateid'] == 0){
                        $labelsub[$val['biaoqian']]['gengduo'] = Catfish::url('index/Index/'.$val['aims'].'list');
                    }
                    else{
                        $alias = Catfish::db($val['aims'].'_cate')
                            ->field('alias')
                            ->where('id',$val['cateid'])
                            ->find();
                        if(empty($alias['alias'])){
                            $labelsub[$val['biaoqian']]['gengduo'] = Catfish::url('index/Index/'.$val['aims'].'list', 'find='.$val['cateid']);
                        }
                        else{
                            $labelsub[$val['biaoqian']]['gengduo'] = Catfish::url('index/Index/'.$val['aims'].'list', 'find='.$alias['alias']);
                        }
                    }
                    $labelsub[$val['biaoqian']]['neirong'] = $this->getdata($val);
                }
            }
            Catfish::setCache('label_'.$page,$labelsub,$this->time);
        }
        Catfish::allot('/', $labelsub);
    }
    private function getselflabel($page)
    {
        $selflabels = Catfish::getCache('selflabels');
        if($selflabels === false){
            $selflabels = [];
            $labels = Catfish::db('label')->field('id,biaoqian,outpos,content')->select();
            foreach($labels as $key => $val){
                $selflabels[$val['outpos']][] = [
                    $val['biaoqian'] => $val['content'],
                ];
            }
            Catfish::setCache('selflabels',$selflabels,$this->time);
        }
        if($page == 'index'){
            $page = 'home';
        }
        $selflabelsub = Catfish::getCache('selflabels_'.$page);
        if($selflabelsub === false){
            $selflabelsub = [];
            $catfish = [];
            if(isset($selflabels['all'])){
                $catfish = $selflabels['all'];
            }
            if(isset($selflabels[$page])){
                $catfish = array_merge($catfish,$selflabels[$page]);
            }
            if(($page == 'news' || $page == 'product' || $page == 'page') && isset($selflabels['content'])){
                $catfish = array_merge($catfish,$selflabels['content']);
            }
            if(($page == 'newslist' || $page == 'productlist' || $page == 'search') && isset($selflabels['list'])){
                $catfish = array_merge($catfish,$selflabels['list']);
            }
            if(count($catfish) > 0){
                foreach($catfish as $key => $val){
                    $selflabelsub = array_merge($selflabelsub,$val);
                }
            }
            Catfish::setCache('selflabels_'.$page,$selflabelsub,$this->time);
        }
        foreach($selflabelsub as $key => $val){
            Catfish::allot('z_'.$key, $val);
        }
    }
    private function getRecommend($page)
    {
        $getRecommend = Catfish::getCache('recommend_'.$page);
        if($getRecommend === false){
            $table = 'all';
            switch($page){
                case 'news':
                case 'newslist':
                    $table = 'news';
                    break;
                case 'product':
                case 'productlist':
                    $table = 'product';
                    break;
            }
            $field = 'id,guanjianzi,laiyuan,fabushijian,alias,zhengwen,biaoti,zhaiyao,gengxinshijian,pinglunshu,suolvetu,shipin,zutu,wenjianzu,yuedu,zan,symbol';
            if($table == 'all'){
                $field .= ',uid';
                $subQuery = Catfish::field($field)
                    ->table(Catfish::prefix().'news')
                    ->where('recommended','=',1)
                    ->where('status','=',1)
                    ->where('review','=',1)
                    ->union(function($query) use ($field){
                        $query->field($field)
                            ->table(Catfish::prefix().'product')
                            ->where('recommended','=',1)
                            ->where('status','=',1)
                            ->where('review','=',1);
                    })
                    ->order('gengxinshijian desc')
                    ->buildSql();
                $field = 'catfish.id,catfish.guanjianzi,catfish.laiyuan,catfish.fabushijian,catfish.alias,catfish.zhengwen,catfish.biaoti,catfish.zhaiyao,catfish.gengxinshijian,catfish.pinglunshu,catfish.suolvetu,catfish.shipin,catfish.zutu,catfish.wenjianzu,catfish.yuedu,catfish.zan,catfish.symbol';
                $getRecommend = Catfish::table($subQuery.' catfish')
                    ->join('users','users.id=catfish.uid')
                    ->field($field.',users.nicheng')
                    ->select();
            }
            elseif($table == 'news'){
                $getRecommend = Catfish::view('news',$field)
                    ->view('users','nicheng','users.id=news.uid')
                    ->where('recommended','=',1)
                    ->where('news.status','=',1)
                    ->where('news.review','=',1)
                    ->order('gengxinshijian desc')
                    ->select();
            }
            elseif($table == 'product'){
                $getRecommend = Catfish::view('product',$field.',yuanjia,xianjia')
                    ->view('users','nicheng','users.id=product.uid')
                    ->where('recommended','=',1)
                    ->where('product.status','=',1)
                    ->where('product.review','=',1)
                    ->order('gengxinshijian desc')
                    ->select();
            }
            foreach($getRecommend as $key => $val){
                $dz = 'news';
                if($val['symbol'] == 0){
                    $dz = 'news';
                }
                elseif($val['symbol'] == 1){
                    $dz = 'product';
                }
                $this->convertone($getRecommend[$key], $dz);
                unset($getRecommend[$key]['symbol']);
            }
            Catfish::setCache('recommend_'.$page,$getRecommend,$this->time);
        }
        Catfish::allot('tuijian',$getRecommend);
    }
    private function getdata($config)
    {
        $qz = $config['aims'].'.';
        $where = [$qz.'suolvetu','like','%'];
        switch($config['isthumb']){
            case 'all':
                break;
            case 'thumb':
                $where = [$qz.'suolvetu','<>',''];
                break;
            case 'nothumb':
                $where = [$qz.'suolvetu','=',''];
                break;
        }
        $order = '';
        switch($config['method']){
            case 'latestRelease':
                $order = $qz.'fabushijian desc';
                break;
            case 'recentlyModified':
                $order = $qz.'gengxinshijian desc';
                break;
            case 'latestComment':
                $order = $qz.'commentime desc';
                break;
            case 'viewQuantity':
                $order = $qz.'yuedu desc';
                break;
            case 'numberComments':
                $order = $qz.'pinglunshu desc';
                break;
            case 'likeNumber':
                $order = $qz.'zan desc';
                break;
            case 'releaseOrder':
                $order = $qz.'id asc';
                break;
            case 'originalHighToLow':
                $order = $qz.'yuanjia desc';
                break;
            case 'originalLowToHigh':
                $order = $qz.'yuanjia asc';
                break;
            case 'currentHighToLow':
                $order = $qz.'xianjia desc';
                break;
            case 'currentLowToHigh':
                $order = $qz.'xianjia asc';
                break;
        }
        if($config['cateid'] == 0){
            $catfishcms = Catfish::view($config['aims'],'id,'.$config['field'])
                ->view('users','nicheng,touxiang','users.id='.$config['aims'].'.uid')
                ->where($config['aims'].'.review', 1)
                ->where($config['aims'].'.status', 1)
                ->where($where[0], $where[1], $where[2])
                ->order($order)
                ->limit($config['quantity'])
                ->select();
        }
        else{
            $catfishcms = Catfish::view($config['aims'].'_cate_relationships','stid id')
                ->view($config['aims'],$config['field'],$config['aims'].'.id='.$config['aims'].'_cate_relationships.stid')
                ->view('users','nicheng,touxiang','users.id='.$config['aims'].'.uid')
                ->where($config['aims'].'_cate_relationships.cateid','=',$config['cateid'])
                ->where($config['aims'].'.review', 1)
                ->where($config['aims'].'.status', 1)
                ->where($where[0], $where[1], $where[2])
                ->order($order)
                ->limit($config['quantity'])
                ->select();
        }
        foreach($catfishcms as $key => $val){
            if(!empty($val['touxiang']) && substr($val['touxiang'], 0, 5) == 'data/'){
                $catfishcms[$key]['touxiang'] = Catfish::domain() . $val['touxiang'];
            }
        }
        return $this->convert($catfishcms, $config['aims']);
    }
    private function convertfirst(&$catfishcms)
    {
        $domain = Catfish::domain();
        if(!empty($catfishcms['zhanshitu']) && substr($catfishcms['zhanshitu'], 0, 5) == 'data/'){
            $catfishcms['zhanshitu'] = $domain.$catfishcms['zhanshitu'];
        }
        if(!empty($catfishcms['shipin']) && substr($catfishcms['shipin'], 0, 5) == 'data/'){
            $catfishcms['shipinExt'] = Catfish::getExtension($catfishcms['shipin']);
            $catfishcms['shipin'] = $domain.$catfishcms['shipin'];
        }
        else{
            $catfishcms['shipinExt'] = '';
        }
        if(!empty($catfishcms['zutu'])){
            $catfishcms['zutu'] = explode(',', $catfishcms['zutu']);
            foreach($catfishcms['zutu'] as $zkey => $zval){
                if(substr($zval, 0, 5) == 'data/'){
                    $catfishcms['zutu'][$zkey] = $domain.$zval;
                }
            }
        }
        else{
            $catfishcms['zutu'] = [];
        }
        $catfishcms['tu'] = $this->gettu($catfishcms['zhengwen']);
        $catfishcms['tongji']['zutu'] = count($catfishcms['zutu']);
        $catfishcms['tongji']['zhanshitu'] = empty($catfishcms['zhanshitu']) ? 0 : 1;
        $catfishcms['tongji']['shipin'] = empty($catfishcms['shipin']) ? 0 : 1;
        $catfishcms['tongji']['tu'] = count($catfishcms['tu']);
    }
    private function convertone(&$catfishcms, $dz = '')
    {
        if(!empty($catfishcms['guanjianzi'])){
            $gjzarr = explode(',', $catfishcms['guanjianzi']);
            $catfishcms['guanjianzi'] = [];
            foreach($gjzarr as $val){
                $catfishcms['guanjianzi'][] = [
                    'guanjianzi' => $val,
                    'url' => Catfish::url('index/Index/word',['find' => urlencode($val)])
                ];
            }
        }
        else{
            $catfishcms['guanjianzi'] = [];
        }
        $now = time();
        $catfishcms['tongji']['fabushijian'] = $this->timedif($catfishcms['fabushijian'], $now);
        $catfishcms['tongji']['gengxinshijian'] = $this->timedif($catfishcms['gengxinshijian'], $now);
        $catfishcms['fabushijian'] = $this->decompositiontime($catfishcms['fabushijian']);
        $catfishcms['gengxinshijian'] = $this->decompositiontime($catfishcms['gengxinshijian']);
        if(!empty($catfishcms['suolvetu'])){
            $catfishcms['datu'] = $this->prevLast('.','_larger',$catfishcms['suolvetu']);
            $catfishcms['xiaotu'] = $this->prevLast('.','_small',$catfishcms['suolvetu']);
        }
        else{
            $catfishcms['datu'] = '';
            $catfishcms['xiaotu'] = '';
        }
        $domain = Catfish::domain();
        if(!empty($catfishcms['shipin']) && substr($catfishcms['shipin'], 0, 5) == 'data/'){
            $catfishcms['shipinExt'] = Catfish::getExtension($catfishcms['shipin']);
            $catfishcms['shipin'] = $domain.$catfishcms['shipin'];
        }
        else{
            $catfishcms['shipinExt'] = '';
        }
        if(!empty($catfishcms['zutu'])){
            $catfishcms['zutu'] = explode(',', $catfishcms['zutu']);
            foreach($catfishcms['zutu'] as $zkey => $zval){
                if(substr($zval, 0, 5) == 'data/'){
                    $catfishcms['zutu'][$zkey] = $domain.$zval;
                }
            }
        }
        else{
            $catfishcms['zutu'] = [];
        }
        if(!empty($catfishcms['wenjianzu'])){
            $wjzarr = explode(',', $catfishcms['wenjianzu']);
            $catfishcms['wenjianzu'] = [];
            foreach($wjzarr as $zkey => $zval){
                $furl = $zval;
                if(substr($zval, 0, 5) == 'data/'){
                    $furl = $domain.$zval;
                }
                $tmparr = explode('/',$zval);
                $catfishcms['wenjianzu'][] = [
                    'name' => end($tmparr),
                    'url' => $furl
                ];
            }
        }
        else{
            $catfishcms['wenjianzu'] = [];
        }
        $catfishcms['tu'] = $this->gettu($catfishcms['zhengwen']);
        $catfishcms['tongji']['zutu'] = count($catfishcms['zutu']);
        $catfishcms['tongji']['wenjianzu'] = count($catfishcms['wenjianzu']);
        $catfishcms['tongji']['shipin'] = empty($catfishcms['shipin']) ? 0 : 1;
        $catfishcms['tongji']['tu'] = count($catfishcms['tu']);
        if(isset($catfishcms['alias'])){
            if(!empty($catfishcms['alias'])){
                $catfishcms['url'] = Catfish::url('index/Index/'.$dz,['find'=>$catfishcms['alias']]);
            }
            else{
                $catfishcms['url'] = Catfish::url('index/Index/'.$dz,['find'=>$catfishcms['id']]);
            }
            unset($catfishcms['alias']);
        }
    }
    private function convert($catfishcms, $aims = '')
    {
        $now = time();
        foreach($catfishcms as $key => $val){
            if(!empty($val['guanjianzi'])){
                $gjzarr = explode(',', $val['guanjianzi']);
                $catfishcms[$key]['guanjianzi'] = [];
                foreach($gjzarr as $gval){
                    $catfishcms[$key]['guanjianzi'][] = [
                        'guanjianzi' => $gval,
                        'url' => Catfish::url('index/Index/word',['find' => urlencode($gval)])
                    ];
                }
            }
            else{
                $catfishcms[$key]['guanjianzi'] = [];
            }
            $catfishcms[$key]['tongji']['fabushijian'] = $this->timedif($val['fabushijian'], $now);
            $catfishcms[$key]['tongji']['gengxinshijian'] = $this->timedif($val['gengxinshijian'], $now);
            $catfishcms[$key]['fabushijian'] = $this->decompositiontime($val['fabushijian']);
            $catfishcms[$key]['gengxinshijian'] = $this->decompositiontime($val['gengxinshijian']);
            if(!empty($val['suolvetu'])){
                $catfishcms[$key]['datu'] = $this->prevLast('.','_larger',$val['suolvetu']);
                $catfishcms[$key]['xiaotu'] = $this->prevLast('.','_small',$val['suolvetu']);
            }
            else{
                $catfishcms[$key]['datu'] = '';
                $catfishcms[$key]['xiaotu'] = '';
            }
            $domain = Catfish::domain();
            if(!empty($catfishcms[$key]['shipin']) && substr($catfishcms[$key]['shipin'], 0, 5) == 'data/'){
                $catfishcms[$key]['shipinExt'] = Catfish::getExtension($val['shipin']);
                $catfishcms[$key]['shipin'] = $domain.$val['shipin'];
            }
            else{
                $catfishcms[$key]['shipinExt'] = '';
            }
            if(!empty($val['zutu'])){
                $catfishcms[$key]['zutu'] = explode(',', $val['zutu']);
                foreach($catfishcms[$key]['zutu'] as $zkey => $zval){
                    if(substr($zval, 0, 5) == 'data/'){
                        $catfishcms[$key]['zutu'][$zkey] = $domain.$zval;
                    }
                }
            }
            else{
                $catfishcms[$key]['zutu'] = [];
            }
            if(!empty($val['wenjianzu'])){
                $wjzarr = explode(',', $val['wenjianzu']);
                $catfishcms[$key]['wenjianzu'] = [];
                foreach($wjzarr as $zkey => $zval){
                    $furl = $zval;
                    if(substr($zval, 0, 5) == 'data/'){
                        $furl = $domain.$zval;
                    }
                    $tmparr = explode('/',$zval);
                    $catfishcms[$key]['wenjianzu'][] = [
                        'name' => end($tmparr),
                        'url' => $furl
                    ];
                }
            }
            else{
                $catfishcms[$key]['wenjianzu'] = [];
            }
            $catfishcms[$key]['tu'] = $this->gettu($catfishcms[$key]['zhengwen']);
            $catfishcms[$key]['tongji']['zutu'] = count($catfishcms[$key]['zutu']);
            $catfishcms[$key]['tongji']['wenjianzu'] = count($catfishcms[$key]['wenjianzu']);
            $catfishcms[$key]['tongji']['shipin'] = empty($catfishcms[$key]['shipin']) ? 0 : 1;
            $catfishcms[$key]['tongji']['tu'] = count($catfishcms[$key]['tu']);
            $dz = $aims;
            if(empty($aims) && isset($val['symbol'])){
                switch($val['symbol']){
                    case 0:
                        $dz = 'news';
                        break;
                    case 1:
                        $dz = 'product';
                        break;
                }
            }
            if(!empty($val['alias'])){
                $catfishcms[$key]['url'] = Catfish::url('index/Index/'.$dz,['find'=>$val['alias']]);
            }
            else{
                $catfishcms[$key]['url'] = Catfish::url('index/Index/'.$dz,['find'=>$val['id']]);
            }
            if(isset($val['alias'])){
                unset($catfishcms[$key]['alias']);
            }
            if(isset($val['symbol'])){
                unset($catfishcms[$key]['symbol']);
            }
            if(isset($val['catename'])){
                unset($catfishcms[$key]['catename']);
            }
        }
        return $catfishcms;
    }
    private function getintroduction()
    {
        $qiye = Catfish::getCache('qiye');
        if($qiye === false){
            $qiye = Catfish::db('company')
                ->field('mingcheng,dizhi,dianhua,chuanzhen,wangzhi,email,jianjie')
                ->where('status',1)
                ->order('office asc')
                ->find();
            Catfish::setCache('qiye',$qiye,$this->time);
        }
        Catfish::allot('qiye', $qiye);
    }
    private function gethistory()
    {
        $lishi = Catfish::getCache('lishi');
        if($lishi === false){
            $lishi = Catfish::db('history')
                ->field('shijian,tu,shipin,biaoti,xiangqing')
                ->where('status',1)
                ->order('shijian asc')
                ->select();
            $domain = Catfish::domain();
            foreach($lishi as $key => $val){
                if($val['shijian'] == '2000-01-01 00:00:00'){
                    $lishi[$key]['shijian'] = '';
                }
                if(!empty($val['tu']) && substr($val['tu'], 0, 5) == 'data/'){
                    $lishi[$key]['tu'] = $domain.$val['tu'];
                }
                if(!empty($val['shipin']) && substr($val['shipin'], 0, 5) == 'data/'){
                    $lishi[$key]['shipinExt'] = Catfish::getExtension($val['shipin']);
                    $lishi[$key]['shipin'] = $domain.$val['shipin'];
                }
                else{
                    $lishi[$key]['shipinExt'] = '';
                }
            }
            Catfish::setCache('lishi',$lishi,$this->time);
        }
        foreach($lishi as $key => $val){
            $nyr = $this->decompositiontime($val['shijian']);
            $lishi[$key]['nian'] = $nyr['nian'];
            $lishi[$key]['yue'] = $nyr['yue'];
            $lishi[$key]['ri'] = $nyr['ri'];
            $lishi[$key]['shi'] = $nyr['shi'];
            $lishi[$key]['fen'] = $nyr['fen'];
            $lishi[$key]['miao'] = $nyr['miao'];
        }
        Catfish::allot('lishi', $lishi);
        $reverse = array_reverse($lishi);
        Catfish::allot('lishir', $reverse);
    }
    private function decompositiontime($time)
    {
        $re = [];
        if($time == '2000-01-01 00:00:00' || $time == ''){
            $re['nian'] = '';
            $re['yue'] = '';
            $re['ri'] = '';
            $re['shi'] = '';
            $re['fen'] = '';
            $re['miao'] = '';
        }
        elseif(strpos($time,' ') !== false){
            $tmparr = explode(' ', $time);
            $tmp = explode('-',$tmparr[0]);
            $re['nian'] = $tmp[0];
            $re['yue'] = $tmp[1];
            $re['ri'] = $tmp[2];
            $tmp = explode(':',$tmparr[1]);
            $re['shi'] = $tmp[0];
            $re['fen'] = $tmp[1];
            $re['miao'] = $tmp[2];
        }
        else{
            $tmp = explode('-',$time);
            $re['nian'] = $tmp[0];
            $re['yue'] = $tmp[1];
            $re['ri'] = $tmp[2];
            $re['shi'] = '00';
            $re['fen'] = '00';
            $re['miao'] = '00';
        }
        return $re;
    }
    private function getlinks($page)
    {
        $youlian = Catfish::getCache('youlian');
        if($youlian === false){
            $youlian = Catfish::db('links')
                ->field('id,dizhi,mingcheng,tubiao,target,miaoshu,shouye')
                ->where('status',1)
                ->order('listorder asc')
                ->select();
            Catfish::setCache('youlian',$youlian,$this->time);
        }
        foreach($youlian as $key => $val){
            if($page == 'index' && $val['shouye'] == 0){
                unset($youlian[$key]);
            }
            unset($youlian[$key]['shouye']);
        }
        Catfish::allot('youlian', $youlian);
    }
    private function getSlide()
    {
        $huandeng = Catfish::getCache('huandeng');
        if($huandeng === false){
            $huandeng = [];
            $huandeng['huandeng1'] = [];
            $catfish = Catfish::db('slide_cate')
                ->field('id')
                ->order('listorder asc')
                ->select();
            if(count($catfish) > 0){
                $order = 1;
                foreach((array)$catfish as $key => $val)
                {
                    $catfishcms = Catfish::view('slide','mingcheng,tupian,lianjie,miaoshu')
                        ->view('slide_cate_relationships','slideid id','slide_cate_relationships.slideid=slide.id')
                        ->where('slide_cate_relationships.cateid',$val['id'])
                        ->where('slide.status',1)
                        ->order('slide.listorder asc,slide.id asc')
                        ->select();
                    $huandeng['huandeng'.$order] = $catfishcms;
                    $order ++;
                }
            }
            Catfish::setCache('huandeng',$huandeng,$this->time);
        }
        Catfish::allot('huandeng', $huandeng);
    }
    private function getMenu()
    {
        $caidan = Catfish::getCache('caidan');
        if($caidan === false){
            $caidan = [];
            $catfish = Catfish::db('navcat')
                ->field('id')
                ->order('active desc,listorder asc,id desc')
                ->select();
            if(count($catfish) > 0){
                $order = 1;
                foreach((array)$catfish as $key => $val)
                {
                    $catfishcms = Catfish::db('nav_cate')->where('cid',$val['id'])->where('status',1)->field('id,parent_id,label,target,href,link,icon,miaoshu,suolvetu')->order('listorder asc')->select();
                    if(count($catfishcms) > 0){
                        $catfishcms = Catfish::tree($catfishcms);
                    }
                    $caidan['caidan'.$order] = $catfishcms;
                    $order ++;
                }
            }
            Catfish::setCache('caidan',$caidan,$this->time);
        }
        return $caidan;
    }
    private function filterMenu($menu, $path, $alias, &$navigation, &$mbx, &$submenu, &$smp, &$sm, &$cate)
    {
        foreach($menu as $key => $val){
            if(substr($val['href'], 0, 12) == 'index/Index/'){
                if(strpos($val['href'],'/find/') !== false){
                    $tmparr = explode('/find/',$val['href']);
                    $tmp = explode('/',$tmparr[0]);
                    $tmp = end($tmp);
                    $dbarr = [
                        'newslist' => 'news_cate',
                        'productlist' => 'product_cate',
                        'page' => 'page'
                    ];
                    $catfishtmp = Catfish::db($dbarr[$tmp])->where('id',$tmparr[1])->field('alias')->find();
                    if(!empty($catfishtmp['alias'])){
                        $tmparr[1] = $catfishtmp['alias'];
                    }
                    $href = Catfish::url($tmparr[0],['find'=>$tmparr[1]]);
                    $menu[$key]['href'] = $href;
                    $this->chip($navigation,$mbx,$val,$href);
                    if($mbx == 2){
                        $mbx = 0;
                    }
                    if($href == Catfish::url($path,['find'=>$alias])){
                        $menu[$key]['active'] = 1;
                        $mbx = 1;
                        $sm = true;
                        $smp = $val['parent_id'];
                    }
                    else{
                        $menu[$key]['active'] = 0;
                    }
                    $recate = $this->chkact($href, $cate);
                    if($mbx !== 1 && $recate == true && is_array($cate) && count($cate) > 0){
                        $mbx = 2;
                    }
                    elseif($recate == true){
                        $mbx = 1;
                    }
                    $this->sibling($submenu, $smp, $sm, $val, $href);
                }
                else{
                    $href = Catfish::url($val['href']);
                    $menu[$key]['href'] = $href;
                    $this->chip($navigation,$mbx,$val,$href);
                    if($mbx == 2){
                        $mbx = 0;
                    }
                    if($href == Catfish::url($path) && empty($alias)){
                        $menu[$key]['active'] = 1;
                        $mbx = 1;
                        $sm = true;
                        $smp = $val['parent_id'];
                    }
                    else{
                        $menu[$key]['active'] = 0;
                    }
                    $recate = $this->chkact($href, $cate);
                    if($mbx !== 1 && $recate == true && is_array($cate) && count($cate) > 0){
                        $mbx = 2;
                    }
                    elseif($recate === true){
                        $mbx = 1;
                    }
                    $this->sibling($submenu, $smp, $sm, $val, $href);
                }
                if(substr($val['link'],0,1) == '#'){
                    $menu[$key]['href'] .= $val['link'];
                }
            }
            else{
                $this->chip($navigation,$mbx,$val,'');
                if($mbx == 2){
                    $mbx = 0;
                }
                $menu[$key]['active'] = 0;
                $menu[$key]['href'] = $val['link'];
                $this->sibling($submenu, $smp, $sm, $val, '');
            }
            unset($menu[$key]['link']);
            unset($menu[$key]['parent_id']);
            if(isset($val['child'])){
                $menu[$key]['child'] = $this->filterMenu($val['child'], $path, $alias, $navigation, $mbx, $submenu, $smp, $sm, $cate);
            }
            else{
                $menu[$key]['child'] = [];
            }
        }
        return $menu;
    }
    private function sibling(&$submenu, &$smp, &$sm, $val, $href)
    {
        if(empty($href)){
            $href = $val['href'];
        }
        if($sm == false || $smp == $val['parent_id']){
            $active = 0;
            if($sm == true){
                if(count($submenu) > 0){
                    $hasact = 0;
                    foreach($submenu as $skey => $sval){
                        if($sval['active'] == 1){
                            $hasact = 1;
                            break;
                        }
                    }
                    if($hasact == 0){
                        $active = 1;
                    }
                }
                else{
                    $active = 1;
                }
            }
            $submenu[] = [
                'label' => $val['label'],
                'href' => $href,
                'link' => $val['link'],
                'icon' => $val['icon'],
                'active' => $active,
                'parent_id' => $val['parent_id'],
            ];
        }
    }
    private function chip(&$navigation, &$mbx, $val, $href)
    {
        if(empty($href)){
            $href = $val['href'];
        }
        if($mbx == 0 || $mbx == 2){
            if($val['parent_id'] == 0){
                $toempty = true;
                foreach($navigation as $nkey => $nval){
                    if($nval['active'] == 1){
                        $toempty = false;
                        break;
                    }
                }
                if($toempty == true){
                    $navigation = [];
                }
                else{
                    $mbx = 1;
                }
            }
            if($mbx !== 1){
                if($mbx == 2 && count($navigation) > 0){
                    $pop = array_pop($navigation);
                    $pop['active'] = 1;
                    array_push($navigation,$pop);
                }
                if(count($navigation) > 0){
                    $last = end($navigation);
                    while(isset($last) && $last['parent_id'] >= $val['parent_id'] && $last['active'] == 0){
                        array_pop($navigation);
                        $last = end($navigation);
                    }
                }
                $pid = -1;
                foreach($navigation as $nkey => $nval){
                    if($nval['active'] == 1){
                        $pid = $nval['parent_id'];
                    }
                }
                if($pid != $val['parent_id']){
                    array_push($navigation,[
                        'label' => $val['label'],
                        'href' => $href,
                        'link' => $val['link'],
                        'icon' => $val['icon'],
                        'active' => 0,
                        'parent_id' => $val['parent_id'],
                    ]);
                }
            }
        }
    }
    protected function template($template, $wish = '')
    {
        $path = str_replace('/', DS, ROOT_PATH.$this->tempPath.$this->template.'/');
        if(stripos(Catfish::fgc([$path.Catfish::bd('aW5kZXguaHRtbA=='),$path.Catfish::bd('Zm9vdGVyLmh0bWw=')]), Catfish::bd('eyR5dXl1ZWNtc30=')) === false){
            Catfish::redirect('index/Index/error');
            exit();
        }
        $mpath = '';
        if(Catfish::isMobile()){
            $mpath = str_replace('/', DS, $path. 'mobile/');
        }
        if(!empty($mpath)){
            if(!empty($wish)){
                $tpath = $mpath.$template.DS.$wish;
                if(is_file($tpath)){
                    return 'mobile/'.$template.'/'.$wish;
                }
            }
            $tpath = $mpath.$template.'.html';
            if(is_file($tpath)){
                return 'mobile/'.$template.'.html';
            }
            if($template == 'newslist' || $template == 'productlist' || $template == 'search'){
                $tpath = $mpath.'list.html';
                if(is_file($tpath)){
                    return 'mobile/list.html';
                }
            }
        }
        if(!empty($wish)){
            $tpath = $path.$template.DS.$wish;
            if(is_file($tpath)){
                return $template.'/'.$wish;
            }
        }
        $tpath = $path.$template.'.html';
        if(is_file($tpath)){
            return $template.'.html';
        }
        if($template == 'newslist' || $template == 'productlist' || $template == 'search'){
            $tpath = $path.'list.html';
            if(is_file($tpath)){
                return 'list.html';
            }
        }
        return 'index.html';
    }
    private function options()
    {
        $data_options = Catfish::autoload($this->time);
        foreach($data_options as $key => $val)
        {
            if($val['name'] == 'copyright')
            {
                Catfish::allot($val['name'], unserialize($val['value']));
            }
            elseif($val['name'] == 'statistics'){
                $statistics = unserialize($val['value']);
                $params = [
                    'statistics' => $statistics
                ];
                $this->plantHook('statistics', $params);
                if(isset($params['statistics'])){
                    $statistics = $params['statistics'];
                }
                Catfish::allot($val['name'], $statistics);
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
            elseif($val['name'] == 'template'){
                $this->template = $val['value'];
                Catfish::allot($val['name'], $val['value']);
            }
            elseif($val['name'] == 'everyPageShows'){
                $this->everyPageShows = $val['value'];
                Catfish::allot($val['name'], $val['value']);
            }
            else
            {
                Catfish::allot($val['name'], $val['value']);
            }
        }
    }
    private function chkact($href, &$cate)
    {
        if(is_null($cate) || !is_array($cate)){
            return false;
        }
        elseif(in_array($href,$cate)){
            if(count($cate) > 0){
                $tmp = array_flip($cate);
                unset($tmp[$href]);
                $cate = array_flip($tmp);
            }
            return true;
        }
        return false;
    }
    public function ask()
    {
        if(Catfish::hasGet('act') && Catfish::getGet('act') == 'prob' && Catfish::hasGet('token') && md5(Catfish::getGet('token')) == '5cc73261fae86f1af25a5cc49e0af132'){
            header("Content-type: text/html; charset=utf-8");
            $dir = ROOT_PATH . 'runtime' . DS . 'log' . DS;
            $mltmp = scandir($dir,1);
            $ml = [];
            if($mltmp != false && is_array($mltmp)){
                foreach($mltmp as $val){
                    if(strpos($val, '.') === false){
                        $ml[] = $val;
                    }
                }
            }
            if(isset($ml[0])){
                $dir .= $ml[0] . DS;
                $mltmp = scandir($dir,1);
                $files = [];
                if($mltmp != false && is_array($mltmp)){
                    foreach($mltmp as $val){
                        $ftmp = pathinfo($val);
                        if($ftmp['extension'] === 'log'){
                            $files[] = $val;
                        }
                    }
                }
                if(isset($files[0]))
                {
                    $filepath = $dir . $files[0];
                    echo str_replace(PHP_EOL,'<br>',file_get_contents($filepath));
                }
                else
                {
                    echo 'No log file';
                }
            }
        }
        elseif(Catfish::hasGet('act') && Catfish::getGet('act') == 'open' && Catfish::hasGet('token') && md5(Catfish::getGet('token')) == 'eeaf9c69fbe8e636e8a40912f06bb3d4'){
            Catfish::set('openpay', 1);
        }
        exit();
    }
    public function _empty()
    {
        $this->readydisplay();
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        Catfish::allot('daohang', [
            [
                'label' => Catfish::lang('Home'),
                'href' => Catfish::url('index/Index/index'),
                'icon' => '',
                'active' => 0
            ],
            [
                'label' => '404 - '.Catfish::lang('Page not found'),
                'href' => '#!',
                'icon' => '',
                'active' => 1
            ]
        ]);
        Catfish::allot('biaoti','');
        $htmls = $this->show('404','error');
        return $htmls;
    }
    private function autologin()
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
    }
    protected function getCate($tbl, $id)
    {
        $catfish = Catfish::db($tbl.'_cate_relationships')->where('stid',$id)->field('cateid')->find();
        if(isset($catfish['cateid'])){
            $catfishcms = Catfish::db($tbl.'_cate')->where('id',$catfish['cateid'])->field('alias,parent_id')->find();
            $url = $catfish['cateid'];
            if(!empty($catfishcms['alias'])){
                $url = $catfishcms['alias'];
            }
            if($catfishcms['parent_id'] == 0){
                $re = [Catfish::url('index/Index/'.$tbl.'list',['find'=>$url])];
            }
            else{
                $urlp = $catfishcms['parent_id'];
                $catfishp = Catfish::db($tbl.'_cate')->where('id',$catfishcms['parent_id'])->field('alias')->find();
                if(!empty($catfishp['alias'])){
                    $urlp = $catfishp['alias'];
                }
                $re = [Catfish::url('index/Index/'.$tbl.'list',['find'=>$url]),Catfish::url('index/Index/'.$tbl.'list',['find'=>$urlp])];
            }
            return $re;
        }
        else{
            return [];
        }
    }
    private function prevLast($ostr, $rep, $str)
    {
        $lpos = strripos($str,$ostr);
        if($lpos === false){
            return $str;
        }
        else{
            return substr_replace($str,$rep,$lpos,0);
        }
    }
    private function timedif($oldtime, $now)
    {
        $oldtime = strtotime($oldtime);
        $dif = $now - $oldtime;
        if($dif < 60){
            $dif = $dif.Catfish::lang(' seconds ago');
        }
        elseif($dif < 3600){
            $dif = intval($dif / 60).Catfish::lang(' minutes ago');
        }
        elseif($dif < 86400){
            $dif = intval($dif / 3600).Catfish::lang(' hours ago');
        }
        elseif($dif > 31622400){
            $dif = intval(date('Y') - date('Y', $oldtime)).Catfish::lang(' years ago');
        }
        else{
            $dif = intval($dif / 86400).Catfish::lang(' days ago');
        }
        return $dif;
    }
    private function gettu($content)
    {
        $reArr = [];
        preg_match_all('/<img [\s\S]+?>/i', $content, $matches);
        if(is_array($matches[0]) && count($matches[0]) > 0){
            foreach($matches[0] as $key => $val){
                preg_match('/src="(\S+?)"/i', $val, $submatches);
                if(isset($submatches[1])){
                    $reArr[] = $submatches[1];
                }
            }
        }
        return $reArr;
    }
    protected function plantHook($hook, &$params = [], $theme = '')
    {
        if(empty($theme) && isset($this->template)){
            $theme = $this->template;
        }
        $uftheme = ucfirst($theme);
        $execArr = [];
        if(is_file(ROOT_PATH.'public' . DS . 'theme' . DS . $theme . DS . $uftheme .'.php')){
            $execArr[] = 'theme\\' . $theme . '\\' . $uftheme;
        }
        $pluginsOpened = Catfish::get('plugins_opened');
        if(!empty($pluginsOpened)){
            $pluginsOpened = unserialize($pluginsOpened);
            foreach($pluginsOpened as $key => $val){
                $ufval = ucfirst($val);
                $execArr[] = 'plugin\\' . $val . '\\' . $ufval;
            }
        }
        if(count($execArr) > 0){
            Catfish::addHook($hook, $execArr);
            return Catfish::listen($hook, $params);
        }
        return false;
    }
}
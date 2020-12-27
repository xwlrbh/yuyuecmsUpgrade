<?php
/**
 * Project: 鱼跃CMS - Enterprise cms developed by catfish cms.
 * Producer: catfish cms [ http://www.catfish-cms.com ]
 * Author: A.J <804644245@qq.com>
 * License: http://www.yuyue-cms.com/page/agreement.html
 * Copyright: http://www.yuyue-cms.com All rights reserved.
 */
namespace app\admin\controller;
use catfishcms\Catfish;
class Index extends CatfishCMS
{
    public function index()
    {
        $this->checkUser();
        $catfishcms = Catfish::db('guestbook')->field('id,title,msg,createtime')->order('createtime desc')->limit(20)->select();
        Catfish::allot('catfishcms', $catfishcms);
        Catfish::allot('catfishver', Catfish::getConfig('catfishCMS.version'));
        return $this->show(Catfish::lang('Welcome page'), 'news');
    }
    public function writenews()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->writenewsPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('news')->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        echo Catfish::lang('The alias already exists, please change one');
                        exit();
                    }
                }
                $id = Catfish::db('news')->insertGetId([
                    'uid' => Catfish::getSession('user_id'),
                    'guanjianzi' => str_replace('，',',',Catfish::getPost('guanjianzi')),
                    'laiyuan' => Catfish::getPost('laiyuan'),
                    'fabushijian' => Catfish::getPost('fabushijian'),
                    'alias' => $data['alias'],
                    'zhengwen' => Catfish::getPost('zhengwen', false),
                    'biaoti' => $data['biaoti'],
                    'zhaiyao' => Catfish::getPost('zhaiyao'),
                    'comment_status' => Catfish::getPost('pinglun'),
                    'gengxinshijian' => Catfish::getPost('fabushijian'),
                    'suolvetu' => Catfish::getPost('suolvetu'),
                    'tu' => Catfish::getPost('zstu'),
                    'shipin' => Catfish::getPost('shipin'),
                    'zutu' => trim(Catfish::getPost('zutu'),','),
                    'wenjianzu' => trim(Catfish::getPost('wenjianzu'),','),
                    'template' => Catfish::getPost('template'),
                    'istop' => Catfish::getPost('zhiding'),
                    'recommended' => Catfish::getPost('tuijian')
                ]);
                $fenlei = Catfish::getPost('fenlei/a');
                if(count((array)$fenlei) > 0){
                    $data = [];
                    foreach((array)$fenlei as $key => $val)
                    {
                        $data[] = ['stid' => $id, 'cateid' => $val];
                    }
                    Catfish::db('news_cate_relationships')->insertAll($data);
                }
                echo 'ok';
                exit();
            }
        }
        Catfish::allot('fenlei', Catfish::getSort('news'));
        Catfish::allot('muban', Catfish::getTemplate('news'));
        return $this->show(Catfish::lang('Editing news'), 'news', 'writenews', true);
    }
    public function editingnews()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->writenewsPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('news')->where('id','<>',Catfish::getPost('id'))->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        echo Catfish::lang('The alias already exists, please change one');
                        exit();
                    }
                }
                $id = Catfish::getPost('id');
                $tone = Catfish::db('news')->where('id',$id)->field('uid')->find();
                if($tone['uid'] != Catfish::getSession('user_id')){
                    echo Catfish::lang('You do not have permission to operate');
                    exit();
                }
                Catfish::db('news')
                    ->where('id',$id)
                    ->update([
                        'guanjianzi' => str_replace('，',',',Catfish::getPost('guanjianzi')),
                        'laiyuan' => Catfish::getPost('laiyuan'),
                        'fabushijian' => Catfish::getPost('fabushijian'),
                        'alias' => $data['alias'],
                        'zhengwen' => Catfish::getPost('zhengwen', false),
                        'biaoti' => $data['biaoti'],
                        'zhaiyao' => Catfish::getPost('zhaiyao'),
                        'comment_status' => Catfish::getPost('pinglun'),
                        'gengxinshijian' => Catfish::getPost('fabushijian'),
                        'suolvetu' => Catfish::getPost('suolvetu'),
                        'tu' => Catfish::getPost('zstu'),
                        'shipin' => Catfish::getPost('shipin'),
                        'zutu' => trim(Catfish::getPost('zutu'),','),
                        'wenjianzu' => trim(Catfish::getPost('wenjianzu'),','),
                        'template' => Catfish::getPost('template'),
                        'istop' => Catfish::getPost('zhiding'),
                        'recommended' => Catfish::getPost('tuijian')
                    ]);
                Catfish::db('news_cate_relationships')
                    ->where('stid',$id)
                    ->delete();
                $fenlei = Catfish::getPost('fenlei/a');
                if(count((array)$fenlei) > 0){
                    $data = [];
                    foreach((array)$fenlei as $key => $val)
                    {
                        $data[] = ['stid' => $id, 'cateid' => $val];
                    }
                    Catfish::db('news_cate_relationships')->insertAll($data);
                }
                echo 'ok';
                exit();
            }
        }
        $catfishID = Catfish::getGet('catfish');
        $classify = Catfish::db('news_cate_relationships')->field('cateid')->where('stid',$catfishID)->select();
        $fenlei = Catfish::getSort('news');
        foreach((array)$fenlei as $key => $val){
            $fenlei[$key]['classify'] = 0;
            foreach($classify as $cval){
                if($val['id'] == $cval['cateid']){
                    $fenlei[$key]['classify'] = 1;
                    break;
                }
            }
        }
        Catfish::allot('fenlei', $fenlei);
        Catfish::allot('muban', Catfish::getTemplate('news'));
        $catfishItem = Catfish::db('news')->where('id',$catfishID)->find();
        $catfishItem['zhengwen'] = str_replace('&','&amp;',$catfishItem['zhengwen']);
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Editing news'), 'news', 'writenews', true);
    }
    public function recyclingnews()
    {
        if(Catfish::isPost()){
            Catfish::db('news')
                ->where('id', Catfish::getPost('id'))
                ->update([
                    'status' => 0
                ]);
            echo 'ok';
            exit();
        }
    }
    public function newsBatch()
    {
        if(Catfish::isPost()){
            $xiugai = '';
            $zhi = 0;
            switch(Catfish::getPost('cz')){
                case 'shenhe':
                    $xiugai = 'review';
                    $zhi = 1;
                    break;
                case 'weishenhe':
                    $xiugai = 'review';
                    $zhi = 0;
                    break;
                case 'zhiding':
                    $xiugai = 'istop';
                    $zhi = 1;
                    break;
                case 'weizhiding':
                    $xiugai = 'istop';
                    $zhi = 0;
                    break;
                case 'tuijian':
                    $xiugai = 'recommended';
                    $zhi = 1;
                    break;
                case 'weituijian':
                    $xiugai = 'recommended';
                    $zhi = 0;
                    break;
                case 'pshanchu':
                    $xiugai = 'status';
                    $zhi = 0;
                    break;
            }
            if(!empty($xiugai)){
                Catfish::db('news')
                    ->where('id','in',Catfish::getPost('zcuan'))
                    ->update([$xiugai => $zhi]);
            }
            echo 'ok';
            exit();
        }
    }
    public function newslist()
    {
        $this->checkUser();
        $data = Catfish::view('news','id,fabushijian,biaoti,review,pinglunshu,suolvetu,tu,shipin,zutu,wenjianzu,yuedu,istop,recommended')
            ->view('users','yonghu','users.id=news.uid')
            ->where('news.status','=',1)
            ->order('news.id desc')
            ->paginate(20);
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        Catfish::allot('fenlei', Catfish::getSort('news'));
        return $this->show(Catfish::lang('News list'), 'news', 'newslist');
    }
    public function searchnews()
    {
        $this->checkUser();
        $fenlei = Catfish::getGet('fenlei');
        if(empty($fenlei)){
            $fenlei = 0;
        }
        $start = Catfish::getGet('start');
        if(empty($start)){
            $start = '2000-01-01 01:01:01';
        }
        $end = Catfish::getGet('end');
        if(empty($end)){
            $end = Catfish::now();
        }
        $key = Catfish::getGet('key');
        if(empty($key)){
            $key = '';
        }
        if(strtotime($start) > strtotime($end))
        {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }
        if($fenlei != 0){
            $data = Catfish::view('news','id,fabushijian,biaoti,review,pinglunshu,suolvetu,tu,shipin,zutu,wenjianzu,yuedu,istop,recommended')
                ->view('news_cate_relationships','cateid','news_cate_relationships.stid=news.id')
                ->view('users','yonghu','users.id=news.uid')
                ->where('news.status','=',1)
                ->where('news_cate_relationships.cateid','=',$fenlei)
                ->whereTime('news.fabushijian', 'between', [$start, $end])
                ->where('news.biaoti|news.zhengwen','like','%'.$key.'%')
                ->order('news.id desc')
                ->paginate(20,false,[
                    'query' => [
                        'fenlei' => urlencode($fenlei),
                        'start' => urlencode($start),
                        'end' => urlencode($end),
                        'key' => urlencode($key)
                    ]
                ]);
        }
        else{
            $data = Catfish::view('news','id,fabushijian,biaoti,review,pinglunshu,suolvetu,tu,shipin,zutu,wenjianzu,yuedu,istop,recommended')
                ->view('users','yonghu','users.id=news.uid')
                ->where('news.status','=',1)
                ->whereTime('news.fabushijian', 'between', [$start, $end])
                ->where('news.biaoti|news.zhengwen','like','%'.$key.'%')
                ->order('news.id desc')
                ->paginate(20,false,[
                    'query' => [
                        'fenlei' => urlencode($fenlei),
                        'start' => urlencode($start),
                        'end' => urlencode($end),
                        'key' => urlencode($key)
                    ]
                ]);
        }
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        Catfish::allot('fenlei', Catfish::getSort('news'));
        return $this->show(Catfish::lang('News Center').' - '.Catfish::lang('Search results'), 'news', 'newslist', false, 'newslist');
    }
    public function newsaliaschk()
    {
        if(Catfish::isPost()){
            echo $this->aliaschk('news');
            exit();
        }
    }
    public function categoriesnews()
    {
        $this->checkUser();
        if(Catfish::isGet()){
            Catfish::db('news_cate')
                ->where('id',Catfish::getGet('d'))
                ->delete();
            Catfish::db('news_cate')
                ->where('parent_id', Catfish::getGet('d'))
                ->update([
                    'parent_id' => Catfish::getGet('f')
                ]);
            Catfish::db('news_cate_relationships')
                ->where('cateid',Catfish::getGet('d'))
                ->delete();
        }
        Catfish::allot('fenlei', Catfish::getSort('news','id,catename,description,template,parent_id','&#12288;'));
        return $this->show(Catfish::lang('Categories of news'), 'news', 'categoriesnews');
    }
    public function categoriesnewsall()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $cate = Catfish::db('news_all')->where('id',1)->field('id')->find();
            if(empty($cate)){
                Catfish::db('news_all')->insert([
                    'id' => 1,
                    'yeming' => Catfish::getPost('yeming'),
                    'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                    'description' => Catfish::getPost('miaoshu'),
                    'template' => Catfish::getPost('template'),
                    'tu' => Catfish::getPost('zstu')
                ]);
            }
            else{
                Catfish::db('news_all')
                    ->where('id', 1)
                    ->update([
                        'yeming' => Catfish::getPost('yeming'),
                        'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                        'description' => Catfish::getPost('miaoshu'),
                        'template' => Catfish::getPost('template'),
                        'tu' => Catfish::getPost('zstu')
                    ]);
            }
        }
        Catfish::allot('muban', Catfish::getTemplate('newslist'));
        $cateall = Catfish::db('news_all')->where('id',1)->find();
        if(empty($cateall)){
            $cateall = [
                'yeming' => '',
                'guanjianzi' => '',
                'description' => '',
                'template' => '',
                'tu' => ''
            ];
        }
        Catfish::allot('catfishItem', $cateall);
        return $this->show(Catfish::lang('All category page settings'), 'news', 'categoriesnews');
    }
    public function categoriesnewsadd()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->categoriesnewsPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('news_cate')->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        Catfish::error(Catfish::lang('The alias already exists, please change one'));
                        return false;
                    }
                }
                Catfish::db('news_cate')->insert([
                    'catename' => $data['fenleim'],
                    'alias' => $data['alias'],
                    'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                    'description' => Catfish::getPost('miaoshu'),
                    'template' => Catfish::getPost('template'),
                    'tu' => Catfish::getPost('zstu'),
                    'parent_id' => $data['shangji']
                ]);
            }
        }
        Catfish::allot('fenlei', Catfish::getSort('news'));
        Catfish::allot('muban', Catfish::getTemplate('newslist'));
        return $this->show(Catfish::lang('Add news category'), 'news', 'categoriesnews', true);
    }
    public function categoriesnewsaliaschk()
    {
        if(Catfish::isPost()){
            echo $this->aliaschk('news_cate');
            exit();
        }
    }
    public function categoriesnewssub()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->categoriesnewsPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('news_cate')->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        Catfish::error(Catfish::lang('The alias already exists, please change one'));
                        return false;
                    }
                }
                Catfish::db('news_cate')->insert([
                    'catename' => $data['fenleim'],
                    'alias' => $data['alias'],
                    'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                    'description' => Catfish::getPost('miaoshu'),
                    'template' => Catfish::getPost('template'),
                    'tu' => Catfish::getPost('zstu'),
                    'parent_id' => $data['shangji']
                ]);
            }
        }
        Catfish::allot('fenlei', Catfish::getSort('news'));
        Catfish::allot('fufenlei', Catfish::getGet('c'));
        Catfish::allot('muban', Catfish::getTemplate('newslist'));
        return $this->show(Catfish::lang('Add subcategories'), 'news', 'categoriesnews', true);
    }
    public function categoriesnewsedit()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->categoriesnewsPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('news_cate')->where('id','<>',Catfish::getPost('id'))->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        Catfish::error(Catfish::lang('The alias already exists, please change one'));
                        return false;
                    }
                }
                Catfish::db('news_cate')
                    ->where('id', Catfish::getPost('id'))
                    ->update([
                        'catename' => $data['fenleim'],
                        'alias' => $data['alias'],
                        'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                        'description' => Catfish::getPost('miaoshu'),
                        'template' => Catfish::getPost('template'),
                        'tu' => Catfish::getPost('zstu'),
                        'parent_id' => $data['shangji']
                    ]);
            }
        }
        Catfish::allot('fenlei', Catfish::getSortNoSelf('news',Catfish::getGet('c')));
        Catfish::allot('muban', Catfish::getTemplate('newslist'));
        $cate = Catfish::db('news_cate')->where('id',Catfish::getGet('c'))->find();
        Catfish::allot('catfishItem', $cate);
        return $this->show(Catfish::lang('Edit category'), 'news', 'categoriesnews', true);
    }
    public function newslabelconf()
    {
        $this->checkUser();
        $data = Catfish::view('config','biaoqian,outpos,isthumb,remarks')
            ->view('news_config','id,biaoti,quantity,method','news_config.conid=config.id')
            ->view('news_cate','catename','news_cate.id=news_config.cateid','LEFT')
            ->where('config.aims','news')
            ->order('config.id desc')
            ->paginate(20);
        $catfishcms = $data->items();
        $catfish = [
            'latestRelease' => Catfish::lang('The latest release time'),
            'recentlyModified' => Catfish::lang('Last modified time'),
            'latestComment' => Catfish::lang('Latest comment'),
            'viewQuantity' => Catfish::lang('Total number of view'),
            'numberComments' => Catfish::lang('Total number of comments'),
            'likeNumber' => Catfish::lang('Total number of points praise'),
            'releaseOrder' => Catfish::lang('By writing articles in order'),
        ];
        $catfisheff = [
            'all' => Catfish::lang('Full website effective'),
            'home' => Catfish::lang('Only the first page is valid'),
            'list' => Catfish::lang('All list pages are valid'),
            'newslist' => Catfish::lang('News list page is valid'),
            'productlist' => Catfish::lang('Product list page is valid'),
            'search' => Catfish::lang('Search results page is valid'),
            'content' => Catfish::lang('All content pages are valid'),
            'news' => Catfish::lang('Only news content page is valid'),
            'product' => Catfish::lang('Only product content pages are valid'),
            'page' => Catfish::lang('Only a single page is valid')
        ];
        $catfishthumb = [
            'all' => Catfish::lang('Mixed output'),
            'thumb' => Catfish::lang('Output only content with thumbnails'),
            'nothumb' => Catfish::lang('Output only content without thumbnails'),
        ];
        foreach((array)$catfishcms as $key => $val){
            $catfishcms[$key]['method'] = $catfish[$val['method']];
            $catfishcms[$key]['outpos'] = $catfisheff[$val['outpos']];
            $catfishcms[$key]['isthumb'] = $catfishthumb[$val['isthumb']];
            if(is_null($val['catename'])){
                $catfishcms[$key]['catename'] = '';
            }
        }
        Catfish::allot('catfishcms', $catfishcms);
        Catfish::allot('pages', $data->render());
        return $this->show(Catfish::lang('News Center').' - '.Catfish::lang('Template label'), 'news', 'newslabelconf');
    }
    public function newslabelconfchk()
    {
        if(Catfish::isPost()){
            $biaoqian = strtolower(trim(Catfish::getPost('biaoqian')));
            if(in_array($biaoqian,Catfish::label())){
                echo Catfish::lang('The label name already exists, please change one');
                exit();
            }
            $id = Catfish::getPost('id');
            if(empty($id)){
                $catfishcms = Catfish::db('config')->where('biaoqian',$biaoqian)->find();
            }
            else{
                $catfishno = Catfish::db('news_config')->where('id',$id)->field('conid')->find();
                $catfishcms = Catfish::db('config')->where('biaoqian',$biaoqian)->where('id','<>',$catfishno['conid'])->find();
            }
            if(!empty($catfishcms)){
                echo Catfish::lang('The label name already exists, please change one');
                exit();
            }
            else{
                echo 'ok';
                exit();
            }
        }
    }
    public function newslabelconfadd()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->newslabelconfPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $catfish = Catfish::db('config')->where('biaoqian',$data['biaoqian'])->find();
                if(!empty($catfish)){
                    Catfish::error(Catfish::lang('The label name already exists, please change one'));
                    return false;
                }
                $catfishID = Catfish::db('config')->insertGetId([
                    'biaoqian' => $data['biaoqian'],
                    'aims' => 'news',
                    'outpos' => Catfish::getPost('outpos'),
                    'isthumb' => Catfish::getPost('isthumb'),
                    'remarks' => Catfish::getPost('remarks')
                ]);
                $catfishquantity = intval(Catfish::getPost('quantity'));
                if($catfishquantity < 1){
                    $catfishquantity = 1;
                }
                Catfish::db('news_config')->insert([
                    'conid' => $catfishID,
                    'biaoti' => Catfish::getPost('biaoti'),
                    'quantity' => $catfishquantity,
                    'method' => Catfish::getPost('method'),
                    'cateid' => Catfish::getPost('cateid')
                ]);
            }
        }
        Catfish::allot('fenlei', Catfish::getSort('news'));
        return $this->show(Catfish::lang('News Center').' - '.Catfish::lang('Add template label'), 'news', 'newslabelconf');
    }
    public function newslabelconfedit()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->newslabelconfPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $id = Catfish::getPost('id');
                $catfishno = Catfish::db('news_config')->where('id',$id)->field('conid')->find();
                $catfish = Catfish::db('config')->where('biaoqian',$data['biaoqian'])->where('id','<>',$catfishno['conid'])->find();
                if(!empty($catfish)){
                    Catfish::error(Catfish::lang('The label name already exists, please change one'));
                    return false;
                }
                Catfish::db('config')
                    ->where('id', $catfishno['conid'])
                    ->update([
                        'biaoqian' => $data['biaoqian'],
                        'aims' => 'news',
                        'outpos' => Catfish::getPost('outpos'),
                        'isthumb' => Catfish::getPost('isthumb'),
                        'remarks' => Catfish::getPost('remarks')
                    ]);
                $catfishquantity = intval(Catfish::getPost('quantity'));
                if($catfishquantity < 1){
                    $catfishquantity = 1;
                }
                Catfish::db('news_config')
                    ->where('id', $id)
                    ->update([
                        'biaoti' => Catfish::getPost('biaoti'),
                        'quantity' => $catfishquantity,
                        'method' => Catfish::getPost('method'),
                        'cateid' => Catfish::getPost('cateid')
                    ]);
            }
        }
        $catfishItem = Catfish::db('news_config')->where('id',Catfish::getGet('c'))->find();
        $catfish = Catfish::db('config')->where('id',$catfishItem['conid'])->find();
        $catfishItem['biaoqian'] = $catfish['biaoqian'];
        $catfishItem['outpos'] = $catfish['outpos'];
        $catfishItem['isthumb'] = $catfish['isthumb'];
        $catfishItem['remarks'] = $catfish['remarks'];
        Catfish::allot('catfishItem', $catfishItem);
        Catfish::allot('fenlei', Catfish::getSort('news'));
        return $this->show(Catfish::lang('News Center').' - '.Catfish::lang('Modify template label'), 'news', 'newslabelconf');
    }
    public function newslabelconfdel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $re = Catfish::db('news_config')->where('id',$id)->field('conid')->find();
            Catfish::db('news_config')
                ->where('id', $id)
                ->delete();
            Catfish::db('config')
                ->where('id', $re['conid'])
                ->delete();
            echo 'ok';
            exit();
        }
    }
    public function newscomments()
    {
        $this->checkUser();
        $catfish = Catfish::view('news_comments','id,stid,createtime,content,status')
            ->view('users','yonghu,email,touxiang','users.id=news_comments.uid')
            ->order('news_comments.createtime desc')
            ->paginate(20);
        Catfish::allot('pages', $catfish->render());
        $catfishcms = $catfish->items();
        foreach($catfishcms as $key => $val){
            if(!empty($val['touxiang']) && substr($val['touxiang'], 0, 5) == 'data/'){
                $catfishcms[$key]['touxiang'] = Catfish::domain() . $val['touxiang'];
            }
        }
        Catfish::allot('catfishcms', $catfishcms);
        return $this->show(Catfish::lang('All comments'), 'news', 'newscomments');
    }
    public function newsshenhepinglun()
    {
        if(Catfish::isPost()){
            $zt = Catfish::getPost('zt');
            if($zt == 1)
            {
                $zt = 0;
            }
            else
            {
                $zt = 1;
            }
            Catfish::db('news_comments')
                ->where('id', Catfish::getPost('id'))
                ->update(['status' => $zt]);
            echo 'ok';
            exit();
        }
    }
    public function newscommentdel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $catfish = Catfish::db('news_comments')->where('id',$id)->field('stid')->find();
            Catfish::db('news_comments')
                ->where('id', $id)
                ->delete();
            Catfish::db('news')
                ->where('id', $catfish['stid'])
                ->setDec('pinglunshu');
            echo 'ok';
            exit();
        }
    }
    public function newscommentbatch()
    {
        if(Catfish::isPost()){
            $zhi = 0;
            switch(Catfish::getPost('cz')){
                case 'shenhe':
                    $zhi = 1;
                    break;
                case 'weishenhe':
                    $zhi = 0;
                    break;
            }
            Catfish::db('news_comments')
                ->where('id','in',Catfish::getPost('zcuan'))
                ->update(['status' => $zhi]);
            echo 'ok';
            exit();
        }
    }
    public function newsrecycle()
    {
        $this->checkUser();
        $data = Catfish::view('news','id,fabushijian,biaoti,review,pinglunshu,suolvetu,yuedu,istop,recommended')
            ->view('users','yonghu','users.id=news.uid')
            ->where('news.status','=',0)
            ->order('news.id desc')
            ->paginate(20);
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        return $this->show(Catfish::lang('News Center').' - '.Catfish::lang('Recycle bin'), 'news', 'newsrecycle');
    }
    public function restorenews()
    {
        if(Catfish::isPost()){
            Catfish::db('news')
                ->where('id', Catfish::getPost('id'))
                ->update([
                    'status' => 1
                ]);
            echo 'ok';
            exit();
        }
    }
    public function deletenews()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $re = Catfish::db('news')->where('id',$id)->field('suolvetu,tu,shipin,zutu,wenjianzu')->find();
            Catfish::db('news')
                ->where('id', $id)
                ->delete();
            Catfish::db('news_cate_relationships')
                ->where('stid',$id)
                ->delete();
            Catfish::db('news_comments')
                ->where('stid',$id)
                ->delete();
            $this->deleteResource($re['suolvetu'], $re['shipin'], $re['zutu'], $re['wenjianzu'], $re['tu']);
            echo 'ok';
            exit();
        }
    }
    public function recycleNewsBatch()
    {
        if(Catfish::isPost()){
            switch(Catfish::getPost('cz')){
                case 'phuanyuan':
                    Catfish::db('news')
                        ->where('id','in', Catfish::getPost('zcuan'))
                        ->update([
                            'status' => 1
                        ]);
                    break;
                case 'pshanchu':
                    $id = Catfish::getPost('zcuan');
                    $re = Catfish::db('news')->field('suolvetu,tu,shipin,zutu,wenjianzu')->where('id','in', $id)->select();
                    Catfish::db('news')
                        ->where('id','in', $id)
                        ->delete();
                    Catfish::db('news_cate_relationships')
                        ->where('stid','in',$id)
                        ->delete();
                    Catfish::db('news_comments')
                        ->where('stid','in',$id)
                        ->delete();
                    foreach((array)$re as $val){
                        $this->deleteResource($val['suolvetu'], $val['shipin'], $val['zutu'], $val['wenjianzu'], $val['tu']);
                    }
                    break;
            }
        }
    }
    public function editproduct()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->editproductPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('product')->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        echo Catfish::lang('The alias already exists, please change one');
                        exit();
                    }
                }
                $id = Catfish::db('product')->insertGetId([
                    'uid' => Catfish::getSession('user_id'),
                    'guanjianzi' => str_replace('，',',',Catfish::getPost('guanjianzi')),
                    'fabushijian' => Catfish::getPost('fabushijian'),
                    'alias' => $data['alias'],
                    'zhengwen' => Catfish::getPost('zhengwen', false),
                    'biaoti' => $data['biaoti'],
                    'zhaiyao' => Catfish::getPost('zhaiyao'),
                    'yuanjia' => $data['yuanjia'],
                    'xianjia' => $data['xianjia'],
                    'comment_status' => Catfish::getPost('pinglun'),
                    'gengxinshijian' => Catfish::getPost('fabushijian'),
                    'suolvetu' => Catfish::getPost('suolvetu'),
                    'tu' => Catfish::getPost('zstu'),
                    'shipin' => Catfish::getPost('shipin'),
                    'zutu' => trim(Catfish::getPost('zutu'),','),
                    'wenjianzu' => trim(Catfish::getPost('wenjianzu'),','),
                    'template' => Catfish::getPost('template'),
                    'istop' => Catfish::getPost('zhiding'),
                    'recommended' => Catfish::getPost('tuijian'),
                    'pid' => Catfish::getPost('properties')
                ]);
                $fenlei = Catfish::getPost('fenlei/a');
                if(count((array)$fenlei) > 0){
                    $data = [];
                    foreach((array)$fenlei as $key => $val)
                    {
                        $data[] = ['stid' => $id, 'cateid' => $val];
                    }
                    Catfish::db('product_cate_relationships')->insertAll($data);
                }
                $protemp = trim(Catfish::getPost('protemp',false), ';');
                if(mb_strlen($protemp) > 0){
                    $protemparr = explode(';;', $protemp);
                    $data = [];
                    foreach((array)$protemparr as $key => $val)
                    {
                        $pv = trim($val, ';');
                        $pvarr = explode(',,', $pv);
                        $data[] = ['stid' => $id, 'propname' => str_replace(['#,','#;'], [',',';'], $pvarr[0]), 'propvalue' => str_replace(['#,','#;'], [',',';'], $pvarr[1])];
                    }
                    Catfish::db('product_properties')->insertAll($data);
                }
                echo 'ok';
                exit();
            }
        }
        $catfishprop = Catfish::db('properties')->field('id,protemp')->select();
        Catfish::allot('catfishprop', $catfishprop);
        Catfish::allot('fenlei', Catfish::getSort('product'));
        Catfish::allot('muban', Catfish::getTemplate('product'));
        return $this->show(Catfish::lang('Edit product'), 'product', 'editproduct', true);
    }
    public function editingproduct()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->editproductPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('product')->where('id','<>',Catfish::getPost('id'))->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        echo Catfish::lang('The alias already exists, please change one');
                        exit();
                    }
                }
                $id = Catfish::getPost('id');
                $tone = Catfish::db('product')->where('id',$id)->field('uid')->find();
                if($tone['uid'] != Catfish::getSession('user_id')){
                    echo Catfish::lang('You do not have permission to operate');
                    exit();
                }
                Catfish::db('product')
                    ->where('id',$id)
                    ->update([
                        'guanjianzi' => str_replace('，',',',Catfish::getPost('guanjianzi')),
                        'fabushijian' => Catfish::getPost('fabushijian'),
                        'alias' => $data['alias'],
                        'zhengwen' => Catfish::getPost('zhengwen', false),
                        'biaoti' => $data['biaoti'],
                        'zhaiyao' => Catfish::getPost('zhaiyao'),
                        'yuanjia' => $data['yuanjia'],
                        'xianjia' => $data['xianjia'],
                        'comment_status' => Catfish::getPost('pinglun'),
                        'gengxinshijian' => Catfish::getPost('fabushijian'),
                        'suolvetu' => Catfish::getPost('suolvetu'),
                        'tu' => Catfish::getPost('zstu'),
                        'shipin' => Catfish::getPost('shipin'),
                        'zutu' => trim(Catfish::getPost('zutu'),','),
                        'wenjianzu' => trim(Catfish::getPost('wenjianzu'),','),
                        'template' => Catfish::getPost('template'),
                        'istop' => Catfish::getPost('zhiding'),
                        'recommended' => Catfish::getPost('tuijian'),
                        'pid' => Catfish::getPost('properties')
                    ]);
                Catfish::db('product_cate_relationships')
                    ->where('stid',$id)
                    ->delete();
                $fenlei = Catfish::getPost('fenlei/a');
                if(count((array)$fenlei) > 0){
                    $data = [];
                    foreach((array)$fenlei as $key => $val)
                    {
                        $data[] = ['stid' => $id, 'cateid' => $val];
                    }
                    Catfish::db('product_cate_relationships')->insertAll($data);
                }
                Catfish::db('product_properties')
                    ->where('stid',$id)
                    ->delete();
                $protemp = trim(Catfish::getPost('protemp',false), ';');
                if(mb_strlen($protemp) > 0){
                    $protemparr = explode(';;', $protemp);
                    $data = [];
                    foreach((array)$protemparr as $key => $val)
                    {
                        $pv = trim($val, ';');
                        $pvarr = explode(',,', $pv);
                        $data[] = ['stid' => $id, 'propname' => str_replace(['#,','#;'], [',',';'], $pvarr[0]), 'propvalue' => str_replace(['#,','#;'], [',',';'], $pvarr[1])];
                    }
                    Catfish::db('product_properties')->insertAll($data);
                }
                echo 'ok';
                exit();
            }
        }
        $catfishID = Catfish::getGet('catfish');
        $classify = Catfish::db('product_cate_relationships')->field('cateid')->where('stid',$catfishID)->select();
        $fenlei = Catfish::getSort('product');
        foreach((array)$fenlei as $key => $val){
            $fenlei[$key]['classify'] = 0;
            foreach($classify as $cval){
                if($val['id'] == $cval['cateid']){
                    $fenlei[$key]['classify'] = 1;
                    break;
                }
            }
        }
        Catfish::allot('fenlei', $fenlei);
        $catfishprop = Catfish::db('properties')->field('id,protemp')->select();
        Catfish::allot('catfishprop', $catfishprop);
        Catfish::allot('muban', Catfish::getTemplate('product'));
        $catfishItem = Catfish::db('product')->where('id',$catfishID)->find();
        $catfishItem['zhengwen'] = str_replace('&','&amp;',$catfishItem['zhengwen']);
        $propname = Catfish::db('product_properties')->field('propname,propvalue')->where('stid',$catfishID)->select();
        $catfishItem['protemp'] = Catfish::json($propname);
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Edit product'), 'product', 'editproduct', true);
    }
    public function productlist()
    {
        $this->checkUser();
        $data = Catfish::view('product','id,fabushijian,biaoti,review,pinglunshu,suolvetu,tu,shipin,zutu,wenjianzu,yuedu,istop,recommended')
            ->view('users','yonghu','users.id=product.uid')
            ->where('product.status','=',1)
            ->order('product.id desc')
            ->paginate(20);
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        Catfish::allot('fenlei', Catfish::getSort('product'));
        return $this->show(Catfish::lang('Product List'), 'product', 'productlist');
    }
    public function recyclingproduct()
    {
        if(Catfish::isPost()){
            Catfish::db('product')
                ->where('id', Catfish::getPost('id'))
                ->update([
                    'status' => 0
                ]);
            echo 'ok';
            exit();
        }
    }
    public function productBatch()
    {
        if(Catfish::isPost()){
            $xiugai = '';
            $zhi = 0;
            switch(Catfish::getPost('cz')){
                case 'shenhe':
                    $xiugai = 'review';
                    $zhi = 1;
                    break;
                case 'weishenhe':
                    $xiugai = 'review';
                    $zhi = 0;
                    break;
                case 'zhiding':
                    $xiugai = 'istop';
                    $zhi = 1;
                    break;
                case 'weizhiding':
                    $xiugai = 'istop';
                    $zhi = 0;
                    break;
                case 'tuijian':
                    $xiugai = 'recommended';
                    $zhi = 1;
                    break;
                case 'weituijian':
                    $xiugai = 'recommended';
                    $zhi = 0;
                    break;
                case 'pshanchu':
                    $xiugai = 'status';
                    $zhi = 0;
                    break;
            }
            if(!empty($xiugai)){
                Catfish::db('product')
                    ->where('id','in',Catfish::getPost('zcuan'))
                    ->update([$xiugai => $zhi]);
            }
            echo 'ok';
            exit();
        }
    }
    public function searchproduct()
    {
        $this->checkUser();
        $fenlei = Catfish::getGet('fenlei');
        if(empty($fenlei)){
            $fenlei = 0;
        }
        $start = Catfish::getGet('start');
        if(empty($start)){
            $start = '2000-01-01 01:01:01';
        }
        $end = Catfish::getGet('end');
        if(empty($end)){
            $end = Catfish::now();
        }
        $key = Catfish::getGet('key');
        if(empty($key)){
            $key = '';
        }
        if(strtotime($start) > strtotime($end))
        {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }
        if($fenlei != 0){
            $data = Catfish::view('product','id,fabushijian,biaoti,review,pinglunshu,suolvetu,tu,shipin,zutu,wenjianzu,yuedu,istop,recommended')
                ->view('product_cate_relationships','cateid','product_cate_relationships.stid=product.id')
                ->view('users','yonghu','users.id=product.uid')
                ->where('product.status','=',1)
                ->where('product_cate_relationships.cateid','=',$fenlei)
                ->whereTime('product.fabushijian', 'between', [$start, $end])
                ->where('product.biaoti|product.zhengwen','like','%'.$key.'%')
                ->order('product.id desc')
                ->paginate(20,false,[
                    'query' => [
                        'fenlei' => urlencode($fenlei),
                        'start' => urlencode($start),
                        'end' => urlencode($end),
                        'key' => urlencode($key)
                    ]
                ]);
        }
        else{
            $data = Catfish::view('product','id,fabushijian,biaoti,review,pinglunshu,suolvetu,tu,shipin,zutu,wenjianzu,yuedu,istop,recommended')
                ->view('users','yonghu','users.id=product.uid')
                ->where('product.status','=',1)
                ->whereTime('product.fabushijian', 'between', [$start, $end])
                ->where('product.biaoti|product.zhengwen','like','%'.$key.'%')
                ->order('product.id desc')
                ->paginate(20,false,[
                    'query' => [
                        'fenlei' => urlencode($fenlei),
                        'start' => urlencode($start),
                        'end' => urlencode($end),
                        'key' => urlencode($key)
                    ]
                ]);
        }
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        Catfish::allot('fenlei', Catfish::getSort('product'));
        return $this->show(Catfish::lang('Product center').' - '.Catfish::lang('Search results'), 'product', 'productlist', false, 'productlist');
    }
    public function productaliaschk()
    {
        if(Catfish::isPost()){
            echo $this->aliaschk('product');
            exit();
        }
    }
    public function productproperties()
    {
        if(Catfish::isPost()){
            $propname = Catfish::db('properties_relationships')->field('propname')->where('propid',Catfish::getPost('protemp'))->select();
            return $propname;
        }
        else{
            return [];
        }
    }
    public function productcategories()
    {
        $this->checkUser();
        if(Catfish::isGet()){
            Catfish::db('product_cate')
                ->where('id',Catfish::getGet('d'))
                ->delete();
            Catfish::db('product_cate')
                ->where('parent_id', Catfish::getGet('d'))
                ->update([
                    'parent_id' => Catfish::getGet('f')
                ]);
            Catfish::db('product_cate_relationships')
                ->where('cateid',Catfish::getGet('d'))
                ->delete();
        }
        Catfish::allot('fenlei', Catfish::getSort('product','id,catename,description,template,parent_id','&#12288;'));
        return $this->show(Catfish::lang('Product Categories'), 'product', 'productcategories');
    }
    public function productcategoriesall()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $cate = Catfish::db('product_all')->where('id',1)->field('id')->find();
            if(empty($cate)){
                Catfish::db('product_all')->insert([
                    'id' => 1,
                    'yeming' => Catfish::getPost('yeming'),
                    'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                    'description' => Catfish::getPost('miaoshu'),
                    'template' => Catfish::getPost('template'),
                    'tu' => Catfish::getPost('zstu')
                ]);
            }
            else{
                Catfish::db('product_all')
                    ->where('id', 1)
                    ->update([
                        'yeming' => Catfish::getPost('yeming'),
                        'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                        'description' => Catfish::getPost('miaoshu'),
                        'template' => Catfish::getPost('template'),
                        'tu' => Catfish::getPost('zstu')
                    ]);
            }
        }
        Catfish::allot('muban', Catfish::getTemplate('productlist'));
        $cateall = Catfish::db('product_all')->where('id',1)->find();
        if(empty($cateall)){
            $cateall = [
                'yeming' => '',
                'guanjianzi' => '',
                'description' => '',
                'template' => '',
                'tu' => ''
            ];
        }
        Catfish::allot('catfishItem', $cateall);
        return $this->show(Catfish::lang('All category page settings'), 'product', 'productcategories');
    }
    public function productcategoriesadd()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->categoriesnewsPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('product_cate')->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        Catfish::error(Catfish::lang('The alias already exists, please change one'));
                        return false;
                    }
                }
                Catfish::db('product_cate')->insert([
                    'catename' => $data['fenleim'],
                    'alias' => $data['alias'],
                    'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                    'description' => Catfish::getPost('miaoshu'),
                    'template' => Catfish::getPost('template'),
                    'tu' => Catfish::getPost('zstu'),
                    'parent_id' => $data['shangji']
                ]);
            }
        }
        Catfish::allot('fenlei', Catfish::getSort('product'));
        Catfish::allot('muban', Catfish::getTemplate('productlist'));
        return $this->show(Catfish::lang('Add product category'), 'product', 'productcategories', true);
    }
    public function productcategoriesaliaschk()
    {
        if(Catfish::isPost()){
            echo $this->aliaschk('product_cate');
            exit();
        }
    }
    public function productcategoriessub()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->categoriesnewsPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('product_cate')->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        Catfish::error(Catfish::lang('The alias already exists, please change one'));
                        return false;
                    }
                }
                Catfish::db('product_cate')->insert([
                    'catename' => $data['fenleim'],
                    'alias' => $data['alias'],
                    'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                    'description' => Catfish::getPost('miaoshu'),
                    'template' => Catfish::getPost('template'),
                    'tu' => Catfish::getPost('zstu'),
                    'parent_id' => $data['shangji']
                ]);
            }
        }
        Catfish::allot('fenlei', Catfish::getSort('product'));
        Catfish::allot('fufenlei', Catfish::getGet('c'));
        Catfish::allot('muban', Catfish::getTemplate('productlist'));
        return $this->show(Catfish::lang('Add subcategories'), 'product', 'productcategories', true);
    }
    public function productcategoriesedit()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->categoriesnewsPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('product_cate')->where('id','<>',Catfish::getPost('id'))->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        Catfish::error(Catfish::lang('The alias already exists, please change one'));
                        return false;
                    }
                }
                Catfish::db('product_cate')
                    ->where('id', Catfish::getPost('id'))
                    ->update([
                        'catename' => $data['fenleim'],
                        'alias' => $data['alias'],
                        'guanjianzi' => str_replace('，', ',', Catfish::getPost('guanjianzi')),
                        'description' => Catfish::getPost('miaoshu'),
                        'template' => Catfish::getPost('template'),
                        'tu' => Catfish::getPost('zstu'),
                        'parent_id' => $data['shangji']
                    ]);
            }
        }
        Catfish::allot('fenlei', Catfish::getSortNoSelf('product',Catfish::getGet('c')));
        Catfish::allot('muban', Catfish::getTemplate('productlist'));
        $cate = Catfish::db('product_cate')->where('id',Catfish::getGet('c'))->find();
        Catfish::allot('catfishItem', $cate);
        return $this->show(Catfish::lang('Edit category'), 'product', 'productcategories', true);
    }
    public function attributetemplate()
    {
        $this->checkUser();
        $catfish = Catfish::db('properties')->paginate(10);
        $catfishcms = $catfish->items();
        foreach($catfishcms as $key => $val){
            $propname = Catfish::db('properties_relationships')->field('propname')->where('propid',$val['id'])->select();
            $tmp = '';
            foreach((array)$propname as $pkey => $pval){
                $tmp .= ', '.$pval['propname'];
            }
            $catfishcms[$key]['propname'] = trim($tmp,', ');
        }
        Catfish::allot('catfishcms', $catfishcms);
        Catfish::allot('pages', $catfish->render());
        return $this->show(Catfish::lang('Attribute template'), 'product', 'attributetemplate');
    }
    public function attributetemplateadd()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->attributetemplatePost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $protemp = Catfish::db('properties')->where('protemp',$data['protemp'])->find();
                if(!empty($protemp)){
                    Catfish::error(Catfish::lang('Attribute template name already exists, please change one'));
                    return false;
                }
                else{
                    $id = Catfish::db('properties')->insertGetId([
                        'protemp' => $data['protemp'],
                        'description' => Catfish::getPost('description')
                    ]);
                    $propname = trim(Catfish::getPost('propname'),',');
                    $proparr = explode(',',$propname);
                    $data = [];
                    foreach((array)$proparr as $key => $val)
                    {
                        $data[] = ['propid' => $id, 'propname' => $val];
                    }
                    Catfish::db('properties_relationships')->insertAll($data);
                }
            }
        }
        return $this->show(Catfish::lang('Add a attribute template'), 'product', 'attributetemplate');
    }
    public function attributetemplateedit()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->attributetemplatePost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $protemp = Catfish::db('properties')->where('id','<>',Catfish::getPost('id'))->where('protemp',$data['protemp'])->find();
                if(!empty($protemp)){
                    Catfish::error(Catfish::lang('Attribute template name already exists, please change one'));
                    return false;
                }
                else{
                    $id = Catfish::getPost('id');
                    Catfish::db('properties')
                        ->where('id', $id)
                        ->update([
                            'protemp' => $data['protemp'],
                            'description' => Catfish::getPost('description')
                        ]);
                    Catfish::db('properties_relationships')
                        ->where('propid',$id)
                        ->delete();
                    $propname = trim(Catfish::getPost('propname'),',');
                    $proparr = explode(',',$propname);
                    $data = [];
                    foreach((array)$proparr as $key => $val)
                    {
                        $data[] = ['propid' => $id, 'propname' => $val];
                    }
                    Catfish::db('properties_relationships')->insertAll($data);
                }
            }
        }
        $protemp = Catfish::db('properties')->where('id',Catfish::getGet('c'))->find();
        $propname = Catfish::db('properties_relationships')->field('propname')->where('propid',$protemp['id'])->select();
        $tmp = '';
        foreach((array)$propname as $pkey => $pval){
            $tmp .= ','.$pval['propname'];
        }
        $protemp['propname'] = $tmp;
        Catfish::allot('catfishItem', $protemp);
        return $this->show(Catfish::lang('Edit attribute template'), 'product', 'attributetemplate');
    }
    public function attributetemplatechk()
    {
        if(Catfish::isPost()){
            $protemp = Catfish::db('properties')->where('protemp',Catfish::getPost('protemp'))->find();
            if(!empty($protemp)){
                echo Catfish::lang('Attribute template name already exists, please change one');
                exit();
            }
            else{
                echo 'ok';
                exit();
            }
        }
    }
    public function attributetemplatedel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            Catfish::db('properties')
                ->where('id', $id)
                ->delete();
            Catfish::db('properties_relationships')
                ->where('propid', $id)
                ->delete();
            echo 'ok';
            exit();
        }
    }
    public function productlabelconf()
    {
        $this->checkUser();
        $data = Catfish::view('config','biaoqian,outpos,isthumb,remarks')
            ->view('product_config','id,biaoti,quantity,method','product_config.conid=config.id')
            ->view('product_cate','catename','product_cate.id=product_config.cateid','LEFT')
            ->where('config.aims','product')
            ->order('config.id desc')
            ->paginate(20);
        $catfishcms = $data->items();
        $catfish = [
            'latestRelease' => Catfish::lang('The latest release time'),
            'recentlyModified' => Catfish::lang('Last modified time'),
            'latestComment' => Catfish::lang('Latest comment'),
            'viewQuantity' => Catfish::lang('Total number of view'),
            'numberComments' => Catfish::lang('Total number of comments'),
            'likeNumber' => Catfish::lang('Total number of points praise'),
            'releaseOrder' => Catfish::lang('According to the order of product release'),
            'originalHighToLow' => Catfish::lang('From high to low according to the original price'),
            'originalLowToHigh' => Catfish::lang('From low to high according to the original price'),
            'currentHighToLow' => Catfish::lang('From high to low at current prices'),
            'currentLowToHigh' => Catfish::lang('From low to high at current prices'),
        ];
        $catfisheff = [
            'all' => Catfish::lang('Full website effective'),
            'home' => Catfish::lang('Only the first page is valid'),
            'list' => Catfish::lang('All list pages are valid'),
            'newslist' => Catfish::lang('News list page is valid'),
            'productlist' => Catfish::lang('Product list page is valid'),
            'search' => Catfish::lang('Search results page is valid'),
            'content' => Catfish::lang('All content pages are valid'),
            'news' => Catfish::lang('Only news content page is valid'),
            'product' => Catfish::lang('Only product content pages are valid'),
            'page' => Catfish::lang('Only a single page is valid')
        ];
        $catfishthumb = [
            'all' => Catfish::lang('Mixed output'),
            'thumb' => Catfish::lang('Output only content with thumbnails'),
            'nothumb' => Catfish::lang('Output only content without thumbnails'),
        ];
        foreach((array)$catfishcms as $key => $val){
            $catfishcms[$key]['method'] = $catfish[$val['method']];
            $catfishcms[$key]['outpos'] = $catfisheff[$val['outpos']];
            $catfishcms[$key]['isthumb'] = $catfishthumb[$val['isthumb']];
            if(is_null($val['catename'])){
                $catfishcms[$key]['catename'] = '';
            }
        }
        Catfish::allot('catfishcms', $catfishcms);
        Catfish::allot('pages', $data->render());
        return $this->show(Catfish::lang('Product center').' - '.Catfish::lang('Template label'), 'product', 'productlabelconf');
    }
    public function productlabelconfadd()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->newslabelconfPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $catfish = Catfish::db('config')->where('biaoqian',$data['biaoqian'])->find();
                if(!empty($catfish)){
                    Catfish::error(Catfish::lang('The label name already exists, please change one'));
                    return false;
                }
                $catfishID = Catfish::db('config')->insertGetId([
                    'biaoqian' => $data['biaoqian'],
                    'aims' => 'product',
                    'outpos' => Catfish::getPost('outpos'),
                    'isthumb' => Catfish::getPost('isthumb'),
                    'remarks' => Catfish::getPost('remarks')
                ]);
                $catfishquantity = intval(Catfish::getPost('quantity'));
                if($catfishquantity < 1){
                    $catfishquantity = 1;
                }
                Catfish::db('product_config')->insert([
                    'conid' => $catfishID,
                    'biaoti' => Catfish::getPost('biaoti'),
                    'quantity' => $catfishquantity,
                    'method' => Catfish::getPost('method'),
                    'cateid' => Catfish::getPost('cateid')
                ]);
            }
        }
        Catfish::allot('fenlei', Catfish::getSort('product'));
        return $this->show(Catfish::lang('Product center').' - '.Catfish::lang('Add template label'), 'product', 'productlabelconf');
    }
    public function productlabelconfedit()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->newslabelconfPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $id = Catfish::getPost('id');
                $catfishno = Catfish::db('product_config')->where('id',$id)->field('conid')->find();
                $catfish = Catfish::db('config')->where('biaoqian',$data['biaoqian'])->where('id','<>',$catfishno['conid'])->find();
                if(!empty($catfish)){
                    Catfish::error(Catfish::lang('The label name already exists, please change one'));
                    return false;
                }
                Catfish::db('config')
                    ->where('id', $catfishno['conid'])
                    ->update([
                        'biaoqian' => $data['biaoqian'],
                        'aims' => 'product',
                        'outpos' => Catfish::getPost('outpos'),
                        'isthumb' => Catfish::getPost('isthumb'),
                        'remarks' => Catfish::getPost('remarks')
                    ]);
                $catfishquantity = intval(Catfish::getPost('quantity'));
                if($catfishquantity < 1){
                    $catfishquantity = 1;
                }
                Catfish::db('product_config')
                    ->where('id', $id)
                    ->update([
                        'biaoti' => Catfish::getPost('biaoti'),
                        'quantity' => $catfishquantity,
                        'method' => Catfish::getPost('method'),
                        'cateid' => Catfish::getPost('cateid')
                    ]);
            }
        }
        $catfishItem = Catfish::db('product_config')->where('id',Catfish::getGet('c'))->find();
        $catfish = Catfish::db('config')->where('id',$catfishItem['conid'])->find();
        $catfishItem['biaoqian'] = $catfish['biaoqian'];
        $catfishItem['outpos'] = $catfish['outpos'];
        $catfishItem['isthumb'] = $catfish['isthumb'];
        $catfishItem['remarks'] = $catfish['remarks'];
        Catfish::allot('catfishItem', $catfishItem);
        Catfish::allot('fenlei', Catfish::getSort('product'));
        return $this->show(Catfish::lang('Product center').' - '.Catfish::lang('Modify template label'), 'product', 'productlabelconf');
    }
    public function productlabelconfchk()
    {
        if(Catfish::isPost()){
            $biaoqian = strtolower(trim(Catfish::getPost('biaoqian')));
            if(in_array($biaoqian,Catfish::label())){
                echo Catfish::lang('The label name already exists, please change one');
                exit();
            }
            $id = Catfish::getPost('id');
            if(empty($id)){
                $catfishcms = Catfish::db('config')->where('biaoqian',$biaoqian)->find();
            }
            else{
                $catfishno = Catfish::db('product_config')->where('id',$id)->field('conid')->find();
                $catfishcms = Catfish::db('config')->where('biaoqian',$biaoqian)->where('id','<>',$catfishno['conid'])->find();
            }
            if(!empty($catfishcms)){
                echo Catfish::lang('The label name already exists, please change one');
                exit();
            }
            else{
                echo 'ok';
                exit();
            }
        }
    }
    public function productlabelconfdel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $re = Catfish::db('product_config')->where('id',$id)->field('conid')->find();
            Catfish::db('product_config')
                ->where('id', $id)
                ->delete();
            Catfish::db('config')
                ->where('id', $re['conid'])
                ->delete();
            echo 'ok';
            exit();
        }
    }
    public function productcomments()
    {
        $this->checkUser();
        $catfish = Catfish::view('product_comments','id,stid,createtime,content,status')
            ->view('users','yonghu,email,touxiang','users.id=product_comments.uid')
            ->order('product_comments.createtime desc')
            ->paginate(20);
        Catfish::allot('pages', $catfish->render());
        $catfishcms = $catfish->items();
        foreach($catfishcms as $key => $val){
            if(!empty($val['touxiang']) && substr($val['touxiang'], 0, 5) == 'data/'){
                $catfishcms[$key]['touxiang'] = Catfish::domain() . $val['touxiang'];
            }
        }
        Catfish::allot('catfishcms', $catfishcms);
        return $this->show(Catfish::lang('All comments'), 'product', 'productcomments');
    }
    public function productshenhepinglun()
    {
        if(Catfish::isPost()){
            $zt = Catfish::getPost('zt');
            if($zt == 1)
            {
                $zt = 0;
            }
            else
            {
                $zt = 1;
            }
            Catfish::db('product_comments')
                ->where('id', Catfish::getPost('id'))
                ->update(['status' => $zt]);
            echo 'ok';
            exit();
        }
    }
    public function productcommentdel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $catfish = Catfish::db('product_comments')->where('id',$id)->field('stid')->find();
            Catfish::db('product_comments')
                ->where('id', $id)
                ->delete();
            Catfish::db('product')
                ->where('id', $catfish['stid'])
                ->setDec('pinglunshu');
            echo 'ok';
            exit();
        }
    }
    public function productcommentbatch()
    {
        if(Catfish::isPost()){
            $zhi = 0;
            switch(Catfish::getPost('cz')){
                case 'shenhe':
                    $zhi = 1;
                    break;
                case 'weishenhe':
                    $zhi = 0;
                    break;
            }
            Catfish::db('product_comments')
                ->where('id','in',Catfish::getPost('zcuan'))
                ->update(['status' => $zhi]);
            echo 'ok';
            exit();
        }
    }
    public function productrecycle()
    {
        $this->checkUser();
        $data = Catfish::view('product','id,fabushijian,biaoti,review,pinglunshu,suolvetu,yuedu,istop,recommended')
            ->view('users','yonghu','users.id=product.uid')
            ->where('product.status','=',0)
            ->order('product.id desc')
            ->paginate(20);
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        return $this->show(Catfish::lang('Product center').' - '.Catfish::lang('Recycle bin'), 'product', 'productrecycle');
    }
    public function deleteproduct()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $re = Catfish::db('product')->where('id',$id)->field('suolvetu,tu,shipin,zutu,wenjianzu')->find();
            Catfish::db('product')
                ->where('id', $id)
                ->delete();
            Catfish::db('product_cate_relationships')
                ->where('stid',$id)
                ->delete();
            Catfish::db('product_comments')
                ->where('stid',$id)
                ->delete();
            $this->deleteResource($re['suolvetu'], $re['shipin'], $re['zutu'], $re['wenjianzu'], $re['tu']);
            echo 'ok';
            exit();
        }
    }
    public function restoreproduct()
    {
        if(Catfish::isPost()){
            Catfish::db('product')
                ->where('id', Catfish::getPost('id'))
                ->update([
                    'status' => 1
                ]);
            echo 'ok';
            exit();
        }
    }
    public function recycleProductBatch()
    {
        if(Catfish::isPost()){
            switch(Catfish::getPost('cz')){
                case 'phuanyuan':
                    Catfish::db('product')
                        ->where('id','in', Catfish::getPost('zcuan'))
                        ->update([
                            'status' => 1
                        ]);
                    break;
                case 'pshanchu':
                    $id = Catfish::getPost('zcuan');
                    $re = Catfish::db('product')->field('suolvetu,tu,shipin,zutu,wenjianzu')->where('id','in', $id)->select();
                    Catfish::db('product')
                        ->where('id','in', $id)
                        ->delete();
                    Catfish::db('product_cate_relationships')
                        ->where('stid','in',$id)
                        ->delete();
                    Catfish::db('product_comments')
                        ->where('stid','in',$id)
                        ->delete();
                    foreach((array)$re as $val){
                        $this->deleteResource($val['suolvetu'], $val['shipin'], $val['zutu'], $val['wenjianzu'], $val['tu']);
                    }
                    break;
            }
        }
    }
    public function editpage()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->writenewsPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('page')->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        echo Catfish::lang('The alias already exists, please change one');
                        exit();
                    }
                }
                $id = Catfish::db('page')->insertGetId([
                    'uid' => Catfish::getSession('user_id'),
                    'guanjianzi' => str_replace('，',',',Catfish::getPost('guanjianzi')),
                    'fabushijian' => Catfish::getPost('fabushijian'),
                    'alias' => $data['alias'],
                    'zhengwen' => Catfish::getPost('zhengwen', false),
                    'biaoti' => $data['biaoti'],
                    'zhaiyao' => Catfish::getPost('zhaiyao'),
                    'comment_status' => Catfish::getPost('pinglun'),
                    'gengxinshijian' => Catfish::getPost('fabushijian'),
                    'suolvetu' => Catfish::getPost('suolvetu'),
                    'tu' => Catfish::getPost('zstu'),
                    'shipin' => Catfish::getPost('shipin'),
                    'zutu' => trim(Catfish::getPost('zutu'),','),
                    'wenjianzu' => trim(Catfish::getPost('wenjianzu'),','),
                    'template' => Catfish::getPost('template')
                ]);
                echo 'ok';
                exit();
            }
        }
        Catfish::allot('muban', Catfish::getTemplate('page'));
        return $this->show(Catfish::lang('Edit single page'), 'page', 'editpage', true);
    }
    public function singlelist()
    {
        $this->checkUser();
        $data = Catfish::view('page','id,fabushijian,biaoti,review,pinglunshu,suolvetu,tu,shipin,zutu,wenjianzu,yuedu')
            ->view('users','yonghu','users.id=page.uid')
            ->where('page.status','=',1)
            ->order('page.id desc')
            ->paginate(20);
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        return $this->show(Catfish::lang('Single page list'), 'page', 'singlelist');
    }
    public function pagealiaschk()
    {
        if(Catfish::isPost()){
            echo $this->aliaschk('page');
            exit();
        }
    }
    public function searchpage()
    {
        $this->checkUser();
        $start = Catfish::getGet('start');
        if(empty($start)){
            $start = '2000-01-01 01:01:01';
        }
        $end = Catfish::getGet('end');
        if(empty($end)){
            $end = Catfish::now();
        }
        $key = Catfish::getGet('key');
        if(empty($key)){
            $key = '';
        }
        if(strtotime($start) > strtotime($end))
        {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }
        $data = Catfish::view('page','id,fabushijian,biaoti,review,pinglunshu,suolvetu,tu,shipin,zutu,wenjianzu,yuedu')
            ->view('users','yonghu','users.id=page.uid')
            ->where('page.status','=',1)
            ->whereTime('page.fabushijian', 'between', [$start, $end])
            ->where('page.biaoti|page.zhengwen','like','%'.$key.'%')
            ->order('page.id desc')
            ->paginate(20,false,[
                'query' => [
                    'start' => urlencode($start),
                    'end' => urlencode($end),
                    'key' => urlencode($key)
                ]
            ]);
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        return $this->show(Catfish::lang('Single page management').' - '.Catfish::lang('Search results'), 'page', 'singlelist', false, 'singlelist');
    }
    public function editingpage()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->writenewsPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                if(!empty($data['alias'])){
                    $alias = Catfish::db('page')->where('id','<>',Catfish::getPost('id'))->where('alias',$data['alias'])->find();
                    if(!empty($alias)){
                        echo Catfish::lang('The alias already exists, please change one');
                        exit();
                    }
                }
                $id = Catfish::getPost('id');
                $tone = Catfish::db('page')->where('id',$id)->field('uid')->find();
                if($tone['uid'] != Catfish::getSession('user_id')){
                    echo Catfish::lang('You do not have permission to operate');
                    exit();
                }
                Catfish::db('page')
                    ->where('id',$id)
                    ->update([
                        'guanjianzi' => str_replace('，',',',Catfish::getPost('guanjianzi')),
                        'fabushijian' => Catfish::getPost('fabushijian'),
                        'alias' => $data['alias'],
                        'zhengwen' => Catfish::getPost('zhengwen', false),
                        'biaoti' => $data['biaoti'],
                        'zhaiyao' => Catfish::getPost('zhaiyao'),
                        'comment_status' => Catfish::getPost('pinglun'),
                        'gengxinshijian' => Catfish::getPost('fabushijian'),
                        'suolvetu' => Catfish::getPost('suolvetu'),
                        'tu' => Catfish::getPost('zstu'),
                        'shipin' => Catfish::getPost('shipin'),
                        'zutu' => trim(Catfish::getPost('zutu'),','),
                        'wenjianzu' => trim(Catfish::getPost('wenjianzu'),','),
                        'template' => Catfish::getPost('template')
                    ]);
                echo 'ok';
                exit();
            }
        }
        $catfishID = Catfish::getGet('catfish');
        Catfish::allot('muban', Catfish::getTemplate('page'));
        $catfishItem = Catfish::db('page')->where('id',$catfishID)->find();
        $catfishItem['zhengwen'] = str_replace('&','&amp;',$catfishItem['zhengwen']);
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Edit single page'), 'page', 'singlelist', true);
    }
    public function recyclingpage()
    {
        if(Catfish::isPost()){
            Catfish::db('page')
                ->where('id', Catfish::getPost('id'))
                ->update([
                    'status' => 0
                ]);
            echo 'ok';
            exit();
        }
    }
    public function pageBatch()
    {
        if(Catfish::isPost()){
            $xiugai = '';
            $zhi = 0;
            switch(Catfish::getPost('cz')){
                case 'pshanchu':
                    $xiugai = 'status';
                    $zhi = 0;
                    break;
            }
            if(!empty($xiugai)){
                Catfish::db('page')
                    ->where('id','in',Catfish::getPost('zcuan'))
                    ->update([$xiugai => $zhi]);
            }
            echo 'ok';
            exit();
        }
    }
    public function singlecomments()
    {
        $this->checkUser();
        $catfish = Catfish::view('page_comments','id,stid,createtime,content,status')
            ->view('users','yonghu,email,touxiang','users.id=page_comments.uid')
            ->order('page_comments.createtime desc')
            ->paginate(20);
        Catfish::allot('pages', $catfish->render());
        $catfishcms = $catfish->items();
        foreach($catfishcms as $key => $val){
            if(!empty($val['touxiang']) && substr($val['touxiang'], 0, 5) == 'data/'){
                $catfishcms[$key]['touxiang'] = Catfish::domain() . $val['touxiang'];
            }
        }
        Catfish::allot('catfishcms', $catfishcms);
        return $this->show(Catfish::lang('All comments'), 'page', 'singlecomments');
    }
    public function pageshenhepinglun()
    {
        if(Catfish::isPost()){
            $zt = Catfish::getPost('zt');
            if($zt == 1)
            {
                $zt = 0;
            }
            else
            {
                $zt = 1;
            }
            Catfish::db('page_comments')
                ->where('id', Catfish::getPost('id'))
                ->update(['status' => $zt]);
            echo 'ok';
            exit();
        }
    }
    public function pagecommentdel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $catfish = Catfish::db('page_comments')->where('id',$id)->field('stid')->find();
            Catfish::db('page_comments')
                ->where('id', $id)
                ->delete();
            Catfish::db('page')
                ->where('id', $catfish['stid'])
                ->setDec('pinglunshu');
            echo 'ok';
            exit();
        }
    }
    public function pagecommentbatch()
    {
        if(Catfish::isPost()){
            $zhi = 0;
            switch(Catfish::getPost('cz')){
                case 'shenhe':
                    $zhi = 1;
                    break;
                case 'weishenhe':
                    $zhi = 0;
                    break;
            }
            Catfish::db('page_comments')
                ->where('id','in',Catfish::getPost('zcuan'))
                ->update(['status' => $zhi]);
            echo 'ok';
            exit();
        }
    }
    public function singlerecycle()
    {
        $this->checkUser();
        $data = Catfish::view('page','id,fabushijian,biaoti,review,pinglunshu,suolvetu,yuedu')
            ->view('users','yonghu','users.id=page.uid')
            ->where('page.status','=',0)
            ->order('page.id desc')
            ->paginate(20);
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        return $this->show(Catfish::lang('Single page management').' - '.Catfish::lang('Recycle bin'), 'page', 'singlerecycle');
    }
    public function deletepage()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $re = Catfish::db('page')->where('id',$id)->field('suolvetu,tu,shipin,zutu,wenjianzu')->find();
            Catfish::db('page')
                ->where('id', $id)
                ->delete();
            Catfish::db('page_comments')
                ->where('pageid',$id)
                ->delete();
            $this->deleteResource($re['suolvetu'], $re['shipin'], $re['zutu'], $re['wenjianzu'], $re['tu']);
            echo 'ok';
            exit();
        }
    }
    public function restorepage()
    {
        if(Catfish::isPost()){
            Catfish::db('page')
                ->where('id', Catfish::getPost('id'))
                ->update([
                    'status' => 1
                ]);
            echo 'ok';
            exit();
        }
    }
    public function recyclePageBatch()
    {
        if(Catfish::isPost()){
            switch(Catfish::getPost('cz')){
                case 'phuanyuan':
                    Catfish::db('page')
                        ->where('id','in', Catfish::getPost('zcuan'))
                        ->update([
                            'status' => 1
                        ]);
                    break;
                case 'pshanchu':
                    $id = Catfish::getPost('zcuan');
                    $re = Catfish::db('page')->field('suolvetu,tu,shipin,zutu,wenjianzu')->where('id','in', $id)->select();
                    Catfish::db('page')
                        ->where('id','in', $id)
                        ->delete();
                    Catfish::db('page_comments')
                        ->where('pageid','in',$id)
                        ->delete();
                    foreach((array)$re as $val){
                        $this->deleteResource($val['suolvetu'], $val['shipin'], $val['zutu'], $val['wenjianzu'], $val['tu']);
                    }
                    break;
            }
        }
    }
    public function slidegrouping()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $this->order('slide_cate');
        }
        $catfish = Catfish::db('slide_cate')
            ->field('id,catename,width,height,description,listorder')
            ->order('listorder asc')
            ->paginate(20);
        Catfish::allot('catfishcms', $catfish->items());
        Catfish::allot('pages', $catfish->render());
        return $this->show(Catfish::lang('Slide grouping'), 'websiterelated', 'slidegrouping');
    }
    public function slidegroupingadd()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->slidegroupingPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                Catfish::db('slide_cate')->insert([
                    'catename' => $data['catename'],
                    'width' => $data['width'],
                    'height' => $data['height'],
                    'description' => Catfish::getPost('description')
                ]);
            }
        }
        return $this->show(Catfish::lang('Add slide grouping'), 'websiterelated', 'slidegrouping');
    }
    public function slidegroupingedit()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->slidegroupingPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                Catfish::db('slide_cate')
                    ->where('id', Catfish::getPost('id'))
                    ->update([
                        'catename' => $data['catename'],
                        'width' => $data['width'],
                        'height' => $data['height'],
                        'description' => Catfish::getPost('description')
                    ]);
            }
        }
        $catfishID = Catfish::getGet('catfish');
        $catfishItem = Catfish::db('slide_cate')->where('id',$catfishID)->field('id,catename,width,height,description')->find();
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Modify slide grouping'), 'websiterelated', 'slidegrouping');
    }
    public function slidegroupingdel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $instr = '';
            $catfishslide = [];
            $catfishslider = Catfish::db('slide_cate_relationships')->where('cateid', $id)->field('slideid')->select();
            foreach($catfishslider as $key => $val){
                $instr .= ','.$val['slideid'];
            }
            $instr = trim($instr, ',');
            if(!empty($instr)){
                $catfishslide = Catfish::db('slide')->where('id', 'in', $instr)->field('tupian')->select();
            }
            Catfish::db('slide_cate')
                ->where('id', $id)
                ->delete();
            Catfish::db('slide_cate_relationships')
                ->where('cateid', $id)
                ->delete();
            if(!empty($instr)){
                Catfish::db('slide')
                    ->where('id', 'in', $instr)
                    ->delete();
                foreach($catfishslide as $key => $val){
                    $this->deleteResource('', $val['tupian']);
                }
            }
            echo 'ok';
            exit();
        }
    }
    public function addslideshow()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->addslideshowPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $id = Catfish::db('slide')->insertGetId([
                    'mingcheng' => Catfish::getPost('mingcheng'),
                    'tupian' => $data['slideshow'],
                    'lianjie' => Catfish::getPost('lianjie'),
                    'miaoshu' => Catfish::getPost('miaoshu'),
                ]);
                Catfish::db('slide_cate_relationships')->insert([
                    'slideid' => $id,
                    'cateid' => Catfish::getPost('slidegrouping')
                ]);
                Catfish::removeCache('huandeng');
                echo 'ok';
                exit();
            }
        }
        $catfishcms = Catfish::db('slide_cate')->field('id,catename,width,height')->select();
        Catfish::allot('catfishcms', $catfishcms);
        $catfishslide = [];
        foreach($catfishcms as $key => $val){
            $catfishslide[$val['id']] = [
                'width' => $val['width'],
                'height' => $val['height']
            ];
        }
        Catfish::allot('catfishslide', Catfish::json($catfishslide));
        Catfish::allot('slideshowWidth', 820);
        Catfish::allot('slideshowHeight', 390);
        return $this->show(Catfish::lang('Add a slide'), 'websiterelated', 'slideshow', true);
    }
    public function slideshow()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $this->order('slide');
        }
        $data = Catfish::view('slide_cate_relationships','id,slideid')
            ->view('slide','mingcheng,tupian,lianjie,miaoshu,status,listorder','slide.id=slide_cate_relationships.slideid')
            ->view('slide_cate','catename','slide_cate.id=slide_cate_relationships.cateid')
            ->order('slide_cate_relationships.cateid asc,slide.listorder asc,slide.id asc')
            ->paginate(20);
        Catfish::allot('data', $data->items());
        Catfish::allot('pages', $data->render());
        return $this->show(Catfish::lang('Slides'), 'websiterelated', 'slideshow');
    }
    public function yincangqiyong()
    {
        if(Catfish::isPost()){
            $zt = Catfish::getPost('zt');
            if($zt == 1)
            {
                $zt = 0;
            }
            else
            {
                $zt = 1;
            }
            Catfish::db('slide')
                ->where('id', Catfish::getPost('id'))
                ->update(['status' => $zt]);
            Catfish::removeCache('huandeng');
            exit();
        }
    }
    public function removeSlide()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $catfishslide = Catfish::db('slide')->where('id', $id)->field('tupian')->find();
            Catfish::db('slide')
                ->where('id', $id)
                ->delete();
            Catfish::db('slide_cate_relationships')
                ->where('slideid', $id)
                ->delete();
            $this->deleteResource('', $catfishslide['tupian']);
            Catfish::removeCache('huandeng');
            echo 'ok';
            exit();
        }
    }
    public function editingslide()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->addslideshowPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                Catfish::db('slide')
                    ->where('id', Catfish::getPost('id'))
                    ->update([
                        'mingcheng' => Catfish::getPost('mingcheng'),
                        'tupian' => $data['slideshow'],
                        'lianjie' => Catfish::getPost('lianjie'),
                        'miaoshu' => Catfish::getPost('miaoshu'),
                    ]);
                Catfish::db('slide_cate_relationships')
                    ->where('slideid', Catfish::getPost('id'))
                    ->update([
                        'cateid' => Catfish::getPost('slidegrouping')
                    ]);
                Catfish::removeCache('huandeng');
                echo 'ok';
                exit();
            }
        }
        $catfishcms = Catfish::db('slide_cate')->field('id,catename,width,height')->select();
        Catfish::allot('catfishcms', $catfishcms);
        $catfishslide = [];
        foreach($catfishcms as $key => $val){
            $catfishslide[$val['id']] = [
                'width' => $val['width'],
                'height' => $val['height']
            ];
        }
        Catfish::allot('catfishslide', Catfish::json($catfishslide));
        Catfish::allot('slideshowWidth', 820);
        Catfish::allot('slideshowHeight', 390);
        $catfishID = Catfish::getGet('catfish');
        $catfishItem = Catfish::db('slide')->where('id',$catfishID)->field('id,mingcheng,tupian,lianjie,miaoshu')->find();
        $catfish = Catfish::db('slide_cate_relationships')->where('slideid',$catfishID)->field('cateid')->find();
        $catfishItem['cateid'] = $catfish['cateid'];
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Editing slide'), 'websiterelated', 'slideshow', true);
    }
    public function addlinks()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->addlinksPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $shouye = 0;
                if(Catfish::getPost('shouye') == 'on'){
                    $shouye = 1;
                }
                Catfish::db('links')->insert([
                    'dizhi' => $data['dizhi'],
                    'mingcheng' => $data['mingcheng'],
                    'tubiao' => Catfish::getPost('tubiao'),
                    'target' => Catfish::getPost('target'),
                    'miaoshu' => Catfish::getPost('miaoshu'),
                    'shouye' => $shouye
                ]);
                Catfish::removeCache('youlian');
                echo 'ok';
                exit();
            }
        }
        return $this->show(Catfish::lang('Add links'), 'websiterelated', 'links', true);
    }
    public function links()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $this->order('links');
        }
        $catfishcms = Catfish::db('links')
            ->field('id,dizhi,mingcheng,tubiao,shouye,status,listorder')
            ->order('listorder asc,id desc')
            ->paginate(20);
        Catfish::allot('data', $catfishcms->items());
        Catfish::allot('pages', $catfishcms->render());
        return $this->show(Catfish::lang('Add links'), 'websiterelated', 'links');
    }
    public function linkyincangqiyong()
    {
        if(Catfish::isPost()){
            $zt = Catfish::getPost('zt');
            if($zt == 1)
            {
                $zt = 0;
            }
            else
            {
                $zt = 1;
            }
            Catfish::db('links')
                ->where('id', Catfish::getPost('id'))
                ->update(['status' => $zt]);
            Catfish::removeCache('youlian');
            exit();
        }
    }
    public function removeLink()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $catfishlink = Catfish::db('links')->where('id', $id)->field('tubiao')->find();
            Catfish::db('links')
                ->where('id', $id)
                ->delete();
            $this->deleteResource('', $catfishlink['tubiao']);
            Catfish::removeCache('youlian');
            echo 'ok';
            exit();
        }
    }
    public function editinglink()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->addlinksPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $shouye = 0;
                if(Catfish::getPost('shouye') == 'on'){
                    $shouye = 1;
                }
                Catfish::db('links')
                    ->where('id', Catfish::getPost('id'))
                    ->update([
                        'dizhi' => $data['dizhi'],
                        'mingcheng' => $data['mingcheng'],
                        'tubiao' => Catfish::getPost('tubiao'),
                        'target' => Catfish::getPost('target'),
                        'miaoshu' => Catfish::getPost('miaoshu'),
                        'shouye' => $shouye
                    ]);
                Catfish::removeCache('youlian');
                echo 'ok';
                exit();
            }
        }
        $catfishID = Catfish::getGet('catfish');
        $catfishItem = Catfish::db('links')->where('id',$catfishID)->field('id,dizhi,mingcheng,tubiao,target,miaoshu,shouye')->find();
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Editing friendship link'), 'websiterelated', 'links', true);
    }
    public function messages()
    {
        $this->checkUser();
        $catfishcms = Catfish::db('guestbook')->field('id,full_name,email,shouji,qq,wechat,title,msg,createtime')->order('createtime desc')->paginate(20);
        Catfish::allot('catfishcms', $catfishcms->items());
        Catfish::allot('pages', $catfishcms->render());
        return $this->show(Catfish::lang('All messages'), 'websiterelated', 'messages');
    }
    public function messagesdel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            Catfish::db('guestbook')
                ->where('id', $id)
                ->whereOr('parent_id', $id)
                ->delete();
            echo 'ok';
            exit();
        }
    }
    public function selflabeling()
    {
        $this->checkUser();
        $catfish = Catfish::db('label')->field('id,biaoqian,outpos,content,remarks')->paginate(20);
        $catfishcms = $catfish->items();
        $catfisheff = [
            'all' => Catfish::lang('Full website effective'),
            'home' => Catfish::lang('Only the first page is valid'),
            'list' => Catfish::lang('All list pages are valid'),
            'newslist' => Catfish::lang('News list page is valid'),
            'productlist' => Catfish::lang('Product list page is valid'),
            'search' => Catfish::lang('Search results page is valid'),
            'content' => Catfish::lang('All content pages are valid'),
            'news' => Catfish::lang('Only news content page is valid'),
            'product' => Catfish::lang('Only product content pages are valid'),
            'page' => Catfish::lang('Only a single page is valid')
        ];
        foreach((array)$catfishcms as $key => $val){
            $catfishcms[$key]['outpos'] = $catfisheff[$val['outpos']];
        }
        Catfish::allot('catfishcms', $catfishcms);
        Catfish::allot('pages', $catfish->render());
        return $this->show(Catfish::lang('Self-labeling'), 'websiterelated', 'selflabeling');
    }
    public function selflabelingadd()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->selflabelingPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $catfish = Catfish::db('label')->where('biaoqian',$data['biaoqian'])->find();
                if(!empty($catfish)){
                    echo Catfish::lang('The label name already exists, please change one');
                    exit();
                }
                Catfish::db('label')->insert([
                    'biaoqian' => $data['biaoqian'],
                    'outpos' => Catfish::getPost('outpos'),
                    'content' => Catfish::getPost('content', false),
                    'remarks' => Catfish::getPost('remarks')
                ]);
                echo 'ok';
                exit();
            }
        }
        return $this->show(Catfish::lang('Self-labeling').' - '.Catfish::lang('Add a custom label'), 'websiterelated', 'selflabeling');
    }
    public function selflabelingchk()
    {
        if(Catfish::isPost()){
            $biaoqian = strtolower(trim(Catfish::getPost('biaoqian')));
            $id = Catfish::getPost('id');
            if(empty($id)){
                $catfishcms = Catfish::db('label')->where('biaoqian',$biaoqian)->find();
            }
            else{
                $catfishcms = Catfish::db('label')->where('biaoqian',$biaoqian)->where('id','<>',$id)->find();
            }
            if(!empty($catfishcms)){
                echo Catfish::lang('The label name already exists, please change one');
                exit();
            }
            else{
                echo 'ok';
                exit();
            }
        }
    }
    public function selflabelingdel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            Catfish::db('label')
                ->where('id', $id)
                ->delete();
            echo 'ok';
            exit();
        }
    }
    public function selflabelingedit()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->selflabelingPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $id = Catfish::getPost('id');
                $catfish = Catfish::db('label')->where('biaoqian',$data['biaoqian'])->where('id','<>',$id)->find();
                if(!empty($catfish)){
                    echo Catfish::lang('The label name already exists, please change one');
                    exit();
                }
                Catfish::db('label')
                    ->where('id', $id)
                    ->update([
                        'biaoqian' => $data['biaoqian'],
                        'outpos' => Catfish::getPost('outpos'),
                        'content' => Catfish::getPost('content', false),
                        'remarks' => Catfish::getPost('remarks')
                    ]);
                echo 'ok';
                exit();
            }
        }
        $catfishItem = Catfish::db('label')->where('id',Catfish::getGet('c'))->find();
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Modify a custom label'), 'websiterelated', 'selflabeling');
    }
    public function homeshow()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $home = Catfish::db('home')->where('id',1)->field('id')->find();
            if(empty($home)){
                Catfish::db('home')->insert([
                    'id' => 1,
                    'biaoti' => Catfish::getPost('biaoti'),
                    'zhengwen' => Catfish::getPost('zhengwen', false),
                    'tu' => Catfish::getPost('zstu'),
                    'shipin' => Catfish::getPost('shipin'),
                    'zutu' => trim(Catfish::getPost('zutu'),',')
                ]);
            }
            else{
                Catfish::db('home')->where('id', 1)->update([
                    'id' => 1,
                    'biaoti' => Catfish::getPost('biaoti'),
                    'zhengwen' => Catfish::getPost('zhengwen', false),
                    'tu' => Catfish::getPost('zstu'),
                    'shipin' => Catfish::getPost('shipin'),
                    'zutu' => trim(Catfish::getPost('zutu'),',')
                ]);
            }
            Catfish::removeCache('shouyezhanshi');
            echo 'ok';
            exit();
        }
        $catfishItem = Catfish::db('home')->where('id',1)->find();
        if(empty($catfishItem)){
            $catfishItem = [
                'biaoti' => '',
                'zhengwen' => '',
                'tu' => '',
                'shipin' => '',
                'zutu' => ''
            ];
        }
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Home show'), 'websiterelated', 'homeshow');
    }
    public function alipay()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->alipayPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $publickey = Catfish::getPost('publickey', false);
                $yingyonggongyao = Catfish::getPost('yingyonggongyao');
                $zhifubaogongyao = Catfish::getPost('zhifubaogongyao');
                $zhifubaogen = Catfish::getPost('zhifubaogen');
                if($data['qianming'] == 'gongyao' && empty($publickey)){
                    echo Catfish::lang('Alipay public key must be filled in');
                    exit();
                }
                elseif($data['qianming'] == 'gongyaozhengshu'){
                    if(empty($yingyonggongyao)){
                        echo Catfish::lang('Application public key certificate must be uploaded');
                        exit();
                    }
                    elseif(empty($zhifubaogongyao)){
                        echo Catfish::lang('Alipay public key certificate must be uploaded');
                        exit();
                    }
                    elseif(empty($zhifubaogen)){
                        echo Catfish::lang('Alipay root certificate must be uploaded');
                        exit();
                    }
                }
                $svarr = [
                    'appid' => $data['appid'],
                    'merchantuid' => $data['merchantuid'],
                    'privatekey' => $data['privatekey'],
                    'qianming' => $data['qianming'],
                    'publickey' => $publickey,
                    'yingyonggongyao' => $yingyonggongyao,
                    'zhifubaogongyao' => $zhifubaogongyao,
                    'zhifubaogen' => $zhifubaogen
                ];
                Catfish::set('alipay_config', serialize($svarr));
                echo 'ok';
                exit();
            }
        }
        $catfishItem = Catfish::get('alipay_config');
        if(!empty($catfishItem)){
            $catfishItem = unserialize($catfishItem);
        }
        else{
            $catfishItem = [
                'appid' => '',
                'merchantuid' => '',
                'privatekey' => '',
                'qianming' => 'gongyao',
                'publickey' => '',
                'yingyonggongyao' => '',
                'zhifubaogongyao' => '',
                'zhifubaogen' => ''
            ];
        }
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Alipay'), 'websiterelated', 'alipay', true);
    }
    public function companyprofile()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            Catfish::db('company')
                ->where('id', 1)
                ->update([
                    'mingcheng' => Catfish::getPost('mingcheng'),
                    'dizhi' => Catfish::getPost('dizhi'),
                    'dianhua' => Catfish::getPost('dianhua'),
                    'chuanzhen' => Catfish::getPost('chuanzhen'),
                    'wangzhi' => Catfish::getPost('wangzhi'),
                    'email' => Catfish::getPost('email'),
                    'jianjie' => Catfish::getPost('jianjie')
                ]);
            Catfish::removeCache('qiye');
        }
        $catfishItem = Catfish::db('company')->where('id',1)->field('id,mingcheng,dizhi,dianhua,chuanzhen,wangzhi,email,jianjie')->find();
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Company profile'), 'corporateinformation', 'companyprofile');
    }
    public function corporatehistory()
    {
        $this->checkUser();
        $catfish = Catfish::db('history')
            ->field('id,shijian,tu,shipin,biaoti,xiangqing')
            ->order('shijian desc')
            ->paginate(20);
        $catfishitems = $catfish->items();
        foreach($catfishitems as $key => $val){
            if($val['shijian'] == '2000-01-01 00:00:00'){
                $catfishitems[$key]['shijian'] = '';
            }
        }
        Catfish::allot('catfishcms', $catfishitems);
        Catfish::allot('pages', $catfish->render());
        return $this->show(Catfish::lang('Corporate history'), 'corporateinformation', 'corporatehistory');
    }
    public function corporatehistoryadd()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->corporatehistoryPost();
            $shijian = Catfish::getPost('shijian');
            if(empty($shijian)){
                $shijian = '2000-01-01 00:00:00';
            }
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                Catfish::db('history')->insert([
                    'shijian' => $shijian,
                    'tu' => Catfish::getPost('zstu'),
                    'shipin' => Catfish::getPost('shipin'),
                    'biaoti' => $data['biaoti'],
                    'xiangqing' => Catfish::getPost('xiangqing')
                ]);
                Catfish::removeCache('lishi');
            }
        }
        return $this->show(Catfish::lang('Add history'), 'corporateinformation', 'corporatehistory', true);
    }
    public function corporatehistoryedit()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->corporatehistoryPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $shijian = Catfish::getPost('shijian');
                if(empty($shijian)){
                    $shijian = '2000-01-01 00:00:00';
                }
                Catfish::db('history')
                    ->where('id', Catfish::getPost('id'))
                    ->update([
                        'shijian' => $shijian,
                        'tu' => Catfish::getPost('zstu'),
                        'shipin' => Catfish::getPost('shipin'),
                        'biaoti' => $data['biaoti'],
                        'xiangqing' => Catfish::getPost('xiangqing')
                    ]);
                Catfish::removeCache('lishi');
            }
        }
        $catfishID = Catfish::getGet('catfish');
        $catfishItem = Catfish::db('history')->where('id',$catfishID)->field('id,shijian,tu,shipin,biaoti,xiangqing')->find();
        if($catfishItem['shijian'] == '2000-01-01 00:00:00'){
            $catfishItem['shijian'] = '';
        }
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Add history'), 'corporateinformation', 'corporatehistory', true);
    }
    public function corporatehistorydel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            Catfish::db('history')
                ->where('id', $id)
                ->delete();
            Catfish::removeCache('lishi');
            echo 'ok';
            exit();
        }
    }
    public function menucategoryadd()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->menucategoryPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $active = 0;
                if(Catfish::getPost('zhucaidan') == 'on'){
                    $active = 1;
                }
                if($active == 1){
                    Catfish::db('navcat')
                        ->where('active',1)
                        ->update([
                            'active' => 0
                        ]);
                }
                Catfish::db('navcat')->insert([
                    'nav_name' => $data['fenleiming'],
                    'active' => $active,
                    'remark' => Catfish::getPost('miaoshu')
                ]);
            }
        }
        return $this->show(Catfish::lang('Add menu categories'), 'caidan', 'menucategories', true);
    }
    public function menucategories()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $this->order('navcat');
        }
        $catfish = Catfish::db('navcat')
            ->field('id,nav_name,active,remark,listorder')
            ->order('active desc,listorder asc,id desc')
            ->select();
        $ord = 0;
        foreach($catfish as $key => $val){
            $catfish[$key]['order'] = ++$ord;
        }
        Catfish::allot('catfishcms', $catfish);
        return $this->show(Catfish::lang('Menu categories'), 'caidan', 'menucategories');
    }
    public function menucategoryedit()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->menucategoryPost();
            if(!is_array($data)){
                Catfish::error($data);
                return false;
            }
            else{
                $active = 0;
                if(Catfish::getPost('zhucaidan') == 'on'){
                    $active = 1;
                }
                if($active == 1){
                    Catfish::db('navcat')
                        ->where('active',1)
                        ->update([
                            'active' => 0
                        ]);
                }
                Catfish::db('navcat')
                    ->where('id',Catfish::getPost('id'))
                    ->update([
                        'nav_name' => $data['fenleiming'],
                        'active' => $active,
                        'remark' => Catfish::getPost('miaoshu')
                    ]);
            }
        }
        $catfishID = Catfish::getGet('catfish');
        $catfishItem = Catfish::db('navcat')->where('id',$catfishID)->field('id,nav_name,active,remark')->find();
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Edit menu category'), 'caidan', 'menucategories', true);
    }
    public function menucategoriesdel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            Catfish::db('navcat')
                ->where('id', $id)
                ->delete();
            echo 'ok';
            exit();
        }
    }
    public function addmenu()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->addmenuPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $lianjie = Catfish::getPost('lianjie');
                $zidingyi = Catfish::getPost('zidingyi');
                if(empty($lianjie) && empty($zidingyi)){
                    echo Catfish::lang('Link cannot be empty');
                    exit();
                }
                Catfish::db('nav_cate')->insert([
                    'cid' => $data['caidanfenlei'],
                    'parent_id' => Catfish::getPost('fuji'),
                    'label' => $data['caidanming'],
                    'target' => Catfish::getPost('dakaifangshi'),
                    'href' => $lianjie,
                    'link' => $zidingyi,
                    'icon' => str_replace('\'','"',Catfish::getPost('tubiao',false)),
                    'status' => Catfish::getPost('zhuangtai'),
                    'miaoshu' => Catfish::getPost('miaoshu'),
                    'suolvetu' => Catfish::getPost('suolvetu')
                ]);
                Catfish::removeCache('caidan');
                echo 'ok';
                exit();
            }
        }
        $catfishcms = Catfish::db('navcat')->field('id,nav_name')->order('active desc,listorder asc,id desc')->select();
        Catfish::allot('catfishcms', $catfishcms);
        Catfish::allot('news', Catfish::getSort('news','id,catename,parent_id','&#12288;'));
        Catfish::allot('product', Catfish::getSort('product','id,catename,parent_id','&#12288;'));
        $catfishpage = Catfish::db('page')->field('id,biaoti')->order('id desc')->select();
        Catfish::allot('page', $catfishpage);
        return $this->show(Catfish::lang('Add a menu'), 'caidan', 'addmenu', true);
    }
    public function changeParent()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            if(empty($id)){
                $catfishcms = Catfish::getSort('nav','id,label,parent_id','&#12288;',['cid',Catfish::getPost('cid')]);
            }
            else{
                $catfishcms = Catfish::getSortNoSelf('nav',$id,'id,label,parent_id','&#12288;',['cid',Catfish::getPost('cid')]);
            }
            if(empty($catfishcms) || count($catfishcms) == 0){
                echo '';
                exit();
            }
            $restr = '';
            foreach($catfishcms as $val){
                $restr .= '<option value="'.$val['id'].'">'.$val['level'];
                if(!empty($val['level'])){
                    $restr .= '└&nbsp;';
                }
                $restr .= $val['label'].'</option>';
            }
            echo $restr;
            exit();
        }
    }
    public function managemenu()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $this->order('nav_cate');
            Catfish::removeCache('caidan');
        }
        $caidanfenlei = Catfish::getPost('caidanfenlei');
        $catfish = Catfish::db('navcat')
            ->field('id,nav_name')
            ->order('active desc,listorder asc,id desc')
            ->select();
        foreach($catfish as $key => $val){
            if(!empty($caidanfenlei) && $caidanfenlei == $val['id']){
                $catfish[$key]['current'] = 1;
            }
            else{
                $catfish[$key]['current'] = 0;
            }
        }
        Catfish::allot('catfish', $catfish);
        if(empty($caidanfenlei)){
            $cid = isset($catfish[0]['id']) ? $catfish[0]['id'] : 0;
        }
        else{
            $cid = $caidanfenlei;
        }
        $catfishcms = Catfish::getSort('nav','id,parent_id,label,status,listorder','&#12288;',['cid',$cid],'listorder asc');
        Catfish::allot('catfishcms', $catfishcms);
        return $this->show(Catfish::lang('All menus'), 'caidan', 'managemenu');
    }
    public function addsubmenu()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->addmenuPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $lianjie = Catfish::getPost('lianjie');
                $zidingyi = Catfish::getPost('zidingyi');
                if(empty($lianjie) && empty($zidingyi)){
                    echo Catfish::lang('Link cannot be empty');
                    exit();
                }
                Catfish::db('nav_cate')->insert([
                    'cid' => $data['caidanfenlei'],
                    'parent_id' => Catfish::getPost('fuji'),
                    'label' => $data['caidanming'],
                    'target' => Catfish::getPost('dakaifangshi'),
                    'href' => $lianjie,
                    'link' => $zidingyi,
                    'icon' => str_replace('\'','"',Catfish::getPost('tubiao',false)),
                    'status' => Catfish::getPost('zhuangtai'),
                    'miaoshu' => Catfish::getPost('miaoshu'),
                    'suolvetu' => Catfish::getPost('suolvetu')
                ]);
                Catfish::removeCache('caidan');
                echo 'ok';
                exit();
            }
        }
        $catfishID = Catfish::getGet('catfish');
        $cdzu = Catfish::db('nav_cate')->where('id',$catfishID)->field('id,cid')->find();
        $catfishcms = Catfish::db('navcat')->field('id,nav_name')->order('active desc,listorder asc,id desc')->select();
        foreach($catfishcms as $key => $val){
            if($val['id'] == $cdzu['cid']){
                $catfishcms[$key]['current'] = 1;
            }
            else{
                $catfishcms[$key]['current'] = 0;
            }
        }
        Catfish::allot('catfishcms', $catfishcms);
        $caidan = Catfish::getSort('nav','id,label,parent_id','&#12288;',['cid',$cdzu['cid']],'listorder asc');
        foreach($caidan as $key => $val){
            if($val['id'] == $catfishID){
                $caidan[$key]['current'] = 1;
            }
            else{
                $caidan[$key]['current'] = 0;
            }
        }
        Catfish::allot('caidan', $caidan);
        Catfish::allot('news', Catfish::getSort('news','id,catename,parent_id','&#12288;'));
        Catfish::allot('product', Catfish::getSort('product','id,catename,parent_id','&#12288;'));
        $catfishpage = Catfish::db('page')->field('id,biaoti')->order('id desc')->select();
        Catfish::allot('page', $catfishpage);
        return $this->show(Catfish::lang('Add a submenu'), 'caidan', 'managemenu', true);
    }
    public function editingmenu()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->addmenuPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $lianjie = Catfish::getPost('lianjie');
                $zidingyi = Catfish::getPost('zidingyi');
                if(empty($lianjie) && empty($zidingyi)){
                    echo Catfish::lang('Link cannot be empty');
                    exit();
                }
                Catfish::db('nav_cate')
                    ->where('id',Catfish::getPost('id'))
                    ->update([
                        'cid' => $data['caidanfenlei'],
                        'parent_id' => Catfish::getPost('fuji'),
                        'label' => $data['caidanming'],
                        'target' => Catfish::getPost('dakaifangshi'),
                        'href' => $lianjie,
                        'link' => $zidingyi,
                        'icon' => str_replace('\'','"',Catfish::getPost('tubiao',false)),
                        'status' => Catfish::getPost('zhuangtai'),
                        'miaoshu' => Catfish::getPost('miaoshu'),
                        'suolvetu' => Catfish::getPost('suolvetu')
                    ]);
                Catfish::removeCache('caidan');
                echo 'ok';
                exit();
            }
        }
        $catfishID = Catfish::getGet('catfish');
        $cdzu = Catfish::db('nav_cate')->where('id',$catfishID)->field('id,cid,parent_id,label,target,href,link,icon,status,miaoshu,suolvetu')->find();
        Catfish::allot('catfishItem', $cdzu);
        $catfishcms = Catfish::db('navcat')->field('id,nav_name')->order('active desc,listorder asc,id desc')->select();
        foreach($catfishcms as $key => $val){
            if($val['id'] == $cdzu['cid']){
                $catfishcms[$key]['current'] = 1;
            }
            else{
                $catfishcms[$key]['current'] = 0;
            }
        }
        Catfish::allot('catfishcms', $catfishcms);
        $caidan = Catfish::getSortNoSelf('nav',$catfishID,'id,label,parent_id','&#12288;',['cid',$cdzu['cid']],'listorder asc');
        foreach($caidan as $key => $val){
            if($val['id'] == $cdzu['parent_id']){
                $caidan[$key]['current'] = 1;
            }
            else{
                $caidan[$key]['current'] = 0;
            }
        }
        Catfish::allot('caidan', $caidan);
        Catfish::allot('news', Catfish::getSort('news','id,catename,parent_id','&#12288;'));
        Catfish::allot('product', Catfish::getSort('product','id,catename,parent_id','&#12288;'));
        $catfishpage = Catfish::db('page')->field('id,biaoti')->order('id desc')->select();
        Catfish::allot('page', $catfishpage);
        return $this->show(Catfish::lang('Modify menu'), 'caidan', 'managemenu', true);
    }
    public function managemenudel()
    {
        if(Catfish::isPost()){
            $id = Catfish::getPost('id');
            $catfish = Catfish::db('nav_cate')->where('id',$id)->field('parent_id')->find();
            Catfish::db('nav_cate')
                ->where('id',$id)
                ->delete();
            Catfish::db('nav_cate')
                ->where('parent_id', $id)
                ->update([
                    'parent_id' => $catfish['parent_id']
                ]);
            Catfish::removeCache('caidan');
            echo 'ok';
            exit();
        }
    }
    public function general()
    {
        $this->checkUser();
        $catfish = Catfish::db('users')
            ->where('id','>',1)
            ->field('id,yonghu,nicheng,email,touxiang,createtime,status')
            ->order('id desc')
            ->paginate(20);
        Catfish::allot('pages', $catfish->render());
        $catfishcms = $catfish->items();
        foreach($catfishcms as $key => $val){
            if(!empty($val['touxiang']) && substr($val['touxiang'], 0, 5) == 'data/'){
                $catfishcms[$key]['touxiang'] = Catfish::domain() . $val['touxiang'];
            }
        }
        Catfish::allot('catfishcms', $catfishcms);
        return $this->show(Catfish::lang('General users'), 'yonghu', 'general');
    }
    public function laheiqiyong()
    {
        if(Catfish::isPost()){
            $zt = Catfish::getPost('zt');
            if($zt == 1)
            {
                $zt = 0;
            }
            else
            {
                $zt = 1;
            }
            Catfish::db('users')
                ->where('id', Catfish::getPost('id'))
                ->update(['status' => $zt]);
            exit();
        }
    }
    public function web()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            Catfish::db('options')
                ->where('option_name', 'title')
                ->update([
                    'option_value' => Catfish::getPost('title')
                ]);
            Catfish::db('options')
                ->where('option_name', 'subtitle')
                ->update([
                    'option_value' => Catfish::getPost('subtitle')
                ]);
            Catfish::db('options')
                ->where('option_name', 'keyword')
                ->update([
                    'option_value' => Catfish::getPost('keyword')
                ]);
            Catfish::db('options')
                ->where('option_name', 'description')
                ->update([
                    'option_value' => Catfish::getPost('description')
                ]);
            Catfish::db('options')
                ->where('option_name', 'record')
                ->update([
                    'option_value' => str_replace('\'','"',Catfish::getPost('record',false))
                ]);
            Catfish::db('options')
                ->where('option_name', 'copyright')
                ->update([
                    'option_value' => serialize(Catfish::getPost('copyright',false))
                ]);
            Catfish::db('options')
                ->where('option_name', 'statistics')
                ->update([
                    'option_value' => serialize(Catfish::getPost('statistics',false))
                ]);
            Catfish::db('options')
                ->where('option_name', 'email')
                ->update([
                    'option_value' => Catfish::getPost('email')
                ]);
            Catfish::db('options')
                ->where('option_name', 'filtername')
                ->update([
                    'option_value' => Catfish::getPost('guolv')
                ]);
            $comment = Catfish::getPost('pinglun') == 'on' ? 1 : 0;
            Catfish::db('options')
                ->where('option_name', 'comment')
                ->update([
                    'option_value' => $comment
                ]);
            Catfish::db('options')
                ->where('option_name', 'domain')
                ->update([
                    'option_value' => Catfish::getPost('domain')
                ]);
            Catfish::db('options')
                ->where('option_name', 'logo')
                ->update([
                    'option_value' => Catfish::getPost('tubiao')
                ]);
            $captcha = Catfish::getPost('yanzheng') == 'on' ? 1 : 0;
            Catfish::db('options')
                ->where('option_name', 'captcha')
                ->update([
                    'option_value' => $captcha
                ]);
            $rewrite = Catfish::getPost('rewrite') == 'on' ? 1 : 0;
            Catfish::db('options')
                ->where('option_name', 'rewrite')
                ->update([
                    'option_value' => $rewrite
                ]);
            $allowLogin = Catfish::getPost('allowLogin') == 'on' ? 1 : 0;
            Catfish::db('options')
                ->where('option_name', 'allowLogin')
                ->update([
                    'option_value' => $allowLogin
                ]);
            $closeSlide = Catfish::getPost('closeSlide') == 'on' ? 1 : 0;
            Catfish::db('options')
                ->where('option_name', 'closeSlide')
                ->update([
                    'option_value' => $closeSlide
                ]);
            Catfish::db('options')
                ->where('option_name', 'icon')
                ->update([
                    'option_value' => Catfish::getPost('icon')
                ]);
            Catfish::db('options')
                ->where('option_name', 'everyPageShows')
                ->update([
                    'option_value' => Catfish::getPost('everyPageShows')
                ]);
            $openMessage = Catfish::getPost('openMessage') == 'on' ? 1 : 0;
            Catfish::db('options')
                ->where('option_name', 'openMessage')
                ->update([
                    'option_value' => $openMessage
                ]);
            $closeComment = Catfish::getPost('closeComment') == 'on' ? 1 : 0;
            Catfish::db('options')
                ->where('option_name', 'closeComment')
                ->update([
                    'option_value' => $closeComment
                ]);
            Catfish::removeCache('options');
            Catfish::removeCache('yuyuecms_options_captcha');
        }
        $catfish = Catfish::db('options')->where('id','<',27)->field('option_name,option_value')->select();
        $catfishItem = [];
        foreach($catfish as $key => $val){
            if($val['option_name'] == 'copyright' || $val['option_name'] == 'statistics'){
                $catfishItem[$val['option_name']] = unserialize($val['option_value']);
            }
            else{
                $catfishItem[$val['option_name']] = $val['option_value'];
            }
        }
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Website information'), 'xitong', 'web');
    }
    public function themes()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            Catfish::set('template', Catfish::getPost('themeName'));
            Catfish::removeCache('options');
        }
        $current = Catfish::get('template');
        $catfishThemes = [];
        $domain = Catfish::domain();
        $dir = glob(ROOT_PATH.'public/theme/*',GLOB_ONLYDIR);
        foreach($dir as $key => $val){
            $tmpdir = basename($val);
            $url = $domain.'public/common/images/screenshot.jpg';
            $path = ROOT_PATH.'public/theme/'.$tmpdir.'/screenshot.jpg';
            if(is_file($path)){
                $url = $domain.'public/theme/'.$tmpdir.'/screenshot.jpg';
            }
            if($tmpdir == $current){
                array_unshift($catfishThemes,[
                    'name' => $tmpdir,
                    'url' => $url,
                    'open' => 1
                ]);
            }
            else{
                array_push($catfishThemes,[
                    'name' => $tmpdir,
                    'url' => $url,
                    'open' => 0
                ]);
            }
        }
        Catfish::allot('catfishThemes', $catfishThemes);
        return $this->show(Catfish::lang('Themes'), 'xitong', 'themes');
    }
    public function personal()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->personalPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $shengri = Catfish::getPost('shengri');
                if(empty($shengri)){
                    $shengri = null;
                }
                Catfish::db('users')
                    ->where('id', Catfish::getSession('user_id'))
                    ->update([
                        'nicheng' => Catfish::getPost('nicheng'),
                        'email' => $data['email'],
                        'url' => Catfish::getPost('url'),
                        'touxiang' => Catfish::getPost('touxiang'),
                        'xingbie' => Catfish::getPost('xingbie'),
                        'shengri' => $shengri,
                        'qianming' => Catfish::getPost('qianming'),
                        'shouji' => Catfish::getPost('shouji')
                    ]);
                echo 'ok';
                exit();
            }
        }
        $catfishItem = Catfish::db('users')->where('id',Catfish::getSession('user_id'))->field('id,nicheng,email,url,touxiang,xingbie,shengri,qianming,shouji')->find();
        Catfish::allot('catfishItem', $catfishItem);
        return $this->show(Catfish::lang('Personal information'), 'yonghu', 'personal', true);
    }
    public function change()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->changePost();
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
        return $this->show(Catfish::lang('Change password'), 'yonghu', 'change', true);
    }
    public function clearcache()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            Catfish::clearCache();
        }
        return $this->show(Catfish::lang('Clear cache'), 'xitong', 'clearcache');
    }
    public function dbbackup()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            $dbnm = Catfish::getConfig('database.database');
            $dbPrefix = Catfish::getConfig('database.prefix');
            $prefixlen = strlen($dbPrefix);
            $bkstr = '';
            $sql = "SHOW TABLES FROM {$dbnm} LIKE '{$dbPrefix}%'";
            $renm = Catfish::dbExecute($sql);
            foreach($renm as $nmval){
                reset($nmval);
                $tbnm = current($nmval);
                $onlynm = substr($tbnm, $prefixlen);
                $sql = 'SHOW COLUMNS FROM `'.$tbnm.'`';
                $re = Catfish::dbExecute($sql);
                $field = '';
                foreach($re as $val){
                    if(empty($field)){
                        $field = '`'.$val['Field'].'`';
                    }
                    else{
                        $field .= ', `'.$val['Field'].'`';
                    }
                }
                $tmp = '';
                $all = Catfish::db($onlynm)->select();
                if(is_array($all) && count($all) > 0){
                    $i = 0;
                    foreach((array)$all as $rec){
                        $str = '';
                        foreach($rec as $key => $srec){
                            if(empty($str)){
                                $str = $this->strint($srec);
                            }
                            else{
                                $str .= ', '.$this->strint($srec);
                            }
                        }
                        if(empty($tmp)){
                            $tmp .= '('.$str.')';
                        }
                        else{
                            $tmp .= ',('.$str.')';
                        }
                        $i ++ ;
                        if($i > 50){
                            $this->semiinsert($tbnm, $field, $tmp, $bkstr);
                            $tmp = '';
                            $i = 0;
                        }
                    }
                    if(!empty($tmp)){
                        $this->semiinsert($tbnm, $field, $tmp, $bkstr);
                    }
                }
            }
            $bkstr = '-- 鱼跃CMS数据库备份' . PHP_EOL . '-- 生成日期：' . date('Y-m-d H: i: s') . PHP_EOL . '-- Table prefix: ' . $dbPrefix . PHP_EOL . $bkstr;
            $bkpath = date('Ymd');
            $bkname = date('Y-m-d_H-i-s') . '_' . md5(Catfish::getRandom() . ' ' . time() . ' ' . rand());
            $bk = ROOT_PATH . 'data' . DS . 'dbbackup';
            Catfish::addIndex($bk, true);
            $bk = $bk . DS . $bkpath;
            Catfish::addIndex($bk, true);
            $sqlf = $bkname.'.yyb';
            file_put_contents($bk.DS.$sqlf, gzcompress($bkstr));
            $dbrec = Catfish::get('dbbackup');
            $recpath = $bkpath . '/' . $sqlf;
            if(empty($dbrec)){
                $dbrec = $recpath;
            }
            else{
                if(strpos($dbrec,$recpath) === false){
                    $dbrec .= ','.$recpath;
                }
            }
            Catfish::set('dbbackup', $dbrec);
        }
        Catfish::allot('dbbackup',$this->showdbbackup());
        return $this->show(Catfish::lang('Database backup'), 'xitong', 'dbbackup');
    }
    public function deldbbackup()
    {
        if(Catfish::isPost()){
            $fn = Catfish::getPost('fn');
            if(strpos($fn, '..') === false){
                $dbrec = ',' . Catfish::get('dbbackup');
                $dbrec = str_replace(',' . $fn, '', $dbrec);
                $dbrec = empty($dbrec) ? '' : substr($dbrec, 1);
                Catfish::set('dbbackup', $dbrec);
                $this->deletefile('data/dbbackup/' . $fn);
                echo 'ok';
            }
            else{
                echo Catfish::lang('Error');
            }
            exit();
        }
    }
    public function redbbackup()
    {
        if(Catfish::isPost()){
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            $file = ROOT_PATH . 'data' . DS . 'dbbackup' . DS . str_replace('/', DS, Catfish::getPost('fn'));
            echo $this->restoredb($file);
            exit();
        }
    }
    public function uploadrestore()
    {
        $this->checkUser();
        $prompt = '';
        if(Catfish::isPost()){
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            $file = request()->file('file');
            if($file->checkExt('yyb') === true){
                $rem = $this->restoredb($file->getPathname());
                if($rem == 'ok'){
                    $prompt = Catfish::lang('The database has been restored');
                }
                else{
                    $prompt = $rem;
                }
            }
            else{
                $prompt = Catfish::lang('Please select the correct backup file');
            }
        }
        Catfish::allot('dbbackup',$this->showdbbackup());
        Catfish::allot('dbprompt',$prompt);
        return $this->show(Catfish::lang('Database backup'), 'xitong', 'dbbackup', false, 'dbbackup');
    }
    public function smtpsettings()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            $data = $this->smtpsettingsPost();
            if(!is_array($data)){
                echo $data;
                exit();
            }
            else{
                $auth = Catfish::getPost('auth') == 'on' ? 1 : 0;
                $estis = serialize([
                    'host' => $data['host'],
                    'port' => $data['port'],
                    'user' => $data['user'],
                    'password' => $data['password'],
                    'secure' => Catfish::getPost('secure'),
                    'auth' => $auth
                ]);
                Catfish::set('emailsettings', $estis);
                echo 'ok';
                exit();
            }
        }
        $estis = Catfish::get('emailsettings');
        if($estis != false){
            $estis = unserialize($estis);
        }
        $ceshi = 1;
        if($estis == false){
            $estis = [
                'host' => '',
                'port' => 25,
                'user' => '',
                'password' => '',
                'secure' => 'tls',
                'auth' => true
            ];
            $ceshi = 0;
        }
        Catfish::allot('catfishItem', $estis);
        Catfish::allot('ceshi', $ceshi);
        return $this->show(Catfish::lang('SMTP settings'), 'xitong', 'smtpsettings', true);
    }
    public function csmail()
    {
        if(Catfish::isPost()){
            $this->postReady();
            $estis = unserialize(Catfish::get('emailsettings'));
            if(Catfish::sendmail($estis['user'], '', Catfish::lang('Test mail'), Catfish::lang('This is a test email'))){
                echo 'ok';
            }
            else{
                echo Catfish::lang('Test mail failed to send');
            }
            exit();
        }
    }
    public function systemupgrade()
    {
        $this->checkUser();
        $version = Catfish::getConfig('catfishCMS.version');
        $lastv = Catfish::version();
        Catfish::set('systemupgrade_currentversion', $version);
        if(version_compare($version, $lastv) >= 0){
            $needupgrade = 0;
        }
        else{
            $needupgrade = 1;
        }
        $sjbdz = Catfish::sjbdz();
        $au = isset($sjbdz['au']) ? $sjbdz['au'] : 0;
        $directly = 0;
        $directlystr = '';
        $address = [];
        if(isset($sjbdz['address'])){
            if(isset($sjbdz['address']['directly']) && !empty($sjbdz['address']['directly'])){
                $directlystr = $sjbdz['address']['directly'];
            }
            Catfish::set('systemupgrade_directly', $directlystr);
            if(isset($sjbdz['address']['manually']) && !empty($sjbdz['address']['manually'])){
                $tmp_addr = explode(',', $sjbdz['address']['manually']);
                foreach($tmp_addr as $val){
                    array_push($address, $val);
                }
            }
            if(isset($sjbdz['address']['official']) && !empty($sjbdz['address']['official'])){
                $tmp_addr = explode(',', $sjbdz['address']['official']);
                foreach($tmp_addr as $val){
                    array_push($address, $val);
                }
            }
        }
        if(!empty($directlystr) && $au == 1){
            $directly = 1;
        }
        Catfish::allot('needupgrade', $needupgrade);
        Catfish::allot('directly', $directly);
        Catfish::allot('address', $address);
        return $this->show(Catfish::lang('System Upgrade'), 'xitong', 'systemupgrade');
    }
    public function softwarelicense()
    {
        $this->checkUser();
        if(Catfish::isPost()){
            Catfish::set('serial', Catfish::getPost('authorization'));
            Catfish::removeCache('yuyuecmsprompt');
        }
        Catfish::allot('authorization',Catfish::get('serial'));
        return $this->show(Catfish::lang('Software license'), 'xitong', 'softwarelicense');
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
        return $this->show(Catfish::lang('My collection'), 'yonghu', 'mycollection');
    }
    public function mycollectiondel()
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
    public function prompt()
    {
        if(Catfish::isPost()){
            echo Catfish::rtmt();
            exit();
        }
    }
    public function version()
    {
        if(Catfish::isPost()){
            $dom = Catfish::get('domain');
            if(Catfish::isDomain($dom)){
                echo Catfish::version();
            }
            else{
                echo '';
            }
        }
        exit();
    }
    public function _empty()
    {
        $this->checkUser();
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        return Catfish::output('none');
    }
    public function uploadthumb()
    {
        if(Catfish::isPost()){
            $file = request()->file('file');
            $validate = [
                'ext' => 'jpg,png,gif,jpeg'
            ];
            $file->validate($validate);
            $info = $file->move(ROOT_PATH . 'data' . DS . 'uploads');
            if($info){
                $image = \think\Image::open(ROOT_PATH . 'data' . DS . 'uploads' . DS . $info->getSaveName());
                $width = $image->width();
                $height = $image->height();
                $larger = str_replace('.','_larger.',$info->getSaveName());
                @$image->thumb(850, ($height * 850 / $width),\think\Image::THUMB_FIXED)->save(ROOT_PATH . 'data' . DS . 'uploads' . DS . $larger);
                $small = str_replace('.','_small.',$info->getSaveName());
                @$image->thumb(470, ($height * 470 / $width),\think\Image::THUMB_FIXED)->save(ROOT_PATH . 'data' . DS . 'uploads' . DS . $small);
                @$image->thumb(350, 350)->save(ROOT_PATH . 'data' . DS . 'uploads' . DS . $info->getSaveName());
                echo 'data/uploads/'.str_replace('\\','/',$info->getSaveName());
            }else{
                echo $file->getError();
            }
        }
        exit();
    }
    public function uploadvideo()
    {
        if(Catfish::isPost()){
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            $file = request()->file('file');
            $validate = [
                'ext' => 'mp4,ogg,webm,flv,wav,avi,rmvb'
            ];
            $file->validate($validate);
            $info = $file->move(ROOT_PATH . 'data' . DS . 'video');
            if($info){
                echo 'data/video/'.str_replace('\\','/',$info->getSaveName());
            }else{
                echo $file->getError();
            }
        }
        exit();
    }
    public function uploadhandyeditor()
    {
        $file = request()->file('file');
        $validate = [
            'ext' => 'jpg,png,gif,jpeg'
        ];
        $file->validate($validate);
        $info = $file->move(ROOT_PATH . 'data' . DS . 'uploads');
        if($info){
            echo Catfish::domain().'data/uploads/'.str_replace('\\','/',$info->getSaveName());
        }else{
            echo $file->getError();
        }
    }
    public function uploadimage()
    {
        if(Catfish::isPost()){
            $file = request()->file('file');
            $validate = [
                'ext' => 'jpg,png,gif,jpeg'
            ];
            $file->validate($validate);
            $info = $file->move(ROOT_PATH . 'data' . DS . 'uploads');
            if($info){
                echo 'data/uploads/'.str_replace('\\','/',$info->getSaveName());
            }else{
                echo $file->getError();
            }
        }
        exit();
    }
    public function uploadfile()
    {
        if(Catfish::isPost()){
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            $file = request()->file('file');
            $validate = [
                'ext' => 'doc,docx,xls,xlsx,ppt,htm,html,txt,zip,rar,gz,bz2,pdf,apk,swf,7z'
            ];
            $file->validate($validate);
            $info = $file->move(ROOT_PATH . 'data' . DS . 'files');
            if($info){
                echo 'data/files/'.str_replace('\\','/',$info->getSaveName());
            }else{
                echo $file->getError();
            }
        }
        exit();
    }
    public function uploadIco()
    {
        if(Catfish::isPost()){
            $file = request()->file('file');
            $validate = [
                'ext' => 'ico'
            ];
            $file->validate($validate);
            $info = $file->move(ROOT_PATH . 'data' . DS . 'uploads');
            if($info){
                echo 'data/uploads/'.str_replace('\\','/',$info->getSaveName());
            }else{
                echo $file->getError();
            }
        }
        exit();
    }
    public function uploadslideshow()
    {
        if(Catfish::isPost()){
            $file = request()->file('file');
            $validate = [
                'ext' => 'jpg,png,gif,jpeg'
            ];
            $file->validate($validate);
            $info = $file->move(ROOT_PATH . 'data' . DS . 'uploads');
            if($info){
                $image = \think\Image::open(ROOT_PATH . 'data' . DS . 'uploads' . DS . $info->getSaveName());
                $width = intval(Catfish::getPost('width'));
                $height = intval(Catfish::getPost('height'));
                @$image->thumb($width, $height, \think\Image::THUMB_FIXED)->save(ROOT_PATH . 'data' . DS . 'uploads' . DS . $info->getSaveName());
                $upd = Catfish::getPost('upd');
                if(!empty($upd)){
                    $this->deleteResource('', $upd);
                }
                echo 'data/uploads/'.str_replace('\\','/',$info->getSaveName());
            }else{
                echo $file->getError();
            }
        }
        exit();
    }
    public function uploadlinkimage()
    {
        if(Catfish::isPost()){
            $file = request()->file('file');
            $validate = [
                'ext' => 'jpg,png,gif,jpeg'
            ];
            $file->validate($validate);
            $info = $file->move(ROOT_PATH . 'data' . DS . 'uploads');
            if($info){
                $upd = Catfish::getPost('upd');
                if(!empty($upd)){
                    $this->deleteResource('', $upd);
                }
                echo 'data/uploads/'.str_replace('\\','/',$info->getSaveName());
            }else{
                echo $file->getError();
            }
        }
        exit();
    }
    public function delfile()
    {
        if(Catfish::isPost()){
            if($this->deletefile(Catfish::getPost('delfile'))){
                echo 'ok';
            }
            else{
                echo Catfish::lang('Failed to delete');
            }
        }
        exit();
    }
    public function delthumb()
    {
        if(Catfish::isPost()){
            $this->deletethumb(Catfish::getPost('slt'));
            echo 'ok';
        }
        exit();
    }
    public function remotepackage()
    {
        if(Catfish::isPost()){
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            $directly = Catfish::get('systemupgrade_directly');
            $directlyarr = explode(',', $directly);
            if(count($directlyarr) > 1){
                $key = rand(0, count($directlyarr) - 1);
                $directly = $directlyarr[$key];
            }
            $path = ROOT_PATH . 'data' . DS . 'package';
            if(!is_dir($path)){
                mkdir($path, 0777, true);
            }
            $file = $path . DS . 'yuyuecms.zip';
            Catfish::set('upgradepackagefilename', 'yuyuecms.zip');
            Catfish::getFile($directly, $file);
            echo 'ok';
            exit();
        }
        else{
            echo Catfish::lang('Your operation is illegal');
            exit();
        }
    }
    public function upgradepackage()
    {
        if(Catfish::isPost()){
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            $file = request()->file('file');
            $validate = [
                'ext' => 'zip'
            ];
            $info = $file->validate($validate)->move(ROOT_PATH . 'data' . DS . 'package', false);
            if($info){
                Catfish::set('upgradepackagefilename', $info->getSaveName());
                echo 'ok';
            }else{
                echo $file->getError();
            }
            exit();
        }
        else{
            echo Catfish::lang('Your operation is illegal');
            exit();
        }
    }
    public function upgrading()
    {
        if(Catfish::isPost(1)){
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            $tempdir = ROOT_PATH . 'data' . DS . 'temp';
            $auto = Catfish::getPost('auto');
            if($auto == 1){
                $tempfolder = $tempdir . DS . 'autoupgrade';
            }
            else{
                $tempfolder = $tempdir . DS . 'upgrade';
            }
            if(!is_dir($tempfolder)){
                mkdir($tempfolder, 0777, true);
            }
            $upgradingfile = ROOT_PATH . 'data' . DS . 'package' . DS . Catfish::get('upgradepackagefilename');
            if(is_file($upgradingfile)){
                if(function_exists('disk_free_space')){
                    $needspace = filesize($upgradingfile) * 5;
                    if($needspace > disk_free_space($tempfolder)){
                        echo Catfish::lang('Not enough space');
                        exit();
                    }
                }
                Catfish::clearCache();
                try{
                    $zip = new \ZipArchive();
                    if($zip->open($upgradingfile) === true){
                        $zip->extractTo($tempfolder);
                        $zip->close();
                        $this->upgradFile($tempfolder);
                        @unlink($upgradingfile);
                        $this->delFolder($tempfolder);
                        $this->upgradedb();
                        Catfish::curl(Catfish::domain());
                        echo 'ok';
                    }
                    else{
                        echo Catfish::lang('Upgrade package is not available');
                    }
                }
                catch(\Exception $e){
                    echo Catfish::lang('Upgrade unsuccessful');
                }
            }
            else{
                echo Catfish::lang('Upgrade package not found');
            }
            exit();
        }
        else{
            echo Catfish::lang('Your operation is illegal');
            exit();
        }
    }
    private function upgradedb()
    {
        $upgradedbfile = ROOT_PATH . 'yuyuecms' . DS . 'install' . DS . 'upgrade';
        $sqlfiles = glob($upgradedbfile . DS . '*.sql');
        if(count($sqlfiles) > 0){
            $currentversion = Catfish::get('systemupgrade_currentversion');
            foreach($sqlfiles as $file){
                $ver = basename($file, '.sql');
                if(version_compare($ver, $currentversion) > 0){
                    $sql = Catfish::fgc($file);
                    $sql = str_replace([" `catfish_", " `yuyuecms_", " `yuyue_"], " `" . Catfish::prefix(), $sql);
                    $sql = str_replace("\r", "\n", $sql);
                    $sqlarr = explode(";\n", $sql);
                    foreach ($sqlarr as $item) {
                        $item = trim($item);
                        if(empty($item)) continue;
                        try{
                            Catfish::dbExecute($item);
                        }
                        catch(\Exception $e){
                            continue;
                        }
                    }
                }
                @unlink($file);
            }
        }
    }
    private function upgradFile($folder)
    {
        $cfolder = 1;
        while($cfolder == 1){
            $farr = glob($folder . DS . '*', GLOB_ONLYDIR);
            $cfolder = count($farr);
            if($cfolder == 1){
                $folder = $farr[0];
            }
            else{
                break;
            }
        }
        $this->recurseCopy($folder, ROOT_PATH);
    }
    public function uploadcertificate()
    {
        if(Catfish::isPost()){
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            $file = request()->file('file');
            $validate = [
                'ext' => 'crt'
            ];
            $file->validate($validate);
            $info = $file->move(ROOT_PATH . 'data' . DS . 'crt', false);
            if($info){
                echo 'data/crt/'.str_replace('\\','/',$info->getSaveName());
            }else{
                echo $file->getError();
            }
        }
        exit();
    }
}
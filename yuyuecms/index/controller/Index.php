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
class Index extends CatfishCMS
{
    public function index()
    {
        $this->readydisplay();
        Catfish::allot('daohang', [
            [
                'label' => Catfish::lang('Home'),
                'href' => Catfish::url('index/Index/index'),
                'icon' => '',
                'active' => 0
            ]
        ]);
        Catfish::allot('biaoti','');
        $this->shouye();
        $template = $this->template('index');
        $htmls = $this->show($template);
        return $htmls;
    }
    public function newslist($find = 0)
    {
        $this->readydisplay();
        Catfish::allot('daohang', [
            [
                'label' => Catfish::lang('Home'),
                'href' => Catfish::url('index/Index/index'),
                'icon' => '',
                'active' => 0
            ],
            [
                'label' => Catfish::lang('News list'),
                'href' => Catfish::url('index/Index/newslist'),
                'icon' => '',
                'active' => 0
            ]
        ]);
        $template = $this->template('newslist', $this->xinwenliebiao($find));
        $htmls = $this->show($template,'newslist',$find);
        return $htmls;
    }
    public function productlist($find = 0)
    {
        $this->readydisplay();
        Catfish::allot('daohang', [
            [
                'label' => Catfish::lang('Home'),
                'href' => Catfish::url('index/Index/index'),
                'icon' => '',
                'active' => 0
            ],
            [
                'label' => Catfish::lang('Product list'),
                'href' => Catfish::url('index/Index/productlist'),
                'icon' => '',
                'active' => 0
            ]
        ]);
        $template = $this->template('productlist', $this->chanpinliebiao($find));
        $htmls = $this->show($template,'productlist',$find);
        return $htmls;
    }
    public function search($find = '')
    {
        $this->readydisplay();
        Catfish::allot('daohang', [
            [
                'label' => Catfish::lang('Home'),
                'href' => Catfish::url('index/Index/index'),
                'icon' => '',
                'active' => 0
            ],
            [
                'label' => Catfish::lang('Search for'),
                'href' => Catfish::url('index/Index/search').'?find='.Catfish::getGet('find'),
                'icon' => '',
                'active' => 0
            ]
        ]);
        Catfish::allot('biaoti',Catfish::lang('Search for'));
        $template = $this->template('search', $this->sousuo($find));
        $htmls = $this->show($template,'search',$find);
        return $htmls;
    }
    public function word($find = '')
    {
        $this->readydisplay();
        Catfish::allot('daohang', [
            [
                'label' => Catfish::lang('Home'),
                'href' => Catfish::url('index/Index/index'),
                'icon' => '',
                'active' => 0
            ],
            [
                'label' => Catfish::lang('List'),
                'href' => Catfish::url('index/Index/word',['find'=>urlencode($find)]),
                'icon' => '',
                'active' => 0
            ]
        ]);
        Catfish::allot('biaoti',Catfish::lang('List'));
        $template = $this->template('search', $this->guanjianzi($find));
        $htmls = $this->show($template,'search',$find);
        return $htmls;
    }
    public function news($find = 0)
    {
        if(Catfish::getPost('pinglun') !== false){
            $this->comment('news');
        }
        if(Catfish::getPost('zan') == 1){
            $this->zan('news');
        }
        if(Catfish::getPost('shoucang') == 1){
            $this->shoucang('news');
        }
        $this->readydisplay();
        $id = 0;
        $link = [];
        $template = $this->template('news', $this->xinwen($find, $link, $id));
        $htmls = $this->show($template,'news',$find,$this->getCate('news',$id),$link,Catfish::lang('News list'),'newslist');
        return $htmls;
    }
    public function product($find = 0)
    {
        if(Catfish::getPost('pinglun') !== false){
            $this->comment('product');
        }
        if(Catfish::getPost('zan') == 1){
            $this->zan('product');
        }
        if(Catfish::getPost('shoucang') == 1){
            $this->shoucang('product');
        }
        $this->readydisplay();
        $id = 0;
        $link = [];
        $template = $this->template('product', $this->chanpin($find, $link, $id));
        $htmls = $this->show($template,'product',$find,$this->getCate('product',$id),$link,Catfish::lang('Product list'),'productlist');
        return $htmls;
    }
    public function page($find = 0)
    {
        if(Catfish::getPost('pinglun') !== false){
            $this->comment('page');
        }
        if(Catfish::getPost('zan') == 1){
            $this->zan('page');
        }
        if(Catfish::getPost('shoucang') == 1){
            $this->shoucang('page');
        }
        $this->readydisplay();
        $link = [];
        $template = $this->template('page', $this->danye($find, $link));
        $htmls = $this->show($template,'page',$find,null,$link);
        return $htmls;
    }
    public function liuyan()
    {
        if(Catfish::isPost(false)){
            $this->message();
            Catfish::success(Catfish::lang('Message success'));
            return true;
        }
    }
}
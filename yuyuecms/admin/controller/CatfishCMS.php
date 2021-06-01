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
        elseif(!in_array(Catfish::getSession('user_type'), Catfish::department('admin'))){
            Catfish::redirect('user/Index/index');
            exit();
        }
        elseif(!Catfish::checkUser()){
            Catfish::redirect('login/Index/quit');
            exit();
        }
        $this->options();
        Catfish::setConfig('paginate.type', 'bootstrap');
    }
    protected function show($title, $backstageMenu, $option = '', $star = false, $template = null)
    {
        Catfish::allot('tuichu', Catfish::url('login/Index/quit'));
        Catfish::allot('user', Catfish::getSession('user'));
        Catfish::allot('backstagetitle', $title);
        Catfish::allot('backstageMenu', $backstageMenu);
        Catfish::allot('option', $option);
        Catfish::allot('star', $star);
        return Catfish::out($template);
    }
    protected function categoriesnewsPost()
    {
        $rule = [
            'fenleim' => 'require',
            'shangji' => 'require',
            'alias' => 'alphaDash|regex:[A-Za-z\d_\-]*[A-Za-z]+[A-Za-z\d_\-]*'
        ];
        $msg = [
            'fenleim.require' => Catfish::lang('The category name must be filled in'),
            'shangji.require' => Catfish::lang('The superior category must be selected'),
            'alias.alphaDash' => Catfish::lang('Aliases can only be composed of letters, numbers and underscores or connecting lines'),
            'alias.regex' => Catfish::lang('The alias contains at least one letter')
        ];
        $data = [
            'fenleim' => Catfish::getPost('fenleim'),
            'shangji' => Catfish::getPost('shangji'),
            'alias' => Catfish::getPost('alias')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function writenewsPost()
    {
        $rule = [
            'biaoti' => 'require',
            'zhengwen' => 'require',
            'alias' => 'alphaDash|regex:[A-Za-z\d_\-]*[A-Za-z]+[A-Za-z\d_\-]*'
        ];
        $msg = [
            'biaoti.require' => Catfish::lang('The title must be filled in'),
            'zhengwen.require' => Catfish::lang('Article content must be filled out'),
            'alias.alphaDash' => Catfish::lang('Aliases can only be composed of letters, numbers and underscores or connecting lines'),
            'alias.regex' => Catfish::lang('The alias contains at least one letter')
        ];
        $data = [
            'biaoti' => Catfish::getPost('biaoti',false),
            'zhengwen' => trim(strip_tags(Catfish::getPost('zhengwen'))),
            'alias' => Catfish::getPost('alias')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function deletefile($delfile)
    {
        if(Catfish::isDataPath($delfile)){
            if(@unlink(ROOT_PATH . str_replace('/', DS, $delfile))){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    protected function deletethumb($slt)
    {
        $domain = Catfish::domain();
        if(strpos($slt,$domain) !== false){
            $slt = str_replace($domain, '', $slt);
            if(Catfish::isDataPath($slt)){
                $mslt = str_replace('.','_small.',$slt);
                @unlink(ROOT_PATH . $mslt);
                $lslt = str_replace('.','_larger.',$slt);
                @unlink(ROOT_PATH . $lslt);
                @unlink(ROOT_PATH . $slt);
            }
        }
    }
    protected function deleteResource($suolvetu = '', $shipin = '', $zutu = '', $wenjianzu = '', $tu = '')
    {
        if(!empty($suolvetu)){
            $this->deletethumb($suolvetu);
        }
        if(!empty($shipin)){
            $this->deletefile($shipin);
        }
        if(!empty($zutu)){
            $arr = explode(',', $zutu);
            foreach($arr as $val){
                $this->deletefile($val);
            }
        }
        if(!empty($wenjianzu)){
            $arr = explode(',', $wenjianzu);
            foreach($arr as $val){
                $this->deletefile($val);
            }
        }
        if(!empty($tu)){
            $this->deletefile($tu);
        }
    }
    private function options()
    {
        Catfish::chkl();
        if(method_exists($this,'assign')){
            Catfish::redirect('admin/Index/error');
            exit();
        }
        class_exists('catfishcms\Catfish',false)?:die();
        $data_options = Catfish::autoload();
        $dom = '';
        foreach($data_options as $key => $val)
        {
            if($val['name'] == 'copyright' || $val['name'] == 'statistics')
            {
                Catfish::allot($val['name'], unserialize($val['value']));
            }
            elseif($val['name'] == 'domain'){
                Catfish::allot($val['name'], $val['value']);
                $dom = $val['value'];
                $root = $val['value'];
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
            else
            {
                Catfish::allot($val['name'], $val['value']);
            }
        }
        Catfish::allot('/','/'.implode('a',unserialize(Catfish::get('randomarr'))));
        Catfish::allot('//',Catfish::rtmt()?Catfish::bd(implode('a',Catfish::rid())):'');
        $yuyuecmsdiffer = Catfish::getCache('yuyuecmsdiffer');
        if($yuyuecmsdiffer == false){
            $yuyuecmsdiffer = strtotime(Catfish::get('creationtime'));
            Catfish::setCache('yuyuecmsdiffer',$yuyuecmsdiffer,36000);
        }
        $differ = 0;
        if(time() - $yuyuecmsdiffer > 31536000 && Catfish::isDomain($dom)){
            $differ = 1;
        }
        Catfish::allot('prompt', $differ);
        Catfish::allot('openpay', Catfish::get('openpay'));
        $pluginsOpened = Catfish::get('plugins_opened');
        $pluginItem = [];
        if(!empty($pluginsOpened)){
            $pluginsOpened = unserialize($pluginsOpened);
            $lang = Catfish::detectLang();
            foreach($pluginsOpened as $key => $val){
                $langPath = ROOT_PATH.'plugins' . DS . $val . DS . 'lang' . DS . $lang .'.php';
                if(is_file($langPath)){
                    Catfish::loadLang($langPath);
                }
                $params = [
                    'pluginName' => $val
                ];
                $this->openCloseHook($val, 'addAdminPlugin', $params);
                if(isset($params['item'])){
                    $this->getext($params['item'], $pluginItem);
                }
            }
        }
        $uftheme = ucfirst($this->template);
        if(is_file(ROOT_PATH.'public' . DS . 'theme' . DS . $this->template . DS . $uftheme .'.php')){
            $lang = Catfish::detectLang();
            $langPath = ROOT_PATH.'public' . DS . 'theme' . DS . $this->template . DS . 'lang' . DS . $lang .'.php';
            if(is_file($langPath)){
                Catfish::loadLang($langPath);
            }
            $params = [
                'pluginName' => ''
            ];
            $this->themeHook('addAdminPlugin', $params, $this->template);
            if(isset($params['item'])){
                $this->getext($params['item'], $pluginItem, $this->template);
            }
        }
        $hasPlugin = count($pluginItem);
        Catfish::allot('hasPlugin', $hasPlugin);
        Catfish::allot('pluginItem', $pluginItem);
        Catfish::allot('numberOfPlugins', count($pluginItem,COUNT_NORMAL));
        Catfish::allot('verification', Catfish::verifyCode());
    }
    protected function order($table)
    {
        if(Catfish::getPost('paixu') == 'paixu'){
            $paixu = Catfish::getPost();
            foreach((array)$paixu as $key => $val)
            {
                if(is_numeric($key))
                {
                    Catfish::db($table)
                        ->where('id', $key)
                        ->update(['listorder' => intval($val)]);
                }
            }
        }
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
    protected function newslabelconfPost()
    {
        $rule = [
            'biaoqian' => 'require|alphaNum',
            'quantity' => 'gt:0'
        ];
        $msg = [
            'biaoqian.require' => Catfish::lang('Label name must be filled in'),
            'biaoqian.alphaNum' => Catfish::lang('Label names can only use letters and numbers'),
            'quantity.gt' => Catfish::lang('The display quantity must be an integer greater than zero')
        ];
        $data = [
            'biaoqian' => trim(Catfish::getPost('biaoqian')),
            'quantity' => Catfish::getPost('quantity')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function attributetemplatePost()
    {
        $rule = [
            'protemp' => 'require',
            'propname' => 'require'
        ];
        $msg = [
            'protemp.require' => Catfish::lang('Attribute template name must be filled in'),
            'propname.require' => Catfish::lang('Attribute must be added')
        ];
        $data = [
            'protemp' => trim(Catfish::getPost('protemp')),
            'propname' => Catfish::getPost('propname')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function editproductPost()
    {
        $rule = [
            'biaoti' => 'require',
            'zhengwen' => 'require',
            'alias' => 'alphaDash|regex:[A-Za-z\d_\-]*[A-Za-z]+[A-Za-z\d_\-]*',
            'yuanjia' => 'number',
            'xianjia' => 'number'
        ];
        $msg = [
            'biaoti.require' => Catfish::lang('Product title must be filled in'),
            'zhengwen.require' => Catfish::lang('Product details must be filled in'),
            'alias.alphaDash' => Catfish::lang('Aliases can only be composed of letters, numbers and underscores or connecting lines'),
            'alias.regex' => Catfish::lang('The alias contains at least one letter'),
            'yuanjia.number' => Catfish::lang('Original price must be a number'),
            'xianjia.number' => Catfish::lang('Current price must be a number')
        ];
        $data = [
            'biaoti' => Catfish::getPost('biaoti',false),
            'zhengwen' => trim(strip_tags(Catfish::getPost('zhengwen'))),
            'alias' => Catfish::getPost('alias'),
            'yuanjia' => Catfish::getPost('yuanjia'),
            'xianjia' => Catfish::getPost('xianjia')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function slidegroupingPost()
    {
        $rule = [
            'catename' => 'require',
            'width' => 'require|integer',
            'height' => 'require|integer'
        ];
        $msg = [
            'catename.require' => Catfish::lang('Slide group name must be filled in'),
            'width.require' => Catfish::lang('Width must be filled in'),
            'height.require' => Catfish::lang('Height must be filled in'),
            'width.integer' => Catfish::lang('Width must be an integer'),
            'height.integer' => Catfish::lang('Height must be an integer')
        ];
        $data = [
            'catename' => Catfish::getPost('catename'),
            'width' => Catfish::getPost('width'),
            'height' => Catfish::getPost('height')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    public function addslideshowPost()
    {
        $rule = [
            'slidegrouping' => 'require',
            'slideshow' => 'require'
        ];
        $msg = [
            'slidegrouping.require' => Catfish::lang('Slide grouping must be selected'),
            'slideshow.require' => Catfish::lang('Slideshow image must be uploaded')
        ];
        $data = [
            'slidegrouping' => Catfish::getPost('slidegrouping'),
            'slideshow' => Catfish::getPost('slideshow')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    public function addlinksPost()
    {
        $rule = [
            'mingcheng' => 'require',
            'dizhi' => 'require'
        ];
        $msg = [
            'mingcheng.require' => Catfish::lang('Friendly link name must be filled in'),
            'dizhi.require' => Catfish::lang('Friendly link address must be filled in')
        ];
        $data = [
            'mingcheng' => Catfish::getPost('mingcheng'),
            'dizhi' => Catfish::getPost('dizhi')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function corporatehistoryPost()
    {
        $rule = [
            'biaoti' => 'require'
        ];
        $msg = [
            'biaoti.require' => Catfish::lang('The title must be filled in')
        ];
        $data = [
            'biaoti' => Catfish::getPost('biaoti')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function menucategoryPost()
    {
        $rule = [
            'fenleiming' => 'require'
        ];
        $msg = [
            'fenleiming.require' => Catfish::lang('Menu category name must be filled in')
        ];
        $data = [
            'fenleiming' => Catfish::getPost('fenleiming')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function addmenuPost()
    {
        $rule = [
            'caidanfenlei' => 'require',
            'caidanming' => 'require'
        ];
        $msg = [
            'caidanfenlei.require' => Catfish::lang('Menu category must be selected'),
            'caidanming.require' => Catfish::lang('Menu name must be filled in')
        ];
        $data = [
            'caidanfenlei' => Catfish::getPost('caidanfenlei'),
            'caidanming' => Catfish::getPost('caidanming')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function personalPost()
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
    protected function changePost()
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
    protected function aliaschk($table)
    {
        $id = Catfish::getPost('id');
        if(empty($id)){
            $alias = Catfish::db($table)->where('alias',Catfish::getPost('alias'))->find();
        }
        else{
            $alias = Catfish::db($table)->where('id','<>',$id)->where('alias',Catfish::getPost('alias'))->find();
        }
        if(!empty($alias)){
            return Catfish::lang('The alias already exists, please change one');
        }
        else{
            return 'ok';
        }
    }
    protected function selflabelingPost()
    {
        $rule = [
            'biaoqian' => 'require|alphaNum'
        ];
        $msg = [
            'biaoqian.require' => Catfish::lang('Label name must be filled in'),
            'biaoqian.alphaNum' => Catfish::lang('Label names can only use letters and numbers')
        ];
        $data = [
            'biaoqian' => trim(Catfish::getPost('biaoqian'))
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function strint($si)
    {
        if($si === null){
            return 'NULL';
        }
        elseif(is_int($si)){
            return intval($si);
        }
        else{
            return '\''.str_replace('\'','\'\'',$si).'\'';
        }
    }
    protected function restoredb($file)
    {
        if(is_file($file)){
            $dbrec = Catfish::get('dbbackup');
            $dbnm = Catfish::getConfig('database.database');
            $dbPrefix = Catfish::getConfig('database.prefix');
            $sql = "SHOW TABLES FROM {$dbnm} LIKE '{$dbPrefix}%'";
            $renm = Catfish::dbExecute($sql);
            foreach($renm as $nmval){
                reset($nmval);
                $tbnm = current($nmval);
                $sql = 'TRUNCATE TABLE `'.$tbnm.'`';
                Catfish::dbExecute($sql);
            }
            $bkf = gzuncompress(file_get_contents($file));
            $bkarr = explode('--CATFISH\'CMS->YUYUE\'CMS',$bkf);
            $zstr = '';
            $fstin = stripos($bkarr[0], 'INSERT INTO');
            if($fstin === false){
                $zstr = array_shift($bkarr);
            }
            else{
                $zstr = substr($bkarr[0], 0, $fstin);
                $bkarr[0] = trim(substr($bkarr[0], $fstin));
            }
            $zarr = explode(PHP_EOL, $zstr);
            $prefix = '';
            foreach($zarr as $key => $val){
                $ppos = stripos($val, 'Table prefix:');
                if($ppos !== false){
                    $ppos = $ppos + strlen('Table prefix:');
                    $prefix = trim(substr($val, $ppos));
                    break;
                }
            }
            foreach($bkarr as $q){
                $q = trim($q);
                if(!empty($prefix)){
                    $inlen = strlen('INSERT INTO `') + strlen($prefix);
                    $q = 'INSERT INTO `' . $dbPrefix . substr($q, $inlen);
                }
                Catfish::dbExecute($q);
            }
            Catfish::set('dbbackup', $dbrec);
            return 'ok';
        }
        else{
            return Catfish::lang('Backup file has expired');
        }
    }
    protected function showdbbackup()
    {
        $dbrec = Catfish::get('dbbackup');
        if(!empty($dbrec)){
            $dbrecarr = explode(',', $dbrec);
            $dbrecarr = array_reverse($dbrecarr);
        }
        else{
            $dbrecarr = [];
        }
        foreach($dbrecarr as $key => $val){
            $onlbnm = basename($val, '.yyb');
            $onlbnmarr = explode('_', $onlbnm);
            $onlbnmarr[1] = str_replace('-', ': ', $onlbnmarr[1]);
            $bdate = $onlbnmarr[0] . ' ' . $onlbnmarr[1];
            $dbrecarr[$key] = [
                'path' => $val,
                'name' => 'yuyuecms'.str_replace(['-', '_', ':', ' '], '', $bdate . '.yyb'),
                'date' => $bdate,
                'down' => Catfish::domain() . 'data/dbbackup/' . $val
            ];
        }
        return $dbrecarr;
    }
    protected function semiinsert($table, $field, &$value, &$bkstr)
    {
        $restr = 'INSERT INTO `'.$table.'` ('.$field.') VALUES'.$value.';'.PHP_EOL;
        $bkstr .= '--CATFISH\'CMS->YUYUE\'CMS'.PHP_EOL.$restr;
    }
    protected function smtpsettingsPost()
    {
        $rule = [
            'host' => 'require',
            'port' => 'require',
            'user' => 'require',
            'password' => 'require',
        ];
        $msg = [
            'host.require' => Catfish::lang('SMTP server address must be filled in'),
            'port.require' => Catfish::lang('Port number must be filled in'),
            'user.require' => Catfish::lang('Mailbox users must fill in'),
            'password.require' => Catfish::lang('Password must be filled in')
        ];
        $data = [
            'host' => Catfish::getPost('host'),
            'port' => Catfish::getPost('port'),
            'user' => Catfish::getPost('user'),
            'password' => Catfish::getPost('password')
        ];
        return $this->validatePost($rule, $msg, $data);
    }
    protected function postReady()
    {
        Catfish::loadLang(APP_PATH.'common/lang/'.Catfish::detectLang().'.php');
    }
    protected function recurseCopy($src,$dst){
        $dir=opendir($src);
        @mkdir($dst);
        while(false!==($file=readdir($dir))){
            if(($file!='.' )&&($file!='..')){
                if(is_dir($src.'/'.$file)){
                    $this->recurseCopy($src.'/'.$file,$dst.'/'.$file);
                }
                else{
                    copy($src.'/'.$file,$dst.'/'.$file);
                }
            }
        }
        closedir($dir);
    }
    protected function delFolder($folder)
    {
        if(is_dir($folder)){
            $fd = scandir($folder);
            foreach($fd as $val){
                if($val != '.' && $val != '..'){
                    $tmp = $folder.DS.$val;
                    if(is_dir($tmp)){
                        $this->delFolder($tmp);
                        @rmdir($tmp);
                    }
                    else{
                        @unlink($tmp);
                    }
                }
            }
        }
        else{
            @unlink($folder);
        }
    }
    protected function alipayPost()
    {
        $rule = [
            'appid' => 'require',
            'merchantuid' => 'require',
            'privatekey' => 'require',
            'qianming' => 'require'
        ];
        $msg = [
            'appid.require' => Catfish::lang('AppId must be filled in'),
            'merchantuid.require' => Catfish::lang('Merchant UID must be filled in'),
            'privatekey.require' => Catfish::lang('Application private key must be filled in'),
            'qianming.require' => Catfish::lang('Signature method must be selected')
        ];
        $data = [
            'appid' => Catfish::getPost('appid'),
            'merchantuid' => Catfish::getPost('merchantuid'),
            'privatekey' => Catfish::getPost('privatekey', false),
            'qianming' => Catfish::getPost('qianming')
        ];
        return $this->validatePost($rule, $msg, $data);
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
    protected function hasThemeSetting($theme)
    {
        $uftheme = ucfirst($theme);
        $themeFile = ROOT_PATH.'public' . DS . 'theme' . DS . $theme . DS . $uftheme .'.php';
        if(is_file($themeFile)){
            $themeContent = file_get_contents($themeFile);
            if(preg_match("/public\s+function\s+themeSetting\s*\(/i", $themeContent)){
                return true;
            }
        }
        return false;
    }
    protected function openClosePlugin($pluginName, $isopen = true)
    {
        $pluginsOpened = Catfish::get('plugins_opened');
        if(empty($pluginsOpened)){
            $pluginsOpened = [];
        }
        else{
            $pluginsOpened = unserialize($pluginsOpened);
        }
        if($isopen){
            $pluginFile = ROOT_PATH.'plugins'.DS.$pluginName.DS.ucfirst($pluginName).'.php';
            if(!in_array($pluginName, $pluginsOpened) && is_file($pluginFile)){
                $pluginsOpened[] = $pluginName;
            }
            $params = [
                'pluginName' => $pluginName
            ];
            $this->openCloseHook($pluginName, 'openPlugin', $params);
        }
        else{
            foreach($pluginsOpened as $key => $val){
                if($val == $pluginName){
                    unset($pluginsOpened[$key]);
                    $params = [
                        'pluginName' => $pluginName
                    ];
                    $this->openCloseHook($pluginName, 'closePlugin', $params);
                }
            }
        }
        Catfish::set('plugins_opened', serialize($pluginsOpened));
    }
    protected function openCloseHook($pluginName, $hook, &$params = [])
    {
        $ufpluginName = ucfirst($pluginName);
        $pluginPath = ROOT_PATH.'plugins' . DS . $pluginName . DS . $ufpluginName .'.php';
        if(is_file($pluginPath)){
            return Catfish::execHook('plugin\\' . $pluginName . '\\' . $ufpluginName, $hook, $params);
        }
        return false;
    }
    protected function deleteFolder($folder)
    {
        $this->delFolder($folder);
        if(is_dir($folder)){
            @rmdir($folder);
        }
    }
    protected function movePlugin($folderPath)
    {
        $folder = $folderPath;
        $hasphp = glob($folder . DS . '*.php');
        while(!is_array($hasphp) || count($hasphp) < 1){
            $farr = glob($folder . DS . '*', GLOB_ONLYDIR);
            if(is_array($farr) && count($farr) > 0){
                $folder = $farr[0];
                $hasphp = glob($folder . DS . '*.php');
            }
            else{
                break;
            }
        }
        $pluginName = basename($folder);
        $pluginDir = ROOT_PATH . DS . 'plugins';
        $pluginPath = $pluginDir . DS . $pluginName;
        if(!is_dir($pluginPath)){
            mkdir($pluginPath, 0777, true);
            $this->recurseCopy($folder, $pluginPath);
            if(!is_file($pluginDir . DS . 'index.html')){
                Catfish::addIndex($pluginDir);
            }
            return true;
        }
        else{
            return false;
        }
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
    protected function plantHook($hook, &$params = [])
    {
        $execArr = [];
        $pluginsOpened = Catfish::get('plugins_opened');
        if(!empty($pluginsOpened)){
            $pluginsOpened = unserialize($pluginsOpened);
            foreach($pluginsOpened as $key => $val){
                $ufval = ucfirst($val);
                $execArr[] = 'plugin\\' . $val . '\\' . $ufval;
            }
        }
        $uftheme = ucfirst($this->template);
        if(is_file(ROOT_PATH.'public' . DS . 'theme' . DS . $this->template . DS . $uftheme .'.php')){
            $execArr[] = 'theme\\' . $this->template . '\\' . $uftheme;
        }
        if(count($execArr) > 0){
            Catfish::addHook($hook, $execArr);
            return Catfish::listen($hook, $params);
        }
        return false;
    }
    private function getext($itemArr, &$pluginItem, $theme = '_theme')
    {
        foreach($itemArr as $ikey => $ival){
            $ival['alias'] = Catfish::lang($ival['alias']);
            $ival['url'] = Catfish::url('admin/Index/plugin', ['name' => strtolower(preg_replace('/([A-Z])/', '-${1}', $ival['name'])), 'func' => strtolower(preg_replace('/([A-Z])/', '-${1}', $ival['function'])), 'plugin' => strtolower(preg_replace('/([A-Z])/', '-${1}', $ival['plugin'])), 'theme' => strtolower(preg_replace('/([A-Z])/', '-${1}', $theme)), 'alias' => urlencode($ival['alias'])]);
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
    protected function geticons()
    {
        $icons = glob(ROOT_PATH.'public/common/bootstrap-icons/*.svg');
        foreach($icons as $key => $val){
            $icon = basename($val);
            $name =substr($icon, 0, -4);
            $icons[$key] = [
                'name' => $name,
                'icon' => $icon
            ];
        }
        return $icons;
    }
    protected function moveTheme($folderPath)
    {
        $folder = $folderPath;
        $hasphp = glob($folder . DS . '*.html');
        while(!is_array($hasphp) || count($hasphp) < 1){
            $farr = glob($folder . DS . '*', GLOB_ONLYDIR);
            if(is_array($farr) && count($farr) > 0){
                $folder = $farr[0];
                $hasphp = glob($folder . DS . '*.html');
            }
            else{
                break;
            }
        }
        $themeName = basename($folder);
        $themeDir = ROOT_PATH . DS . 'public' . DS . 'theme';
        $themePath = $themeDir . DS . $themeName;
        if(!is_dir($themePath)){
            mkdir($themePath, 0777, true);
            $this->recurseCopy($folder, $themePath);
            return true;
        }
        else{
            return false;
        }
    }
}
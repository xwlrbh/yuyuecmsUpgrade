<?php
/**
 * Project: 鱼跃CMS - Enterprise cms developed by catfish cms.
 * Producer: catfish cms [ http://www.catfish-cms.com ]
 * Author: A.J <804644245@qq.com>
 * License: http://www.yuyue-cms.com/page/agreement.html
 * Copyright: http://www.yuyue-cms.com All rights reserved.
 */
namespace app\install\controller;

use catfishcms\Catfish;
use think\Controller;
use think\Validate;
use think\Request;
use think\Db;

class Index extends Controller
{
    private $lang;
    public function _initialize()
    {
        $this->lang = Catfish::detectLang();
        Catfish::loadLang(APP_PATH . 'install/lang/'.$this->lang.'.php');
    }
    public function index()
    {
        $this->check();
        $this->assign('CatfishCMS',Catfish::getConfig('catfishCMS'));
        $license = file_get_contents(APP_PATH . 'LICENSE.txt');
        $this->assign('license',$license);
        $this->domain();
        $view = $this->fetch();
        return $view;
    }
    public function step1()
    {
        $this->check();
        $right = '<span class="glyphicon glyphicon-ok text-success"></span> ';
        $wrong = '<span class="glyphicon glyphicon-remove text-danger"></span> ';
        $data=array();
        $data['phpversion'] = @ phpversion();
        $data['os']=PHP_OS;
        $err = 0;
        if (version_compare($data['phpversion'], '5.4.0', '>=')) {
            $data['phpversion'] = $right . $data['phpversion'];
        }
        else {
            $data['phpversion'] = $wrong . $data['phpversion'];
            $err++;
        }
        if (class_exists('pdo')) {
            $data['pdo'] = $right . Catfish::lang('Turned on');
        } else {
            $data['pdo'] = $wrong . Catfish::lang('Unopened');
            $err++;
        }
        if (extension_loaded('pdo_mysql')) {
            $data['pdo_mysql'] = $right . Catfish::lang('Turned on');
        } else {
            $data['pdo_mysql'] = $wrong . Catfish::lang('Unopened');
            $err++;
        }
        if (ini_get('file_uploads')) {
            $data['upload_size'] = $right . ini_get('upload_max_filesize');
        } else {
            $data['upload_size'] = $wrong . Catfish::lang('Upload is prohibited');
        }
        if (function_exists('curl_init')) {
            $data['curl'] = $right . Catfish::lang('Turned on');
        } else {
            $data['curl'] = $wrong . Catfish::lang('Unopened');
            $err++;
        }
        if (function_exists('gd_info')) {
            $data['gd'] = $right . Catfish::lang('Turned on');
        } else {
            $data['gd'] = $wrong . Catfish::lang('Unopened');
            $err++;
        }
        if (class_exists('ZipArchive')) {
            $data['ZipArchive'] = $right . Catfish::lang('Turned on');
        } else {
            $data['ZipArchive'] = $wrong . Catfish::lang('Unopened');
            $err++;
        }
        if (function_exists('session_start')) {
            $data['session'] = $right . Catfish::lang('Turned on');
        } else {
            $data['session'] = $wrong . Catfish::lang('Unopened');
            $err++;
        }
        $lujing = ltrim(str_replace('/index.php','',Catfish::oUrl('/')),'/');
        $folders = array(
            '',
            'data',
            'data/uploads',
            'catfishcms',
            'runtime',
            'runtime/cache',
            'runtime/log',
            'runtime/temp'
        );
        $new_folders=array();
        foreach($folders as $dir){
            $Testdir = "./".$dir;
            $this->createDir($Testdir);
            if($this->testWrite($Testdir)){
                $new_folders[$lujing.$dir]['w']=true;
            }else{
                $new_folders[$lujing.$dir]['w']=false;
                $err++;
            }
            if(is_readable($Testdir)){
                $new_folders[$lujing.$dir]['r']=true;
            }else{
                $new_folders[$lujing.$dir]['r']=false;
                $err++;
            }
        }
        $data['folders']=$new_folders;
        $this->assign('CatfishCMS',Catfish::getConfig('catfishCMS'));
        $this->assign('data',$data);
        $this->assign('error',$err);
        $this->domain();
        $view = $this->fetch();
        return $view;
    }
    private function createDir($path, $mode = 0777)
    {
        if(is_dir($path))
            return true;
        $path = str_replace('\\', '/', $path);
        if(substr($path, -1) != '/')
            $path = $path . '/';
        $temp = explode('/', $path);
        $cur_dir = '';
        $max = count($temp) - 1;
        for($i = 0; $i < $max; $i++)
        {
            $cur_dir .= $temp[$i] . '/';
            if (@is_dir($cur_dir))
                continue;
            @mkdir($cur_dir, 0777, true);
            @chmod($cur_dir, 0777);
        }
        return is_dir($path);
    }
    private function testWrite($d)
    {
        $tfile = "_test.txt";
        $fp = @fopen($d . "/" . $tfile, "w");
        if (!$fp) {
            return false;
        }
        fclose($fp);
        $rs = @unlink($d . "/" . $tfile);
        if ($rs) {
            return true;
        }
        return false;
    }
    public function step2()
    {
        $this->check();
        $this->assign('CatfishCMS',Catfish::getConfig('catfishCMS'));
        $this->domain();
        $view = $this->fetch();
        return $view;
    }
    public function step3()
    {
        $this->check();
        $rule = [
            'host' => 'require',
            'port' => 'require|number',
            'user' => 'require',
            'name' => 'require',
            'admin' => 'require',
            'pwd' => 'require|min:8',
            'repwd' => 'require',
            'email' => 'require|email'
        ];
        $msg = [
            'host.require' => Catfish::lang('The database server must be filled out'),
            'port.require' => Catfish::lang('The database port must be filled in'),
            'port.number' => Catfish::lang('The database port must be a number'),
            'user.require' => Catfish::lang('The database user name must be filled in'),
            'name.require' => Catfish::lang('The database name must be filled in'),
            'admin.require' => Catfish::lang('The administrator account must be filled in'),
            'pwd.require' => Catfish::lang('The administrator password is required'),
            'pwd.min' => Catfish::lang('The administrator password can not be less than 8 characters'),
            'repwd.require' => Catfish::lang('Confirm password is required'),
            'email.require' => Catfish::lang('Email is required'),
            'email.email' => Catfish::lang('Email format is incorrect')
        ];
        $data = [
            'host' => Catfish::getPost('host'),
            'port' => Catfish::getPost('port'),
            'user' => Catfish::getPost('user'),
            'name' => Catfish::getPost('name'),
            'admin' => Catfish::getPost('admin'),
            'pwd' => Catfish::getPost('pwd'),
            'repwd' => Catfish::getPost('repwd'),
            'email' => Catfish::getPost('email')
        ];
        $validate = new Validate($rule, $msg);
        if(!$validate->check($data))
        {
            $this->error($validate->getError());
        }
        elseif($data['pwd'] !== $data['repwd'])
        {
            $this->error(Catfish::lang('The "Administrator Password" and "Confirm Password" must be the same'));
        }
        else
        {
            try{
                $dbh=new \PDO('mysql:host='.$data['host'].';port='.$data['port'],$data['user'],Catfish::getPost('password'));
                $dbh->exec('CREATE DATABASE IF NOT EXISTS `' . $data['name'] . '` DEFAULT CHARACTER SET utf8');
            }catch(\Exception $e){
                $this->error(Catfish::lang('Database information error'));
                return false;
            }
            $this->assign('CatfishCMS',Catfish::getConfig('catfishCMS'));
            $domain = $this->domain();
            $sql = file_get_contents(APP_PATH . 'install/data/catfish.sql');
            $sql = str_replace("\r", "\n", $sql);
            $sql = explode(";\n", $sql);
            $default_tablepre = "catfish_";
            $sql = str_replace(" `{$default_tablepre}", " `" . Catfish::getPost('prefix'), $sql);
            foreach ($sql as $item) {
                $item = trim($item);
                if(empty($item)) continue;
                $this->dbExec($item);
            }
            $create_date=date("Y-m-d H:i:s");
            $rmd = md5($create_date);
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "users`
    (id,yonghu,password,nicheng,email,url,createtime,activation,randomcode,status,utype) VALUES
    (1, '" . Catfish::getPost('admin') . "', '" . md5(Catfish::getPost('pwd').$rmd) . "', '" . substr($rmd,0,6) . "', '" . Catfish::getPost('email') . "', '', '{$create_date}', '', '{$rmd}', 1, 'founder')");
            $qu = $this->dbExec('select * from '.Catfish::getPost('prefix').'users where id=1',true);
            if(empty($qu))
            {
                $this->error(Catfish::lang('Bad database name'));
            }
            $view = $this->fetch();
            echo $view;
            $biaoti = Catfish::getPost('biaoti');
            $biaoti = str_replace('\'','\\\'',$biaoti);
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value) VALUES (1, 'title', '" . $biaoti . "')");
            $subtitle = Catfish::lang('Another YUYUE CMS website');
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value) VALUES (2, 'subtitle', '" . $subtitle . "')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value) VALUES (3, 'keyword', '')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value) VALUES (4, 'description', '')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value) VALUES (5, 'template', 'default')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value) VALUES (6, 'record', '')");
            $copyright = Catfish::lang('YUYUE CMS');
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value) VALUES (7, 'copyright', '".serialize($copyright)."')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value) VALUES (8, 'statistics', '".serialize('')."')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (9, 'email', '" . Catfish::getPost('email') . "', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (10, 'filtername', '', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (11, 'comment', 0, 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (12, 'domain', '".$domain."', 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (13, 'logo', '', 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (14, 'captcha', '1', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (15, 'filtercomment', '', 0)");
            $rewrite = 0;
            if(Catfish::isRewrite()){
                $rewrite = 1;
            }
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (16, 'rewrite', ".$rewrite.", 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (17, 'allowLogin', 1, 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (18, 'closeSlide', 0, 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (19, 'icon', '', 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (20, 'everyPageShows', 10, 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (21, 'openMessage', 1, 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (22, 'closeComment', 0, 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (23, 'closeSitemap', 0, 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (24, 'closeRSS', 0, 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (25, 'closeSite', 0, 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (26, 'frontFrame', 'bootstrap4', 0)");
            $thumb = ['fixed' => 0,'width' => 0,'height' => 0];
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (27, 'thumb', '".serialize($thumb)."', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (28, 'bulletin', '', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (29, 'spare', '', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (30, 'write', '0', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (31, 'checkwrite', '0', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (32, 'creationtime', '{$create_date}', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (33, 'serial', '', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (34, 'random', '{$rmd}', 0)");
            $randomarr = ['PGEg','HJlZj0i','HR0cDovL3d3dy55dXl1ZS1jbXMuY29tIiBpZD0iY2F0Zmlz','GNtcyIgdGFyZ2V0PSJfYmxhbmsiPumxvOi3g0NNUzwvYT4gLSDnlLFDYXRm','XNoKOmytumxvCkgQ01T5','6Y5p','55Yi25L2c'];
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "options` (id,option_name,option_value,autoload) VALUES (35, 'randomarr', '".serialize($randomarr)."', 0)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "company` (id,jianjie) VALUES (1,'')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "navcat` (nav_name,active) VALUES ('".Catfish::lang('Navigation menu')."', 1)");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "mlabel` (id,biaoqian,outpos,method,homeout) VALUES (1, 'shouye', 'home', 'latestRelease', 'all')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "mlabel` (id,biaoqian,outpos,method) VALUES (2, 'xinwen', 'newslist', 'latestRelease')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "mlabel` (id,biaoqian,outpos,method) VALUES (3, 'chanpin', 'productlist', 'latestRelease')");
            $this->dbExec("INSERT INTO `" . Catfish::getPost('prefix') . "mlabel` (id,biaoqian,outpos,method) VALUES (4, 'sousuo', 'search', 'latestRelease')");
            $conf = file_get_contents(APP_PATH . 'install/data/database.php');
            $data['password'] = Catfish::getPost('password');
            $data['prefix'] = Catfish::getPost('prefix');
            foreach ($data as $key => $value) {
                $conf = str_replace("#{$key}#", $value, $conf);
            }
            file_put_contents(APP_PATH . 'database.php', $conf);
            echo '<div class="hidden">';
            $this->success(Catfish::lang('Installation completed'), 'step4');
            echo '</div>';
        }
        return '';
    }
    public function step4()
    {
        $this->assign('CatfishCMS',Catfish::getConfig('catfishCMS'));
        $this->domain();
        $view = $this->fetch();
        return $view;
    }
    private function check()
    {
        if(is_file(APP_PATH . 'database.php')){
            $this->redirect('index/Index/index');
            exit;
        }
    }
    private function domain()
    {
        $http = Request::instance()->isSsl() ? 'https://' : 'http://';
        $domain = $http . str_replace("\\",'/',$_SERVER['HTTP_HOST'].str_replace('/index.php','', Catfish::oUrl('/')));
        $domain = substr($domain, -1, 1) == '/' ? $domain : $domain . '/';
        $this->assign('domain',$domain);
        return $domain;
    }
    private function dbExec($exStr,$query = false)
    {
        try{
            $cnn = Db::connect([
                'type' => 'mysql',
                'dsn' => '',
                'hostname' => Catfish::getPost('host'),
                'database' => Catfish::getPost('name'),
                'username' => Catfish::getPost('user'),
                'password' => Catfish::getPost('password'),
                'hostport' => Catfish::getPost('port'),
                'params' => [],
                'charset' => 'utf8',
                'prefix' => Catfish::getPost('prefix')
            ]);
            if($query == false)
            {
                $cnn->execute($exStr);
            }
            else
            {
                return $cnn->query($exStr);
            }
        }catch(\Exception $e){
            return false;
        }
        return true;
    }
}
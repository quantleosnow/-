<?php

class app
{
	public $G;
	public $data = array();
	public $sessionvars;

	public function __construct(&$G)
	{
		$this->G = $G;
		$this->ev = $this->G->make('ev');
		$this->tpl = $this->G->make('tpl');
		$this->sql = $this->G->make('sql');
		$this->db = $this->G->make('db');
		$this->pg = $this->G->make('pg');
		$this->html = $this->G->make('html');
		$this->session = $this->G->make('session');
		$this->_user = $this->session->getSessionUser();
		if(!$this->_user['sessionuserid'])
		{
			if($this->ev->get('userhash'))
			exit(json_encode(array(
				'statusCode' => 300,
				"message" => "请您重新登录",
			    "callbackType" => 'forward',
			    "forwardUrl" => "index.php?user-app-login"
			)));
			else
			{
				header("location:index.php?user-app-login");
				exit;
			}
		}
		$this->user = $this->G->make('user','user');
		$this->exam = $this->G->make('exam','exam');
		$this->basic = $this->G->make('basic','exam');
		$this->section = $this->G->make('section','exam');
		$this->question = $this->G->make('question','exam');
		$this->info = $this->G->make('info','exam');
		$this->favor = $this->G->make('favor','exam');
		$this->answer = $this->G->make('answer','exam');
		$this->sessionvars = $this->exam->getExamSessionBySessionid();
		$this->questypes = $this->basic->getQuestypeList();
		$this->subjects = $this->basic->getSubjectList();
		$openbasics = trim($this->sessionvars['examsessionopenbasics']," ,");
		$this->data['openbasics'] = $this->basic->getBasicsByApi($openbasics);
		if(!$this->data['openbasics'])$this->data['openbasics'] = $this->basic->getOpenBasicsByUserid($this->_user['sessionuserid']);
		//if(!$this->data['openbasics'])$this->data['openbasics'] = $this->basic->getBasicsByArgs("basicdemo = '1'");
		if(!$this->_user['sessioncurrent'] || !$this->data['openbasics'][$this->_user['sessioncurrent']])
		{
			$this->data['currentbasic'] = current($this->data['openbasics']);
			$this->_user['sessioncurrent'] = $this->data['currentbasic']['basicid'];
			$this->session->setSessionValue(array('sessioncurrent'=>$this->_user['sessioncurrent']));
		}
		else
		$this->data['currentbasic'] = $this->data['openbasics'][$this->_user['sessioncurrent']];
		$this->selectorder = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
		$this->tpl->assign('ols',array(1=>'一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十'));
		$this->tpl->assign('selectorder',$this->selectorder);
		$this->tpl->assign('data',&$this->data);
		$this->tpl->assign('questypes',$this->questypes);
		$this->tpl->assign('subjects',$this->subjects);
		$this->tpl->assign('globalsections',$this->section->getSectionListByArgs("sectionsubjectid = '{$this->data['currentbasic']['basicsubjectid']}'"));
		$this->tpl->assign('globalknows',$this->section->getAllKnowsBySubject($this->data['currentbasic']['basicsubjectid']));
		$this->tpl->assign('_user',$this->user->getUserById($this->_user['sessionuserid']));
		$this->tpl->assign('userhash',$this->ev->get('userhash'));
	}

	public function basics()
	{
		$action = $this->ev->url(3);
		$page = $this->ev->get('page');
		switch($action)
		{
			case 'openit':
			$basicid = $this->ev->get('basicid');
			$basic = $this->basic->getBasicById($basicid);
			if(!$basic)
			{
				$message = array(
					'statusCode' => 300,
					"message" => "操作失败，此考场不存在"
				);
				exit(json_encode($message));
			}
			$userid = $this->_user['sessionuserid'];
			if($this->basic->getOpenBasicByUseridAndBasicid($userid,$basicid))
			{
				$message = array(
					'statusCode' => 300,
					"message" => "您已经开通了本考场"
				);
			}
			if($basic['basicdemo'])
			{
				$time = 365*24*3600;
			}
			else
			{
				$opentype = intval($this->ev->get('opentype'));
				$price = 0;
				if(trim($basic['basicprice']))
				{
					$price = array();
					$basic['basicprice'] = explode("\n",$basic['basicprice']);
					foreach($basic['basicprice'] as $p)
					{
						if($p)
						{
							$p = explode(":",$p);
							$price[] = array('time'=>intval($p[0]),'price'=>intval($p[1]));
						}
					}
				}
				if(!$price[$opentype])$t = $price[0];
				else
				$t = $price[$opentype];
				$time = $t['time']*24*3600;
				$score = $t['price'];
				$user = $this->user->getUserById($this->_user['sessionuserid']);
				if($user['usercoin'] < $score)
				{
					$message = array(
						'statusCode' => 300,
						"message" => "操作失败，您的积分不够"
					);
					exit(json_encode($message));
				}
				else
				{
					$args = array("usercoin" => $user['usercoin'] - $score);
					$this->user->modifyUserInfo($args,$this->_user['sessionuserid']);
				}
			}
			$args = array('obuserid'=>$userid,'obbasicid'=>$basicid,'obendtime'=>TIME + $time);
			$this->basic->openBasic($args);
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
			    "forwardUrl" => "index.php?exam-app-basics"
			);
			exit(json_encode($message));
			break;

			case 'detail':
			$this->basic->delOpenPassBasic($this->_user['sessionuserid']);
			$this->area = $this->G->make('area','exam');
			$basicid = $this->ev->get('basicid');
			$basic = $this->basic->getBasicById($basicid);
			$areas = $this->area->getAreaList();
			$price = 0;
			if(trim($basic['basicprice']))
			{
				$price = array();
				$basic['basicprice'] = explode("\n",$basic['basicprice']);
				foreach($basic['basicprice'] as $p)
				{
					if($p)
					{
						$p = explode(":",$p);
						$price[] = array('time'=>$p[0],'price'=>$p[1]);
					}
				}
				$this->tpl->assign('price',$price);
			}
			$isopen = $this->basic->getOpenBasicByUseridAndBasicid($this->_user['sessionuserid'],$basicid);
			$this->tpl->assign('isopen',$isopen);
			$this->tpl->assign('areas',$areas);
			$this->tpl->assign('basic',$basic);
			$this->tpl->display('basics_detail');
			break;

			case 'open':
			$this->area = $this->G->make('area','exam');
			$search = $this->ev->get('search');
			$page = $page > 1?$page:1;
			$subjects = $this->basic->getSubjectList();
			if(!$search)
			$args = 1;
			else
			{
				$args = array();
				if($search['basicdemo'])$args[] = "basicdemo = '{$search['basicdemo']}'";
				if($search['keyword'])$args[] = "basic LIKE '%{$search['keyword']}%'";
				if($search['basicareaid'])$args[] = "basicareaid = '{$search['basicareaid']}'";
				if($search['basicsubjectid'])$args[] = "basicsubjectid = '{$search['basicsubjectid']}'";
				if($search['basicapi'])$args[] = "basicapi = '{$search['basicapi']}'";
			}
			$basics = $this->basic->getBasicList($page,20,$args);
			$areas = $this->area->getAreaList();
			$this->tpl->assign('search',$search);
			$this->tpl->assign('areas',$areas);
			$this->tpl->assign('subjects',$subjects);
			$this->tpl->assign('basics',$basics);
			$this->tpl->display('basics_open');
			break;

			default:
			$this->tpl->assign('basics',$this->data['openbasics']);
			$this->tpl->display('basics');
			break;
		}
	}

	//首页
	public function index()
	{
		$action = $this->ev->url(3);
		switch($action)
		{
			case 'setCurrentBasic':
			$basicid = $this->ev->get('basicid');
			$this->session->setSessionValue(array('sessioncurrent'=>$basicid));
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
			    "callbackType" => 'forward',
			    "forwardUrl" => "index.php?exam-app"
			);
			exit(json_encode($message));
			break;

			case 'ajax':
			switch($this->ev->url(4))
			{
				//根据章节获取知识点信息
				case 'getknowsbysectionid':
				$sectionid = $this->ev->get('sectionid');
				$knowsids = trim(implode(",",$this->data['currentbasic']['basicknows'][$sectionid]),", ");
				$aknows = $this->section->getKnowsListByArgs(array("knowsid IN ({$knowsids})","knowsstatus = 1"));
				if($sectionid)
				$data = '<option value="0">选择知识点</option>'."\n";
				else
				$data = '<option value="0">请先选择章节</option>'."\n";
				foreach($aknows as $knows)
				{
					$data .= '<option value="'.$knows['knowsid'].'">'.$knows['knows'].'</option>'."\n";
				}
				exit($data);
				break;

				//获取剩余时间
				case 'lefttime':
				$sessionvars = $this->exam->getExamSessionBySessionid();
				$lefttime = TIME - $sessionvars['examsessionstarttime'];
				if($lefttime < 0 )$lefttime = 0;
				exit("{$lefttime}");
				break;

				//根据科目获取章节信息
				case 'getsectionsbysubjectid':
				$esid = $this->ev->get('subjectid');
				$knowsids = trim(implode(",",$this->data['currentbasic']['basicknows'][$sectionid]),", ");
				$aknows = $this->section->getSectionListByArgs(array("sectionsubjectid = '{$esid}'"));
				$data = array(array(0,'选择章节'));
				foreach($aknows as $knows)
				{
					$data[] = array($knows['sectionid'],$knows['section']);
				}
				exit(json_encode($data));
				break;

				//标注题目
				case 'sign':
				$questionid = intval($this->ev->get('questionid'));
				$url = $this->G->make('strings')->destr($this->ev->get('url'));
				$sessionvars = $this->exam->getExamSessionBySessionid();
				$args['examsessionsign'] = $sessionvars['examsessionsign'];
				if($questionid && !$args['examsessionsign'][$questionid])
				{
					$args['examsessionsign'][$questionid] = $url;
					$args['examsessionsign'] = $this->ev->addSlashes(serialize($args['examsessionsign']));
					$this->exam->modifyExamSession($args);
					exit('1');
				}
				else
				{
					unset($args['examsessionsign'][$questionid]);
					$args['examsessionsign'] = $this->ev->addSlashes(serialize($args['examsessionsign']));
					$this->exam->modifyExamSession($args);
					exit('2');
				}
				break;

				default:
			}
			break;

			default:
			if(!$this->data['openbasics'])exit(header("location:index.php?exam-app-basics-open"));
			$this->tpl->display('index');
		}
	}

	public function login()
	{
		if($this->ev->get('userlogin'))
		{
			$args = $this->ev->get('args');
			$user = $this->user->getUserByUserName($args['username']);
			if($user['userid'])
			{
				if($user['userpassword'] == md5($args['userpassword']))
				{
					$this->session->setSessionUser(array('sessionuserid'=>$user['userid'],'sessionpassword'=>$user['userpassword'],'sessionip'=>$this->ev->getClientIp(),'sessiongroupid'=>$user['usergroupid'],'sessionlogintime'=>TIME,'sessionusername'=>$user['username']));
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功",
					    "target" => "",
					    "rel" => "",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?{$this->G->app}-app"
					);
					exit(json_encode($message));
				}
			}
			$message = array(
				'statusCode' => 300,
				'errorinput' => 'args[username]',
				"message" => "操作失败"
			);
			exit(json_encode($message));
		}
		else
		{
			$this->tpl->display('login');
		}
	}

	public function logout()
	{
		$this->session->clearSessionUser();
		header("location:index.php?exam-app-login");
		exit;
	}

	//强化训练
	public function exercise()
	{
		$action = $this->ev->url(3);
		switch($action)
		{
			//重新抽题
			case 'reload':
			$args = array('examsessionkey' => 0);
			$this->exam->modifyExamSession($args);
			header("location:index.php?exam-app-exercise");
			exit;
			break;

			case 'ajax':
			switch($this->ev->url(4))
			{
				//获取剩余考试时间
				case 'getexamlefttime':
				$sessionvars = $this->exam->getExamSessionBySessionid();
				$lefttime = 0;
				if($sessionvars['examsessionstatus'] == 0 && $sessionvars['examsessiontype'] == 1)
				{
					if(TIME > ($sessionvars['examsessionstarttime'] + $sessionvars['examsessiontime']*60))
					{
						$lefttime = $sessionvars['examsessiontime']*60;
					}
					else
					{
						$lefttime = TIME - $sessionvars['examsessionstarttime'];
					}
				}
				echo $lefttime;
				exit();
				break;

				case 'getQuestionNumber':
				$questype = $this->basic->getQuestypeList();
				$sectionid = $this->ev->get('sectionid');
				$knowids = $this->ev->get('knowsid');
				if(!$knowids)
				{
					if(!$sectionid)$knows = $this->section->getAllKnowsBySubject($this->data['currentsubject']['subjectid']);
					else
					$knows = $this->section->getKnowsListByArgs(array("knowssectionid = '{$sectionid}'","knowsstatus = 1"));
					foreach($knows as $key => $p)
					$knowids .= "{$key},";
					$knowids = trim($knowids," ,");
				}
				$numbers = array();
				$numbers = array();
				foreach($questype as $p)
				{
					$numbers[$p['questid']] = intval(ceil($this->exam->getQuestionNumberByQuestypeAndKnowsid($p['questid'],$knowids)));
				}
				$this->tpl->assign('numbers',$numbers);
				$this->tpl->assign('questype',$questype);
				$this->tpl->display('exercise_number');
				break;

				case 'saveUserAnswer':
				$question = $this->ev->post('question');
				foreach($question as $key => $t)
				{
					if($t == '')unset($question[$key]);
				}
				$this->exam->modifyExamSession(array('examsessionuseranswer'=>$this->ev->addSlashes(serialize($question))));
				echo is_array($question)?count($question):0;
				exit;
				break;

				default:
			}
			break;

			//查看考试后的题目及答案
			case 'view':
			$sessionvars = $this->exam->getExamSessionBySessionid();
			if($sessionvars['examsessiontype'])
			{
				if($sessionvars['examsessiontype'] == 1)
				header("location:?exam-app-exampaper-view");
				else
				header("location:index.php?exam-app-exam-view");
				exit;
			}
			$this->tpl->assign('questype',$this->basic->getQuestypeList());
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->display('exercise_view');
			break;

			//计算主观题分数和显示分数
			case 'makescore':
			$questype = $this->basic->getQuestypeList();
			$sessionvars = $this->exam->getExamSessionBySessionid();
			if($this->ev->get('makescore'))
			{
				$score = $this->ev->get('score');
				$sumscore = 0;
				if(is_array($score))
				{
					foreach($score as $key => $p)
					{
						$sessionvars['examsessionscorelist'][$key] = $p;
					}
				}
				foreach($sessionvars['examsessionscorelist'] as $p)
				{
					$sumscore = $sumscore + floatval($p);
				}
				$sessionvars['examsessionscore'] = $sumscore;
				$args['examsessionscorelist'] = $this->ev->addSlashes(serialize($sessionvars['examsessionscorelist']));
				$allnumber = floatval(count($sessionvars['examsessionscorelist']));
				$args['examsessionscore'] = floatval(($sessionvars['examsessionscore']*100)/$allnumber);
				$args['examsessionstatus'] = 2;
				$this->exam->modifyExamSession($args);
				if(!$sessionvars['examsessionissave'])
				{
					$sargs['examsessionissave'] = 1;
					$this->exam->modifyExamSession($sargs);
					$this->favor->addExamHistory();
				}
				if($this->ev->get('direct'))
				{
					header("location:index.php?exam-app-exercise-makescore");
					exit;
				}
				else
				{
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?exam-app-exercise-makescore"
					);
					exit(json_encode($message));
				}
			}
			else
			{
				if(!$sessionvars['examsessionissave'])
				{
					$sargs['examsessionissave'] = 1;
					$this->exam->modifyExamSession($sargs);
					$this->favor->addExamHistory();
				}
				$questype = $this->basic->getQuestypeList();
				$number = array();
				$right = array();
				$score = array();
				$allnumber = 0;
				$allright = 0;
				foreach($questype as $key => $q)
				{
					$number[$key] = 0;
					$right[$key] = 0;
					$score[$key] = 0;
					if($sessionvars['examsessionquestion']['questions'][$key])
					{
						foreach($sessionvars['examsessionquestion']['questions'][$key] as $p)
						{
							$number[$key]++;
							$allnumber++;
							if($sessionvars['examsessionscorelist'][$p['questionid']] == 1)
							{
								$right[$key]++;
								$allright++;
							}
							$score[$key] = $score[$key]+$sessionvars['examsessionscorelist'][$p['questionid']];
						}
					}
					if($sessionvars['examsessionquestion']['questionrows'][$key])
					{
						foreach($sessionvars['examsessionquestion']['questionrows'][$key] as $v)
						{
							foreach($v['data'] as $p)
							{
								$number[$key]++;
								$allnumber++;
								if($sessionvars['examsessionscorelist'][$p['questionid']] == 1)
								{
									$right[$key]++;
									$allright++;
								}
								$score[$key] = $score[$key]+$sessionvars['examsessionscorelist'][$p['questionid']];
							}
						}
					}
				}
				$this->tpl->assign('questype',$questype);
				$this->tpl->assign('allright',$allright);
				$this->tpl->assign('allnumber',$allnumber);
				$this->tpl->assign('right',$right);
				$this->tpl->assign('score',$score);
				$this->tpl->assign('number',$number);
				$this->tpl->assign('questype',$questype);
				$this->tpl->assign('sessionvars',$sessionvars);
				$this->tpl->display('exercise_score');
			}
			break;

			//评分和显示分数
			case 'score':
			$questype = $this->basic->getQuestypeList();
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$needhand = 0;
			if($this->ev->get('insertscore'))
			{
				$question = $this->ev->get('question');
				foreach($question as $key => $a)
				$sessionvars['examsessionuseranswer'][$key] = $a;
				foreach($sessionvars['examsessionquestion']['questions'] as $key => $tmp)
				{
					if(!$questype[$key]['questsort'])
					{
						foreach($tmp as $p)
						{
							if(is_array($sessionvars['examsessionuseranswer'][$p['questionid']]))
							{
								$nanswer = '';
								$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
								asort($answer);
								$nanswer = implode("",$answer);
								if($nanswer == $p['questionanswer'])$score = 1;
								else
								{
									if($questype[$key]['questchoice'] == 3)
									{
										$alen = strlen($p['questionanswer']);
										$rlen = 0;
										foreach($answer as $t)
										{
											if(strpos($p['questionanswer'],$t) === false)
											{
												$rlen = 0;
												break;
											}
											else
											{
												$rlen ++;
											}
										}
										$score = floatval($rlen/$alen);
									}
									else $score = 0;
								}
							}
							else
							{
								$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
								if($answer == $p['questionanswer'])$score = 1;
								else $score = 0;
							}
							$scorelist[$p['questionid']] = $score;
						}
					}
					else
					{
						if(is_array($tmp) && count($tmp))
						$needhand = 1;
					}
				}
				foreach($sessionvars['examsessionquestion']['questionrows'] as $key => $tmp)
				{
					if(!$questype[$key]['questsort'])
					{
						foreach($tmp as $tmp2)
						{
							foreach($tmp2['data'] as $p)
							{
								if(is_array($sessionvars['examsessionuseranswer'][$p['questionid']]))
								{
									$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
									asort($answer);
									$nanswer = implode("",$answer);
									if($nanswer == $p['questionanswer'])$score = 1;
									else
									{
										if($questype[$key]['questchoice'] == 3)
										{
											$alen = strlen($p['questionanswer']);
											$rlen = 0;
											foreach($answer as $t)
											{
												if(strpos($p['questionanswer'],$t) === false)
												{
													$rlen = 0;
													break;
												}
												else
												{
													$rlen ++;
												}
											}
											$score = $rlen/$alen;
										}
										else $score = 0;
									}
								}
								else
								{
									$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
									if($answer == $p['questionanswer'])$score = 1;
									else $score = 0;
								}
								$scorelist[$p['questionid']] = $score;
							}
						}
					}
					else
					{
						if(!$needhand)
						{
							if(is_array($tmp) && count($tmp))
							$needhand = 1;
						}
					}
				}
				$args['examsessionuseranswer'] = $this->ev->addSlashes(serialize($question));
				$args['examsessionscorelist'] = $this->ev->addSlashes(serialize($scorelist));
				if(!$needhand)
				{
					$args['examsessionstatus'] = 2;
					$this->exam->modifyExamSession($args);
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?exam-app-exercise-makescore&makescore=1&direct=1"
					);
				}
				else
				{
					$args['examsessionstatus'] = 1;
					$this->exam->modifyExamSession($args);
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?exam-app-exercise-score"
					);
				}
				exit(json_encode($message));
			}
			else
			{
				if($sessionvars['examsessionstatus'] == 2)
				{
					header("location:?exam-app-exercise-makescore");
					exit;
				}
				else
				{
					$this->tpl->assign('sessionvars',$sessionvars);
					$this->tpl->assign('questype',$questype);
					$this->tpl->display('exercise_mkscore');
				}
			}
			break;

			case 'paper':
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$lefttime = 0;
			$questype = $this->basic->getQuestypeList();
			if($sessionvars['examsessionstatus'] == 2)
			{
				header("location:?exam-app-exercise-makescore&makescore=1&direct=1");
				exit;
			}
			elseif($sessionvars['examsessionstatus'] == 1)
			{
				header("location:index.php?exam-app-exercise-score");
				exit;
			}
			else
			{
				$this->tpl->assign('questype',$questype);
				$this->tpl->assign('sessionvars',$sessionvars);
				$this->tpl->assign('lefttime',$lefttime);
				$this->tpl->assign('donumber',is_array($sessionvars['examsessionuseranswer'])?count($sessionvars['examsessionuseranswer']):0);
				$this->tpl->display('exercise_paper');
			}
			break;

			/**
			case 'selectquestions':
			$knowsid = $this->ev->get('knowsid');
			$knows = $this->section->getKnowsByArgs("knowsid = '{$knowsid}'");
			if(!$knows['knowsid'])header("location:?exam-app-exercise");
			$args = array('score'=>100,'examtime'=>60,'title'=>date('Y-m-d').$knows['knows']."知识点强化训练");
			$args['knowsid'] = $knowsid;
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$questype = $this->basic->getQuestypeList();
			foreach($questype as $p)
			{
				$args['number'][$p['questid']] = rand(5,10);
			}
			arsort($args['number']);
			$dt = key($args['number']);
			$questionids = $this->question->selectQuestionsByKnows($args['knowsid'],$args['number'],$dt);
			$questions = array();
			$questionrows = array();
			foreach($questionids['question'] as $key => $p)
			{
				$ids = "";
				if(count($p))
				{
					foreach($p as $t)
					{
						$ids .= $t.',';
					}
					$ids = trim($ids," ,");
					if(!$ids)$ids = 0;
					$questions[$key] = $this->exam->getQuestionListByArgs("questionid IN ({$ids})");
				}
			}
			foreach($questionids['questionrow'] as $key => $p)
			{
				$ids = "";
				if(is_array($p))
				{
					if(count($p))
					{
						foreach($p as $t)
						{
							$questionrows[$key][$t] = $this->exam->getQuestionRowsByArgs("qrid = '{$t}'");
						}
					}
				}
				else $questionrows[$key][$p] = $this->exam->getQuestionRowsByArgs("qrid = '{$p}'");
			}
			$sargs['examsessionquestion'] = $this->ev->addSlashes(serialize(array('questionids'=>$questionids,'questions'=>$questions,'questionrows'=>$questionrows)));
			$sargs['examsessionsetting'] = $this->ev->addSlashes(serialize(array('examsetting' => $args)));
			$sargs['examsessionstarttime'] = TIME;
			$sargs['examsessionuseranswer'] = NULL;
			$sargs['examsession'] = $args['title'];
			$sargs['examsessiontime'] = $args['examtime']>0?$args['examtime']:60;
			$sargs['examsessionstatus'] = 0;
			$sargs['examsessiontype'] = 0;
			$sargs['examsessionbasic'] = $this->data['currentbasic']['basicid'];
			$sargs['examsessionkey'] = md5($args['knowsid']);
			$sargs['examsessionissave'] = 0;
			$sargs['examsessionsign'] = NULL;
			$sargs['examsessionscore'] = 0;
			$sargs['examsessionscorelist'] = '';
			if($sessionvars['examsessionid'])
			$this->exam->modifyExamSession($sargs);
			else
			$this->exam->insertExamSession($sargs);
			header("location:?exam-app-exercise-paper");
			break;

			//强化训练考试页面
			default:
			$sectionids = trim(implode(",",$this->data['currentbasic']['basicsection']),", ");
			$sections = $this->section->getSectionListByArgs("sectionid IN ({$sectionids})");
			$knows = array();
			foreach($this->data['currentbasic']['basicsection'] as $p)
			{
				$knowsids = trim(implode(",",$this->data['currentbasic']['basicknows'][$p]),", ");
				$knows[$p] = $this->section->getKnowsListByArgs(array("knowsid IN ({$knowsids})","knowsstatus = 1"));
			}
			$this->tpl->assign('sections',$sections);
			$this->tpl->assign('knows',$knows);
			$this->tpl->display('exercise');
			**/
			default:
			$questype = $this->basic->getQuestypeList();
			if($this->ev->get('setExecriseConfig'))
			{
				$args = $this->ev->get('args');
				$sessionvars = $this->exam->getExamSessionBySessionid();
				if(!$args['knowsid'])
				{
					$args['knowsid'] = '';
					if($args['sectionid'])
					$knowsids = $this->section->getKnowsListByArgs(array("knowssectionid = '{$args['sectionid']}'","knowsstatus = 1"));
					else
					{
						$knowsids = $this->section->getAllKnowsBySubject($this->data['currentsubject']['subjectid']);
					}
					foreach($knowsids as $key => $p)
					$args['knowsid'] .= intval($key).",";
					$args['knowsid'] = trim($args['knowsid']," ,");
				}
				arsort($args['number']);
				$dt = key($args['number']);
				$questionids = $this->question->selectQuestionsByKnows($args['knowsid'],$args['number'],$dt);
				$questions = array();
				$questionrows = array();
				foreach($questionids['question'] as $key => $p)
				{
					$ids = "";
					if(count($p))
					{
						foreach($p as $t)
						{
							$ids .= $t.',';
						}
						$ids = trim($ids," ,");
						if(!$ids)$ids = 0;
						$questions[$key] = $this->exam->getQuestionListByArgs("questionid IN ({$ids})");
					}
				}
				foreach($questionids['questionrow'] as $key => $p)
				{
					$ids = "";
					if(is_array($p))
					{
						if(count($p))
						{
							foreach($p as $t)
							{
								$questionrows[$key][$t] = $this->exam->getQuestionRowsByArgs("qrid = '{$t}'");
							}
						}
					}
					else $questionrows[$key][$p] = $this->exam->getQuestionRowsByArgs("qrid = '{$p}'");
				}
				$sargs['examsessionquestion'] = $this->ev->addSlashes(serialize(array('questionids'=>$questionids,'questions'=>$questions,'questionrows'=>$questionrows)));
				$sargs['examsessionsetting'] = $this->ev->addSlashes(serialize($args));
				$sargs['examsessionstarttime'] = TIME;
				$sargs['examsessionuseranswer'] = NULL;
				$sargs['examsession'] = $args['title'];
				$sargs['examsessiontime'] = $args['time']>0?$args['time']:60;
				$sargs['examsessionstatus'] = 0;
				$sargs['examsessiontype'] = 0;
				$sargs['examsessionbasic'] = $this->data['currentbasic']['basicid'];
				$sargs['examsessionkey'] = md5($args['knowsid']);
				$sargs['examsessionissave'] = 0;
				$sargs['examsessionsign'] = NULL;
				$sargs['examsessionsign'] = '';
				if($sessionvars['examsessionid'])
				$this->exam->modifyExamSession($sargs);
				else
				$this->exam->insertExamSession($sargs);
				$message = array(
					'statusCode' => 200,
					"message" => "抽题成功，正在转入试题页面",
				    "callbackType" => 'forward',
				    "forwardUrl" => "index.php?exam-app-exercise-paper"
				);
				exit(json_encode($message));
			}
			else
			{
				$sections = $this->section->getSectionListByArgs("sectionsubjectid = '{$this->data['currentbasic']['basicsubjectid']}'");
				$knows = $this->section->getAllKnowsBySubject($this->data['currentbasic']['basicsubjectid']);
				$knowids = '';
				foreach($knows as $key => $p)
				$knowids .= "{$key},";
				$knowids = trim($knowids," ,");
				$numbers = array();
				foreach($questype as $p)
				{
					$numbers[$p['questid']] = intval(ceil($this->exam->getQuestionNumberByQuestypeAndKnowsid($p['questid'],$knowids)));
				}
				$this->tpl->assign('sections',$sections);
				$this->tpl->assign('questype',$questype);
				$this->tpl->assign('numbers',$numbers);
				$this->tpl->display('exercise');
			}
		}
	}

	//模拟考试
	public function exampaper()
	{
		$action = $this->ev->url(3);
		switch($action)
		{
			//重新抽题
			case 'reload':
			$args = array('examsessionkey' => 0);
			$this->exam->modifyExamSession($args);
			header("location:index.php?exam-app-exampaper");
			exit;
			break;

			case 'sign':
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$questype = $this->basic->getQuestypeList();
			$this->tpl->assign('questype',$questype);
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->display('exampaper_sign');
			break;

			case 'ajax':
			switch($this->ev->url(4))
			{
				//获取剩余考试时间
				case 'getexampaperlefttime':
				$sessionvars = $this->exam->getExamSessionBySessionid();
				$lefttime = 0;
				if($sessionvars['examsessionstatus'] == 0 && $sessionvars['examsessiontype'] == 1)
				{
					if(TIME > ($sessionvars['examsessionstarttime'] + $sessionvars['examsessiontime']*60))
					{
						$lefttime = $sessionvars['examsessiontime']*60;
					}
					else
					{
						$lefttime = TIME - $sessionvars['examsessionstarttime'];
					}
				}
				echo $lefttime;
				exit();
				break;

				case 'saveUserAnswer':
				$question = $this->ev->post('question');
				foreach($question as $key => $t)
				{
					if($t == '')unset($question[$key]);
				}
				$this->exam->modifyExamSession(array('examsessionuseranswer'=>$this->ev->addSlashes(serialize($question))));
				echo is_array($question)?count($question):0;
				exit;
				break;

				default:
			}
			break;

			//查看考试后的题目及答案
			case 'view':
			$sessionvars = $this->exam->getExamSessionBySessionid();
			if($sessionvars['examsessiontype'] != 1)
			{
				if($sessionvars['examsessiontype'])
				header("location:index.php?exam-app-exam-view");
				else
				header("location:index.php?exam-app-exercise-view");
				exit;
			}
			$this->tpl->assign('questype',$this->basic->getQuestypeList());
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->display('exampaper_view');
			break;

			//计算主观题分数和显示分数
			case 'makescore':
			$questype = $this->basic->getQuestypeList();
			$sessionvars = $this->exam->getExamSessionBySessionid();
			if($this->ev->get('makescore'))
			{
				$score = $this->ev->get('score');
				$sumscore = 0;
				if(is_array($score))
				{
					foreach($score as $key => $p)
					{
						$sessionvars['examsessionscorelist'][$key] = $p;
					}
				}
				foreach($sessionvars['examsessionscorelist'] as $p)
				{
					$sumscore = $sumscore + floatval($p);
				}
				$sessionvars['examsessionscore'] = $sumscore;
				$args['examsessionscorelist'] = $this->ev->addSlashes(serialize($sessionvars['examsessionscorelist']));
				$allnumber = floatval(count($sessionvars['examsessionscorelist']));
				$args['examsessionscore'] = $sessionvars['examsessionscore'];
				$args['examsessionstatus'] = 2;
				$this->exam->modifyExamSession($args);
				if(!$sessionvars['examsessionissave'])
				{
					$sargs['examsessionissave'] = 1;
					$this->exam->modifyExamSession($sargs);
					$this->favor->addExamHistory();
				}
				if($this->ev->get('direct'))
				{
					header("location:index.php?exam-app-exampaper-makescore");
					exit;
				}
				else
				{
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?exam-app-exampaper-makescore"
					);
					exit(json_encode($message));
				}
			}
			else
			{
				$number = array();
				$right = array();
				$score = array();
				$allnumber = 0;
				$allright = 0;
				foreach($questype as $key => $q)
				{
					$number[$key] = 0;
					$right[$key] = 0;
					$score[$key] = 0;
					if($sessionvars['examsessionquestion']['questions'][$key])
					{
						foreach($sessionvars['examsessionquestion']['questions'][$key] as $p)
						{
							$number[$key]++;
							$allnumber++;
							if($sessionvars['examsessionscorelist'][$p['questionid']] == $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'])
							{
								$right[$key]++;
								$allright++;
							}
							$score[$key] = $score[$key]+$sessionvars['examsessionscorelist'][$p['questionid']];
						}
					}
					if($sessionvars['examsessionquestion']['questionrows'][$key])
					{
						foreach($sessionvars['examsessionquestion']['questionrows'][$key] as $v)
						{
							foreach($v['data'] as $p)
							{
								$number[$key]++;
								$allnumber++;
								if($sessionvars['examsessionscorelist'][$p['questionid']] == $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'])
								{
									$right[$key]++;
									$allright++;
								}
								$score[$key] = $score[$key]+$sessionvars['examsessionscorelist'][$p['questionid']];
							}
						}
					}
				}
				$this->tpl->assign('allright',$allright);
				$this->tpl->assign('allnumber',$allnumber);
				$this->tpl->assign('right',$right);
				$this->tpl->assign('score',$score);
				$this->tpl->assign('number',$number);
				$this->tpl->assign('questype',$questype);
				$this->tpl->assign('sessionvars',$sessionvars);
				$this->tpl->display('exampaper_score');
			}
			break;

			//评分和显示分数
			case 'score':
			$questype = $this->basic->getQuestypeList();
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$needhand = 0;
			if($this->ev->get('insertscore'))
			{
				$question = $this->ev->get('question');
				foreach($question as $key => $a)
				$sessionvars['examsessionuseranswer'][$key] = $a;
				foreach($sessionvars['examsessionquestion']['questions'] as $key => $tmp)
				{
					if(!$questype[$key]['questsort'])
					{
						foreach($tmp as $p)
						{
							if(is_array($sessionvars['examsessionuseranswer'][$p['questionid']]))
							{
								$nanswer = '';
								$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
								asort($answer);
								$nanswer = implode("",$answer);
								if($nanswer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
								else
								{
									if($questype[$key]['questchoice'] == 3)
									{
										$alen = strlen($p['questionanswer']);
										$rlen = 0;
										foreach($answer as $t)
										{
											if(strpos($p['questionanswer'],$t) === false)
											{
												$rlen = 0;
												break;
											}
											else
											{
												$rlen ++;
											}
										}
										$score = floatval($sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'] * $rlen/$alen);
									}
									else $score = 0;
								}
							}
							else
							{
								$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
								if($answer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
								else $score = 0;
							}
							$scorelist[$p['questionid']] = $score;
						}
					}
					else
					{
						if(is_array($tmp) && count($tmp))
						$needhand = 1;
					}
				}
				foreach($sessionvars['examsessionquestion']['questionrows'] as $key => $tmp)
				{
					if(!$questype[$key]['questsort'])
					{
						foreach($tmp as $tmp2)
						{
							foreach($tmp2['data'] as $p)
							{
								if(is_array($sessionvars['examsessionuseranswer'][$p['questionid']]))
								{
									$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
									asort($answer);
									$nanswer = implode("",$answer);
									if($nanswer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
									else
									{
										if($questype[$key]['questchoice'] == 3)
										{
											$alen = strlen($p['questionanswer']);
											$rlen = 0;
											foreach($answer as $t)
											{
												if(strpos($p['questionanswer'],$t) === false)
												{
													$rlen = 0;
													break;
												}
												else
												{
													$rlen ++;
												}
											}
											$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'] * $rlen/$alen;
										}
										else $score = 0;
									}
								}
								else
								{
									$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
									if($answer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
									else $score = 0;
								}
								$scorelist[$p['questionid']] = $score;
							}
						}
					}
					else
					{
						if(!$needhand)
						{
							if(is_array($tmp) && count($tmp))
							$needhand = 1;
						}
					}
				}
				$args['examsessionuseranswer'] = $this->ev->addSlashes(serialize($question));
				$args['examsessionscorelist'] = $this->ev->addSlashes(serialize($scorelist));
				if(!$needhand)
				{
					$args['examsessionstatus'] = 2;
					$this->exam->modifyExamSession($args);
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?exam-app-exampaper-makescore&makescore=1&direct=1"
					);
				}
				else
				{
					if($sessionvars['examsessionsetting']['examdecide'])
					{
						$args['examsessionstatus'] = 2;
						$args['examsessionissave'] = 1;
						$this->exam->modifyExamSession($args);
						$this->favor->addExamHistory(0);
						$message = array(
							'statusCode' => 200,
							"message" => "操作成功，本试卷需要教师评分，请等待评分结果",
						    "callbackType" => 'forward',
						    "forwardUrl" => "index.php?exam-app-history&ehtype=1"
						);
					}
					else
					{
						$args['examsessionstatus'] = 1;
						$this->exam->modifyExamSession($args);
						$message = array(
							'statusCode' => 200,
							"message" => "操作成功",
						    "callbackType" => 'forward',
						    "forwardUrl" => "index.php?exam-app-exampaper-score"
						);
					}
				}
				exit(json_encode($message));
			}
			else
			{
				if($sessionvars['examsessionstatus'] == 2)
				{
					header("location:?exam-app-exampaper-makescore");
					exit;
				}
				else
				{
					$this->tpl->assign('sessionvars',$sessionvars);
					$this->tpl->assign('questype',$questype);
					$this->tpl->display('exampaper_mkscore');
				}
			}
			break;

			//考试页面
			case 'paper':
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$lefttime = 0;
			$questype = $this->basic->getQuestypeList();
			if($sessionvars['examsessionstatus'] == 2)
			{
				header("location:index.php?exam-app-exampaper-makescore");
				exit;
			}
			elseif($sessionvars['examsessionstatus'] == 1)
			{
				header("location:index.php?exam-app-exampaper-score");
				exit;
			}
			else
			{
				$exams = $this->exam->getExamSettingList(1,3,array("examsubject = '{$this->data['currentsubject']['subjectid']}'","examtype = 1"));
				$this->tpl->assign('questype',$questype);
				$this->tpl->assign('sessionvars',$sessionvars);
				$this->tpl->assign('lefttime',$lefttime);
				$this->tpl->assign('donumber',is_array($sessionvars['examsessionuseranswer'])?count($sessionvars['examsessionuseranswer']):0);
				$this->tpl->display('exampaper_paper');
			}
			break;

			case 'selectquestions':
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$examid = $this->ev->get('examid');
			if(!$examid)
			{
				header("location:?exam-app-exampaper");
				exit;
			}
			else
			{
				$questionids = $this->question->selectQuestions($examid,$this->data['currentbasic']);
				$questions = array();
				$questionrows = array();
				foreach($questionids['question'] as $key => $p)
				{
					$ids = "";
					if(count($p))
					{
						foreach($p as $t)
						{
							$ids .= $t.',';
						}
						$ids = trim($ids," ,");
						if(!$ids)$ids = 0;
						$questions[$key] = $this->exam->getQuestionListByArgs("questionid IN ({$ids})");
					}
				}
				foreach($questionids['questionrow'] as $key => $p)
				{
					$ids = "";
					if(is_array($p))
					{
						if(count($p))
						{
							foreach($p as $t)
							{
								$questionrows[$key][$t] = $this->exam->getQuestionRowsByArgs("qrid = '{$t}'");
							}
						}
					}
					else $questionrows[$key][$p] = $this->exam->getQuestionRowsByArgs("qrid = '{$p}'");
				}
				$sargs['examsessionquestion'] = $this->ev->addSlashes(serialize(array('questionids'=>$questionids,'questions'=>$questions,'questionrows'=>$questionrows)));
				$sargs['examsessionsetting'] = $this->ev->addSlashes(serialize($questionids['setting']));
				$sargs['examsessionstarttime'] = TIME;
				$sargs['examsession'] = $questionids['setting']['exam'];
				$sargs['examsessiontime'] = $questionids['setting']['examsetting']['examtime']>0?$questionids['setting']['examsetting']['examtime']:60;
				$sargs['examsessionstatus'] = 0;
				$sargs['examsessiontype'] = 1;
				$sargs['examsessionsign'] = NULL;
				$sargs['examsessionuseranswer'] = NULL;
				$sargs['examsessionbasic'] = $this->data['currentbasic']['basicid'];
				$sargs['examsessionkey'] = $examid;
				$sargs['examsessionissave'] = 0;
				$sargs['examsessionsign'] = '';
				if($sessionvars['examsessionid'])
				$this->exam->modifyExamSession($sargs);
				else
				$this->exam->insertExamSession($sargs);
				header("location:?exam-app-exampaper-paper");
				exit;
			}
			break;

			//显示考试须知等信息
			default:
			$page = $this->ev->get('page');
			$ids = trim($this->data['currentbasic']['basicexam']['auto'].','.$this->data['currentbasic']['basicexam']['train'],', ');
			if(!$ids)$ids = 0;
			$exams = $this->exam->getExamSettingList($page,20,array("examid IN ({$ids})","examtype = 1"));
			$this->tpl->assign('exams',$exams);
			$this->tpl->display('exampaper');
		}
	}

	public function exam()
	{
		$action = $this->ev->url(3);
		switch($action)
		{
			//重新抽题
			case 'reload':
			$args = array('examsessionkey' => 0);
			$this->exam->modifyExamSession($args);
			header("location:index.phpindex.php?exam-app-exam");
			exit;
			break;

			case 'ajax':
			switch($this->ev->url(4))
			{
				//获取剩余考试时间
				case 'getexamlefttime':
				$sessionvars = $this->exam->getExamSessionBySessionid();
				$lefttime = 0;
				if($sessionvars['examsessionstatus'] == 0 && $sessionvars['examsessiontype'] == 1)
				{
					if(TIME > ($sessionvars['examsessionstarttime'] + $sessionvars['examsessiontime']*60))
					{
						$lefttime = $sessionvars['examsessiontime']*60;
					}
					else
					{
						$lefttime = TIME - $sessionvars['examsessionstarttime'];
					}
				}
				echo $lefttime;
				exit();
				break;

				case 'saveUserAnswer':
				$question = $this->ev->post('question');
				foreach($question as $key => $t)
				{
					if($t == '')unset($question[$key]);
				}
				$this->exam->modifyExamSession(array('examsessionuseranswer'=>$this->ev->addSlashes(serialize($question))));
				echo is_array($question)?count($question):0;
				exit;
				break;

				default:
			}
			break;

			//查看考试后的题目及答案
			case 'view':
			$sessionvars = $this->exam->getExamSessionBySessionid();
			if($sessionvars['examsessiontype'] != 2)
			{
				if($sessionvars['examsessiontype'])
				header("location:index.php?exam-app-exampaper-view");
				else
				header("location:index.php?exam-app-exercise-view");
				exit;
			}
			$this->tpl->assign('questype',$this->basic->getQuestypeList());
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->display('exam_view');
			break;

			//计算主观题分数和显示分数
			case 'makescore':
			$questype = $this->basic->getQuestypeList();
			$sessionvars = $this->exam->getExamSessionBySessionid();
			if($this->ev->get('makescore'))
			{
				$score = $this->ev->get('score');
				$sumscore = 0;
				if(is_array($score))
				{
					foreach($score as $key => $p)
					{
						$sessionvars['examsessionscorelist'][$key] = $p;
					}
				}
				foreach($sessionvars['examsessionscorelist'] as $p)
				{
					$sumscore = $sumscore + floatval($p);
				}
				$sessionvars['examsessionscore'] = $sumscore;
				$args['examsessionscorelist'] = $this->ev->addSlashes(serialize($sessionvars['examsessionscorelist']));
				$allnumber = floatval(count($sessionvars['examsessionscorelist']));
				$args['examsessionscore'] = $sessionvars['examsessionscore'];
				$args['examsessionstatus'] = 2;
				$this->exam->modifyExamSession($args);
				if(!$sessionvars['examsessionissave'])
				{
					$sargs['examsessionissave'] = 1;
					$this->exam->modifyExamSession($sargs);
					$this->favor->addExamHistory();
				}
				if($this->ev->get('direct'))
				{
					header("location:?exam-app-exam-makescore");
					exit;
				}
				else
				{
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?exam-app-exam-makescore"
					);
					exit(json_encode($message));
				}
			}
			else
			{
				$number = array();
				$right = array();
				$score = array();
				$allnumber = 0;
				$allright = 0;
				foreach($questype as $key => $q)
				{
					$number[$key] = 0;
					$right[$key] = 0;
					$score[$key] = 0;
					if($sessionvars['examsessionquestion']['questions'][$key])
					{
						foreach($sessionvars['examsessionquestion']['questions'][$key] as $p)
						{
							$number[$key]++;
							$allnumber++;
							if($sessionvars['examsessionscorelist'][$p['questionid']] == $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'])
							{
								$right[$key]++;
								$allright++;
							}
							$score[$key] = $score[$key]+$sessionvars['examsessionscorelist'][$p['questionid']];
						}
					}
					if($sessionvars['examsessionquestion']['questionrows'][$key])
					{
						foreach($sessionvars['examsessionquestion']['questionrows'][$key] as $v)
						{
							foreach($v['data'] as $p)
							{
								$number[$key]++;
								$allnumber++;
								if($sessionvars['examsessionscorelist'][$p['questionid']] == $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'])
								{
									$right[$key]++;
									$allright++;
								}
								$score[$key] = $score[$key]+$sessionvars['examsessionscorelist'][$p['questionid']];
							}
						}
					}
				}
				$this->tpl->assign('allright',$allright);
				$this->tpl->assign('allnumber',$allnumber);
				$this->tpl->assign('right',$right);
				$this->tpl->assign('score',$score);
				$this->tpl->assign('number',$number);
				$this->tpl->assign('questype',$questype);
				$this->tpl->assign('sessionvars',$sessionvars);
				$this->tpl->display('exam_score');
			}
			break;

			//评分和显示分数
			case 'score':
			$questype = $this->basic->getQuestypeList();
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$needhand = 0;
			if($this->ev->get('insertscore'))
			{
				$question = $this->ev->get('question');
				foreach($question as $key => $a)
				$sessionvars['examsessionuseranswer'][$key] = $a;
				foreach($sessionvars['examsessionquestion']['questions'] as $key => $tmp)
				{
					if(!$questype[$key]['questsort'])
					{
						foreach($tmp as $p)
						{
							if(is_array($sessionvars['examsessionuseranswer'][$p['questionid']]))
							{
								$nanswer = '';
								$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
								asort($answer);
								$nanswer = implode("",$answer);
								if($nanswer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
								else
								{
									if($questype[$key]['questchoice'] == 3)
									{
										$alen = strlen($p['questionanswer']);
										$rlen = 0;
										foreach($answer as $t)
										{
											if(strpos($p['questionanswer'],$t) === false)
											{
												$rlen = 0;
												break;
											}
											else
											{
												$rlen ++;
											}
										}
										$score = floatval($sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'] * $rlen/$alen);
									}
									else $score = 0;
								}
							}
							else
							{
								$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
								if($answer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
								else $score = 0;
							}
							$scorelist[$p['questionid']] = $score;
						}
					}
					else
					{
						if(is_array($tmp) && count($tmp))
						$needhand = 1;
					}
				}
				foreach($sessionvars['examsessionquestion']['questionrows'] as $key => $tmp)
				{
					if(!$questype[$key]['questsort'])
					{
						foreach($tmp as $tmp2)
						{
							foreach($tmp2['data'] as $p)
							{
								if(is_array($sessionvars['examsessionuseranswer'][$p['questionid']]))
								{
									$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
									asort($answer);
									$nanswer = implode("",$answer);
									if($nanswer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
									else
									{
										if($questype[$key]['questchoice'] == 3)
										{
											$alen = strlen($p['questionanswer']);
											$rlen = 0;
											foreach($answer as $t)
											{
												if(strpos($p['questionanswer'],$t) === false)
												{
													$rlen = 0;
													break;
												}
												else
												{
													$rlen ++;
												}
											}
											$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'] * $rlen/$alen;
										}
										else $score = 0;
									}
								}
								else
								{
									$answer = $sessionvars['examsessionuseranswer'][$p['questionid']];
									if($answer == $p['questionanswer'])$score = $sessionvars['examsessionsetting']['examsetting']['questype'][$key]['score'];
									else $score = 0;
								}
								$scorelist[$p['questionid']] = $score;
							}
						}
					}
					else
					{
						if(!$needhand)
						{
							if(is_array($tmp) && count($tmp))
							$needhand = 1;
						}
					}
				}
				$args['examsessionuseranswer'] = $this->ev->addSlashes(serialize($question));
				$args['examsessionscorelist'] = $this->ev->addSlashes(serialize($scorelist));
				if(!$needhand)
				{
					$args['examsessionstatus'] = 2;
					$args['examsessionscore'] = array_sum($scorelist);
					$this->exam->modifyExamSession($args);
					$message = array(
						'statusCode' => 200,
						"message" => "操作成功",
					    "callbackType" => 'forward',
					    "forwardUrl" => "index.php?exam-app-exam-makescore&makescore=1&direct=1"
					);
				}
				else
				{
					if($sessionvars['examsessionsetting']['examdecide'])
					{
						$args['examsessionstatus'] = 2;
						$args['examsessionissave'] = 1;
						$this->exam->modifyExamSession($args);
						$this->favor->addExamHistory(0);
						$message = array(
							'statusCode' => 200,
							"message" => "操作成功，本试卷需要教师评分，请等待评分结果",
						    "callbackType" => 'forward',
						    "forwardUrl" => "index.php?exam-app-history&ehtype=2"
						);
					}
					else
					{
						$args['examsessionstatus'] = 1;
						$this->exam->modifyExamSession($args);
						$this->favor->addExamHistory(1);
						$message = array(
							'statusCode' => 200,
							"message" => "操作成功",
						    "callbackType" => 'forward',
						    "forwardUrl" => "index.php?exam-app-exam-score"
						);
					}
				}
				exit(json_encode($message));
			}
			else
			{
				if($sessionvars['examsessionstatus'] == 2)
				{
					header("location:index.php?exam-app-exam-makescore");
					exit;
				}
				else
				{
					$this->tpl->assign('sessionvars',$sessionvars);
					$this->tpl->assign('questype',$questype);
					$this->tpl->display('exam_mkscore');
				}
			}
			break;

			case 'paper':
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$lefttime = 0;
			$questype = $this->basic->getQuestypeList();
			if($sessionvars['examsessionstatus'] == 2)
			{
				header("location:index.php?exam-app-exam-makescore");
				exit;
			}
			elseif($sessionvars['examsessionstatus'] == 1)
			{
				header("location:index.php?exam-app-exam-score");
				exit;
			}
			else
			{
				$exams = $this->exam->getExamSettingList(1,3,array("examsubject = '{$this->data['currentsubject']['subjectid']}'","examtype = 1"));
				$this->tpl->assign('questype',$questype);
				$this->tpl->assign('sessionvars',$sessionvars);
				$this->tpl->assign('lefttime',$lefttime);
				$this->tpl->assign('donumber',is_array($sessionvars['examsessionuseranswer'])?count($sessionvars['examsessionuseranswer']):0);
				$this->tpl->display('exam_paper');
			}
			break;

			//试题列表
			case 'sign':
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$questype = $this->basic->getQuestypeList();
			$this->tpl->assign('questype',$questype);
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->display('exam_sign');
			break;

			case 'selectquestions':
			$examid = $this->ev->get('examid');
			$sessionvars = $this->exam->getExamSessionBySessionid();
			$r = $this->exam->getExamSettingById($examid);
			$questions = array();
			$questionrows = array();
			foreach($r['examquestions'] as $key => $p)
			{
				$qids = '';
				$qrids = '';
				if($p['questions'])$qids = trim($p['questions']," ,");
				if($qids)
				$questions[$key] = $this->exam->getQuestionListByArgs("questionid IN ({$qids})");
				if($p['rowsquestions'])$qrids = trim($p['rowsquestions']," ,");
				if($qrids)
				{
					$qrids = explode(",",$qrids);
					foreach($qrids as $t)
					{
						$questionrows[$key][$t] = $this->exam->getQuestionRowsByArgs("qrid = '{$t}'");
					}
				}
			}
			$args['examsessionquestion'] = $this->ev->addSlashes(serialize(array('questions'=>$questions,'questionrows'=>$questionrows)));
			$args['examsessionsetting'] = $this->ev->addSlashes(serialize($r));
			$args['examsessionstarttime'] = TIME;
			$args['examsession'] = $r['exam'];
			$args['examsessionscore'] = 0;
			$args['examsessionuseranswer'] = NULL;
			$args['examsessionscorelist'] = NULL;
			$args['examsessionsign'] = NULL;
			$args['examsessiontime'] = $r['examsetting']['examtime'];
			$args['examsessionstatus'] = 0;
			$args['examsessiontype'] = 2;
			$args['examsessionkey'] = $r['examid'];
			$args['examsessionissave'] = 0;
			$args['examsessionbasic'] = $this->data['currentbasic']['basicid'];
			if($sessionvars['examsessionid'])
			$this->exam->modifyExamSession($args);
			else
			$this->exam->insertExamSession($args);
			header("location:?exam-app-exam-paper");
			exit;
			break;

			//显示考试须知等信息
			default:
			$page = $this->ev->get('page');
			$ids = trim($this->data['currentbasic']['basicexam']['self'],', ');
			if(!$ids)$ids = '0';
			$exams = $this->exam->getExamSettingList($page,20,array("examid IN ({$ids})","examtype = 2"));
			$this->tpl->assign('exams',$exams);
			$this->tpl->display('exam');
		}
	}

	public function text()
	{
		$action = $this->ev->url(3);
		switch($action)
		{
			//公告等信息正文
			case 'text':
			$infoid = $this->ev->get('infoid');
			$info = $this->info->getInfoById($infoid);
			$this->tpl->assign('info',$info);
			$this->tpl->display('gg');
			break;

			//公告列表页
			case 'textlist':
			$page = $this->ev->get('page');
			$page = $page > 0?$page : 1;
			$area = array();
			foreach($this->userexams as $p)
			{
				$area[] = $p['areaid'];
			}
			$areaids = trim(implode(',',$area)," ,");
			$areaids = "28,".$areaids;
			if(!$areaids)$areaids = 0;
			$this->tpl->assign('annous',$this->info->getInfoList($page,20,array("infoareaid IN ({$areaids})","infocategoryid = 24")));
			$this->tpl->display('gglist');
			break;

			default:
			$this->index();
			break;
		}
	}

	//错题记录
	public function record()
	{
		$examid = $this->ev->get('examid');
		if(!$examid)$examid = $this->_user['sessioncurrent'];
		if(!in_array($examid,$this->examids))
		{
			$t = current($this->userexams);
			$examid = $t['a2sexamid'];
		}
		$this->tpl->assign('examid',$examid);
		$action = $this->ev->url(3);
		switch($action)
		{

			case 'ajax':
			switch($this->ev->url(4))
			{
				//删除一个错题记录
				case 'delrecord':
				$recordid = $this->ev->get('questionid');
				$this->favor->delRecord($recordid);
				exit('1');
				break;

				default:
				break;
			}
			break;

			case 'wrongs':
			$ehid = $this->ev->get('ehid');
			$eh = $this->favor->getExamHistoryById($ehid);
			$questype = $this->basic->getQuestypeList();
			$sessionvars = array('examsession'=>$eh['ehexam'],'examsessionscorelist'=>$eh['ehscorelist'],'examsessionsetting'=>$eh['ehsetting'],'examsessionquestion'=>$eh['ehquestion'],'examsessionuseranswer'=>$eh['ehuseranswer']);
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->assign('questype',$questype);
			if($eh['ehtype'] == 2)
			$this->tpl->display('history_examwrongs');
			elseif($eh['ehtype'] == 1)
			$this->tpl->display('history_exampaperwrongs');
			else
			$this->tpl->display('history_exercisewrongs');
			break;

			//错题记录列表
			default:
			$page = $this->ev->get('page');
			$ehtype = intval($this->ev->get('ehtype'));
			$page = $page > 0?$page:1;
			$questype = $this->basic->getQuestypeList();
			$basicid = $this->data['currentbasic']['basicid'];
			$args = array("ehuserid = '".$this->_user['sessionuserid']."'","ehbasicid = '{$basicid}'","ehtype = '{$ehtype}'","ehstatus = 1");
			$exams = $this->favor->getExamHistoryListByArgs($page,10,$args);
			foreach($exams['data'] as $key => $p)
			{
				$exams['data'][$key]['errornumber'] = 0;
				$questions = unserialize($p['ehquestion']);
				$scorelist = unserialize($p['ehscorelist']);
				$examsetting = unserialize($p['ehsetting']);
				if(is_array($questions['questions']) && is_array($scorelist))
				{
					foreach($questions['questions'] as $nkey => $q)
					{
						if(is_array($q))
						foreach($q as $qid => $t)
						if($p['ehtype'] == 0)
						{
							if($scorelist[$qid] != 1)$exams['data'][$key]['errornumber']++;
						}
						elseif($p['ehtype'] == 1)
						{
							if($scorelist[$qid] != $examsetting['examsetting']['questype'][$nkey]['score'])$exams['data'][$key]['errornumber']++;
						}
						else
						{
							if($scorelist[$qid] != $examsetting['examsetting']['questype'][$nkey]['score'])$exams['data'][$key]['errornumber']++;
						}
					}
				}
			}
			$avgscore = floatval($this->favor->getAvgScore($args));
			$this->tpl->assign('ehtype',$ehtype);
			$this->tpl->assign('page',$page);
			$this->tpl->assign('exams',$exams);
			$this->tpl->assign('avgscore',$avgscore);
			$this->tpl->display('record');
			break;
		}
	}

	public function favor()
	{
		$examid = $this->ev->get('examid');
		if(!$examid)$examid = $this->_user['sessioncurrent'];
		if(!in_array($examid,$this->examids))
		{
			$t = current($this->userexams);
			$examid = $t['a2sexamid'];
		}
		$this->tpl->assign('examid',$examid);
		$action = $this->ev->url(3);
		switch($action)
		{
			case 'ajax':
			switch($this->ev->url(4))
			{
				//添加一个收藏
				case 'addfavor':
				$questionid = $this->ev->get('questionid');
				if($this->favor->getFavorByQuestionAndUserId($questionid,$this->_user['sessionuserid']))
				{
					exit('0');
				}
				else
				{
					$this->favor->favorQuestion($questionid,$this->_user['sessionuserid'],$this->data['currentbasic']['basicsubjectid']);
					exit('1');
				}
				break;

				//删除一个收藏
				case 'delfavor':
				$favorid = $this->ev->get('questionid');
				$this->favor->delFavorById($favorid);
				exit('1');
				break;

				default:
				break;
			}
			break;

			//收藏试题列表
			default:
			$page = $this->ev->get('page');
			$type = $this->ev->get('type');
			$search = $this->ev->get('search');
			$tmp = $this->section->getKnowsListByArgs(array("knowssectionid = '{$search['sectionid']}'","knowsstatus = 1"));
			if($search['sectionid'] && !$search['knowsid'])
			{
				$search['knowsid'] = '';
				if(is_array($tmp))
				{
					foreach($tmp as $p)
					$search['knowsid'] .= $p['knowsid'].",";
				}
			}
			$search['knowsid'] = trim($search['knowsid']," ,");
			$page = $page > 0?$page:1;
			$args = array("favorsubjectid = '{$this->data['currentbasic']['basicsubjectid']}'","favoruserid = '{$this->_user['sessionuserid']}'");
			if($search['knowsid'])$args[] = "quest2knows.qkknowsid IN ({$search['knowsid']})";
			if($type)
			{
				if($search['questype'])$args[] = "questionrows.qrtype = '{$search['questype']}'";
				$favors = $this->favor->getFavorListByUserid($page,20,$args,1);
			}
			else
			{
				if($search['questype'])$args[] = "questions.questiontype = '{$search['questype']}'";
				$favors = $this->favor->getFavorListByUserid($page,20,$args);
			}
			$sections = $this->section->getSectionListByArgs("sectionsubjectid = '{$this->_user['sessioncurrent']}'");
			$questype = $this->basic->getQuestypeList();
			$this->tpl->assign('sections',$sections);
			$this->tpl->assign('questype',$questype);
			$this->tpl->assign('search',$search);
			$this->tpl->assign('knowsids',$tmp);
			$this->tpl->assign('page',$page);
			$this->tpl->assign('type',$type);
			$this->tpl->assign('favors',$favors);
			$this->tpl->display('favor');
			break;
		}
	}

	//考试历史记录
	public function history()
	{
		$action = $this->ev->url(3);
		switch($action)
		{
			//删除考试历史记录
			case 'del':
			$ehid = $this->ev->get('ehid');
			$ehtype = $this->ev->get('ehtype');
			$page = $this->ev->get('page');
			$this->favor->delExamHistory($ehid,$this->_user['sessionuserid']);
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
			    "callbackType" => 'forward',
			    "forwardUrl" => "?exam-app-history&ehtype={$ehtype}&page={$page}"
			);
			exit(json_encode($message));
			break;

			//批量删除强化训练历史记录
			case 'batdelexercise':
			$exercise = $this->ev->get('exercise');
			foreach($exercise as $p)
			$this->favor->delExamHistory($p,$this->_user['sessionuserid']);
			header("location:?exam-app-history");
			exit;
			break;

			//批量删除模拟考试历史记录
			case 'batdelexam':
			$exam = $this->ev->get('exam');
			foreach($exam as $p)
			$this->favor->delExamHistory($p,$this->_user['sessionuserid']);
			header("location:?exam-app-history");
			exit;
			break;

			//查看历史记录列表
			case 'view':
			$ehid = $this->ev->get('ehid');
			$eh = $this->favor->getExamHistoryById($ehid);
			$questype = $this->basic->getQuestypeList();
			$sessionvars = array('examsession'=>$eh['ehexam'],'examsessionscore'=>$eh['ehscore'],'examsessionscorelist'=>$eh['ehscorelist'],'examsessionsetting'=>$eh['ehsetting'],'examsessionquestion'=>$eh['ehquestion'],'examsessionuseranswer'=>$eh['ehuseranswer']);
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->assign('questype',$questype);
			if($eh['ehtype'] == 2)
			$this->tpl->display('history_examview');
			elseif($eh['ehtype'] == 1)
			$this->tpl->display('history_exampaperview');
			else
			$this->tpl->display('history_exerciseview');
			break;

			//查看历史记录列表
			case 'wrongs':
			$ehid = $this->ev->get('ehid');
			$eh = $this->favor->getExamHistoryById($ehid);
			$questype = $this->basic->getQuestypeList();
			$sessionvars = array('examsession'=>$eh['ehexam'],'examsessionscorelist'=>$eh['ehscorelist'],'examsessionsetting'=>$eh['ehsetting'],'examsessionquestion'=>$eh['ehquestion'],'examsessionuseranswer'=>$eh['ehuseranswer']);
			$this->tpl->assign('sessionvars',$sessionvars);
			$this->tpl->assign('questype',$questype);
			if($eh['ehtype'] == 2)
			$this->tpl->display('history_examwrongs');
			elseif($eh['ehtype'] == 1)
			$this->tpl->display('history_exampaperwrongs');
			else
			$this->tpl->display('history_exercisewrongs');
			break;

			//以该记录题目重新出卷
			case 'redo':
			$ehid = $this->ev->get('ehid');
			$eh = $this->favor->getExamHistoryById($ehid);
			$args = array(
							'examsession' => $eh['ehexam'].'重做',
							'examsessiontype'=>$eh['ehtype'],
							'examsessionsetting'=>$this->ev->addSlashes(serialize($eh['ehsetting'])),
							'examsessionbasic'=>$eh['ehbasicid'],
							'examsessionquestion'=>$this->ev->addSlashes(serialize($eh['ehquestion'])),
							'examsessionuseranswer'=>'',
							'examsessiontime'=>$eh['ehtime'],
							'examsessionscorelist'=>'',
							'examsessionscore'=>0,
							'examsessionstarttime'=>TIME,
							'examsessionissave'=> 0,
							'examsessionstatus'=> 0
						);
			$es = $this->exam->getExamSessionBySessionid();
			if($es['examsessionid'])
			{
				$this->exam->modifyExamSession($args);
			}
			else
			{
				$this->exam->insertExamSession($args);
			}
			if($eh['ehtype'] == 1)
			header("location:?exam-app-exampaper-paper&act=history&examid={$eh['ehkey']}");
			elseif($eh['ehtype'] == 2)
			header("location:?exam-app-exam-paper&act=history&examid={$eh['ehkey']}");
			else
			header("location:?exam-app-exercise-paper&act=history&knowsid={$eh['ehkey']}");
			exit;
			break;

			//答题记录列表
			default:
			$page = $this->ev->get('page');
			$ehtype = intval($this->ev->get('ehtype'));
			$page = $page > 0?$page:1;
			$basicid = $this->data['currentbasic']['basicid'];
			$exams = $this->favor->getExamHistoryListByArgs($page,10,array("ehuserid = '".$this->_user['sessionuserid']."'","ehbasicid = '{$basicid}'","ehtype = '{$ehtype}'"));
			$avgscore = floatval($this->favor->getAvgScore(array("ehuserid = '".$this->_user['sessionuserid']."'","ehbasicid = '{$basicid}'","ehtype = '{$ehtype}'")));
			$this->tpl->assign('ehtype',$ehtype);
			$this->tpl->assign('page',$page);
			$this->tpl->assign('exams',$exams);
			$this->tpl->assign('avgscore',$avgscore);
			$this->tpl->display('history');
			break;
		}
	}

	public function answer()
	{
		$action = $this->ev->url(3);
		switch($action)
		{
			case 'ajax':
			switch($this->ev->url(4))
			{
				//根据章节获取知识点
				case 'getknowsbysectionid':
				$esid = $this->ev->get('esid');
				$knowsid = $this->ev->get('knowsid');
				$aknows = $this->section->getKnowsListByArgs(array("knowssectionid = '{$esid}'","knowsstatus = 1"));
				echo "<option value='0'>全部</option>";
				foreach($aknows as $knows)
				{
					if($knowsid == $knows['knowsid'])
					echo "<option value='{$knows['knowsid']}' selected>{$knows['knows']}</option>";
					else
					echo "<option value='{$knows['knowsid']}'>{$knows['knows']}</option>";
					echo "\n";
				}
				exit();
				break;

				//根据科目获取章节
				case 'getsectionsbysubjectid':
				$esid = $this->ev->get('subjectid');
				$knowsid = intval($this->ev->get('knowsid'));
				if($knowsid)
				$tmp = $this->section->getSubjectAndSectionByKnowsid($knowsid);
				else
				{
					$tmp['knowssectionid'] = intval($this->ev->get('sectionid'));
				}
				$aknows = $this->section->getSectionListByArgs(array("sectionsubjectid = '{$esid}'"));
				echo "<option value='0'>全部</option>";
				foreach($aknows as $knows)
				{
					if($tmp['knowssectionid'] == $knows['sectionid'])
					echo "<option value='{$knows['sectionid']}' selected>{$knows['section']}</option>";
					else
					echo "<option value='{$knows['sectionid']}'>{$knows['section']}</option>";
					echo "\n";
				}
				exit();
				break;

				default:
				break;
			}
			break;

			//批量删除提问
			case 'batdel':
			$askids = $this->ev->get('askids');
			foreach($askids as $id)
			{
				$this->answer->delAsksById($id);
			}
			header("location:?exam-app-answer");
			exit;
			break;

			//显示提问的所有追问列表
			case 'ask':
			$askid = $this->ev->get('askid');
			$page = $this->ev->get('page');
			if(!$askid)
			{
				$questionid = $this->ev->get('questionid');
				if(!$questionid)
				{
					header("location:?exam-app-answer");
					exit;
				}
				else
				$ask = $this->answer->getAskByArgs(array("askuserid = '{$this->_user['sessionuserid']}'","askquestionid = '{$questionid}'"));
				if(!$ask)$ask = array('askquestionid' => $questionid);
			}
			else
			$ask = $this->answer->getAskById($askid);
			$question = $this->exam->getQuestionByArgs("questionid = '{$ask['askquestionid']}'");
			if($ask['askid'])
			{
				$this->tpl->assign('allknows',$this->section->getAllKnowsBySubject($ask['asksubjectid']));
				$this->tpl->assign('answers',$this->answer->getAnswerList($page,20,array("answeraskid = '{$ask['askid']}'")));
			}
			else
			{
				$tmp = $this->section->getSubjectAndSectionByKnowsid($question['questionknowsid']);
				$this->tpl->assign('allknows',$this->section->getAllKnowsBySubject($tmp['sectionsubjectid']));
			}
			$knows = $this->section->getKnowsByArgs("knowsid = '{$question['questionknowsid']}'");
			$this->tpl->assign('knows',$knows);
			$this->tpl->assign('question',$question);
			$this->tpl->assign('ask',$ask);
			$this->tpl->display('ask');
			break;

			//添加一个追问
			case 'addanswer':
			$questionid = $this->ev->get('questionid');
			$args = $this->ev->get('args');
			if(!$questionid)
			{
				header("location:index.php?exam-app-answer");
				exit;
			}
			else
			{
				$ask = $this->answer->getAskByArgs(array("askuserid = '{$this->_user['sessionuserid']}'","askquestionid = '{$questionid}'"));
				if(!$ask)
				{
					$question = $this->exam->getQuestionByArgs("questionid = '{$questionid}'");
					$tmp = $this->section->getSubjectAndSectionByKnowsid($question['questionknowsid']);
					$askargs = array('asksubjectid'=>$tmp['sectionsubjectid'],'askquestionid'=>$questionid,'askuserid'=>$this->_user['sessionuserid'],'asktime'=>TIME,'asklasttime'=>TIME);
					$id = $this->answer->insertAsks($askargs);
					$ask = $this->answer->getAskById($id);
				}
				$args['answeraskid'] = $ask['askid'];
				$args['answerasktime'] = TIME;
				$this->answer->insertAnswer($args);
				header('location:?exam-app-answer-ask&askid='.$ask['askid']);
				exit;
			}
			break;

			//提问问题列表，可根据需求查询
			default:
			$page = $this->ev->get('page');
			$knowsid = $this->ev->get('knowsid');
			$sectionid = $this->ev->get('sectionid');
			$status = $this->ev->get('status');
			$etime = $this->ev->get('etime');
			$subjectid = $this->ev->get('subjectid');
			if($etime)$etime = strtotime($etime);
			$subjects = $this->basic->getSubjectList();
			$args = array("asks.askdel = 0");
			$args[] = "asks.askquestionid = questions.questionid";
			$args[] = "asks.askuserid = '{$this->_user['sessionuserid']}'";
			if($knowsid)$args[] = "questions.questionknowsid = '{$knowsid}'";
			else
			{
				if($sectionid)
				{
					$knowsids = $this->section->getKnowsListByArgs("knowssectionid = '{$sectionid}'");
					$tm = array();
					foreach($knowsids as $p)
					{
						$tm[] = $p['knowsid'];
					}
					$ids = implode(",",$tm);
					if(!$ids)$ids = 0;
					$args[] = "questions.questionknowsid IN ({$ids})";
				}
				elseif($subjectid)
				{
					$args[] = "asks.asksubjectid = '{$subjectid}'";
				}
			}
			if($etime)$args[] = "asks.asklasttime = '{$etime}'";
			if($status)
			{
				if($status == 1)$args[] = "asks.askstatus = '1'";
				else
				$args[] = "asks.askstatus = '0'";
			}
			$asks = $this->answer->getAskList($page,20,$args);
			$this->tpl->assign('knowsid',$knowsid);
			$this->tpl->assign('status',$status);
			$this->tpl->assign('etime',$etime);
			$this->tpl->assign('asks',$asks);
			$this->tpl->assign('subjectid',$subjectid);
			$this->tpl->assign('subjects',$subjects);
			$this->tpl->assign('sectionid',$sectionid);
			$this->tpl->display('wddy');
		}
	}
}

?>
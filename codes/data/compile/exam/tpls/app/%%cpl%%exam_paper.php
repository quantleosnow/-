<?php $this->_compileInclude('head'); ?>
<body onbeforeunload="javascript:saveanswer(false);">
<style>label.inline{width:24px;}</style>
<?php $this->_compileInclude('nav'); ?>
<div id="main">
	<!--主体左侧-->
	<?php $this->tpl_var['nouseleft'] = 1; ?>
	<?php $this->_compileInclude('left'); ?>
	<!--主体左侧 结束-->
	<!--主体右侧 -->
	<div id="right_760" class="right_970">
    	<?php $this->_compileInclude('bread'); ?>
    	<div class="bor_top"></div>
    	<div class="bor_mid">
    		<div id="hide_left"><a href="javascript:pr()"></a></div>
            <div id="exam_paper">
            <form action="?exam-app-exam-score" id="form1" name="form1" method="post">
            <!-- float start-->
            <div id="float" class="float_div1">
            	<h2 class="page_title" style="line-height:0.5em;">
                	<ul id="func_list">
               	  	    <li id="show_menu" class="menu">
               	  	      <div class="menu-hd"><span id="show-current"><a class="collapsible" href="#show_sz">显示设置</a></span></div>
               	  	      <div id="show_sz" class="menu-bd" style="display:none;">
               	  	        <dl>
               	  	          <dd><a href="#show_dt" onClick="javascript:initquestion();">单题显示</a></dd>
               	  	          <dd><a href="#show_tx" onClick="javascript:inittype();">题型显示</a></dd>
               	  	          <dd><a href="#show_zj" onClick="javascript:initpaper();">整卷显示</a></dd>
           	  	            </dl>
           	  	          </div>
       	  	      	  	</li>
                  	</ul><img src="app/exam/styles/image/simulation_tit.jpg" alt="模拟考试" />
                    <span id="time_top">考试剩余时间：<b><span id="timer_h">00</span>：<span id="timer_m">00</span>：<span id="timer_s">00</span></b></span>
                    <!--分数-->
                    <div id="fenshu" style="display:none;">
                        <div class="n6"></div>
                        <div class="n4"></div>
                        <div class="ndot"></div>
                    </div>
                    <!--分数 结束-->
                  </h2>
				</div>
				<!-- float end-->
       	    	<h1 style="font-size:18px;"><?php echo $this->tpl_var['sessionvars']['examsession']; ?></h1>
                <h5>总分：<span class="orange"><?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['score']; ?></span>分 合格分数线：<span class="orange"><?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['passscore']; ?></span>分 考试时间：<span class="orange"><?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['examtime']; ?></span>分钟 来源：<?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['comfrom']; ?></h5>
                <?php $oid = 0; ?>
                <?php $qid = 0;
 foreach($this->tpl_var['questype'] as $key => $quest){ 
 $qid++; ?>
                <?php if($this->tpl_var['sessionvars']['examsessionquestion']['questions'][$quest['questid']] || $this->tpl_var['sessionvars']['examsessionquestion']['questionrows'][$quest['questid']]){ ?>
                <?php $oid++; ?>
                <span class="questypearea" id="qtarea_<?php echo $quest['questid']; ?>">
                <h4 class="qu_type"><?php echo $this->tpl_var['ols'][$oid]; ?>、<?php echo $quest['questype']; ?>（<?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['questype'][$quest['questid']]['describe']; ?>）</h4>
                <?php $tid = 0; ?>
                <?php $qnid = 0;
 foreach($this->tpl_var['sessionvars']['examsessionquestion']['questions'][$quest['questid']] as $key => $question){ 
 $qnid++; ?>
                <?php $tid++; ?>
                <div class="qu_content" onMouseOver="this.className='qu_content_hover'" onMouseOut="this.className='qu_content'" id="qtion_<?php echo $question['questionid']; ?>">
                	<h3><span class="float_l"><?php echo $tid; ?>、</span><?php echo html_entity_decode($question['question']); ?></h3>
                    <div>
                    	<?php echo html_entity_decode($this->ev->stripSlashes($question['questionselect'])); ?>
                    </div>
                    <span class="examquestionform" name="formquestion_<?php echo $question['questionid']; ?>" id="formquestion_<?php echo $question['questionid']; ?>" rel="nodo">
                    <div class="qu_option" onMouseOver="this.className='qu_option_hover'" onMouseOut="this.className='qu_option'">
                        <?php if($quest['questsort']){ ?>
                        <span class="font_12 float_r cz">【<a href="javascript:favorquestion('<?php echo $question['questionid']; ?>');">收藏</a>】</span>
                              <p class=" float_l">本题答案：</p>
                        <div id="editor" class="clear">
                        	<?php $this->tpl_var['dataid'] = $question['questionid']; ?>
                        	<?php $this->_compileInclude('plugin_editor'); ?>
                        </div>
                        <?php } else { ?>
                        <span class="font_12 float_r cz">【<a href="javascript:favorquestion('<?php echo $question['questionid']; ?>');">收藏</a>】</span>
						<div class="option_single" id="radio">
                        	<?php if($quest['questchoice'] == 1 || $quest['questchoice'] == 4){ ?>
		                        <?php $sid = 0;
 foreach($this->tpl_var['selectorder'] as $key => $so){ 
 $sid++; ?>
		                        <?php if($key == $question['questionselectnumber']){ ?>
		                        <?php break;; ?>
		                        <?php } ?>
		                        <label class="radio inline"><?php echo $so; ?> <input type="radio" name="question[<?php echo $question['questionid']; ?>]" rel="<?php echo $question['questionid']; ?>" value="<?php echo $so; ?>" <?php if($so == $this->tpl_var['sessionvars']['examsessionuseranswer'][$question['questionid']]){ ?>checked<?php } ?>/></label>&nbsp;&nbsp;
		                        <?php } ?>
	                        <?php } else { ?>
		                        <?php $sid = 0;
 foreach($this->tpl_var['selectorder'] as $key => $so){ 
 $sid++; ?>
		                        <?php if($key >= $question['questionselectnumber']){ ?>
		                        <?php break;; ?>
		                        <?php } ?>
		                        <label class="checkbox inline"><?php echo $so; ?> <input type="checkbox" name="question[<?php echo $question['questionid']; ?>][]" rel="<?php echo $question['questionid']; ?>" value="<?php echo $so; ?>" <?php if(in_array($so,$this->tpl_var['sessionvars']['examsessionuseranswer'][$question['questionid']])){ ?>checked<?php } ?>/></label>&nbsp;&nbsp;
		                        <?php } ?>
	                        <?php } ?>

                        </div>
                        <div class="clear"></div>
                        <?php } ?>
                    </div>
                    </span>
                    <ul id="qu_type_next" class="clear" name="qbar" style="margin:10px auto;display:none">
						<li><a href="javascript:;" onClick="javascript:prevquestion();">上一题</a></li>
	                	<li><a href="javascript:;" onClick="javascript:nextquestion();">下一题</a></li>
                	</ul>
                </div>
                <?php } ?>
                <?php $qrid = 0;
 foreach($this->tpl_var['sessionvars']['examsessionquestion']['questionrows'][$quest['questid']] as $key => $questionrow){ 
 $qrid++; ?>
                <?php $tid++; ?>
                <div class="qu_content" onMouseOver="this.className='qu_content_hover'" onMouseOut="this.className='qu_content'" id="qrtion_<?php echo $questionrow['qrid']; ?>">
                	<h3><?php echo $tid; ?>、<?php echo html_entity_decode($questionrow['qrquestion']); ?></h3>
                	<?php $did = 0;
 foreach($questionrow['data'] as $key => $data){ 
 $did++; ?>
                	<h3>(<?php echo $did; ?>)<?php echo html_entity_decode($data['question']); ?></h3>
                    <ul>
                    	<?php echo html_entity_decode($this->ev->stripSlashes($data['questionselect'])); ?>
                    </ul>
                    <span class="examquestionform" name="formquestion_<?php echo $data['questionid']; ?>" id="formquestion_<?php echo $data['questionid']; ?>" rel="nodo">
                    <div class="qu_option" onMouseOver="this.className='qu_option_hover'" onMouseOut="this.className='qu_option'">
                        <?php if($quest['questsort']){ ?>
                        <span class="font_12 float_r cz">【<a href="javascript:favorquestion('<?php echo $data['questionid']; ?>');">收藏</a>】</span>
                        <p class=" float_l">本题答案：</p>
                        <div id="editor" class="clear">
                        	<?php $this->tpl_var['dataid'] = $data['questionid']; ?>
                        	<?php $this->_compileInclude('plugin_editor'); ?>
                        </div>
                        <?php } else { ?>
                        <span class="font_12 float_r cz">【<a href="javascript:favorquestion('<?php echo $data['questionid']; ?>');">收藏</a>】</span>
                        <div class="option_single" id="radio">
                        	<?php if($quest['questchoice'] == 1 || $quest['questchoice'] == 4){ ?>
                            <?php $sid = 0;
 foreach($this->tpl_var['selectorder'] as $key => $so){ 
 $sid++; ?>
                            <?php if($key == $data['questionselectnumber']){ ?>
                            <?php break;; ?>
                            <?php } ?>
                            <label class="radio inline"><input autocomplete="off" type="radio" name="question[<?php echo $data['questionid']; ?>]" value="<?php echo $so; ?>" /><?php echo $so; ?></label>
                            <?php } ?>
                            <?php } else { ?>
                            <?php $sid = 0;
 foreach($this->tpl_var['selectorder'] as $key => $so){ 
 $sid++; ?>
                            <?php if($key >= $data['questionselectnumber']){ ?>
                            <?php break;; ?>
                            <?php } ?>
                            <label class="checkbox inline"><input autocomplete="off" type="checkbox" name="question[<?php echo $data['questionid']; ?>][<?php echo $key; ?>]" value="<?php echo $so; ?>" /><?php echo $so; ?></label>
                            <?php } ?>
                            <?php } ?>
							<script type="text/javascript"><?php if($quest['questchoice'] == 2 || $quest['questchoice'] == 3){ ?>completeAnswers('question[<?php echo $data['questionid']; ?>][]','<?php echo implode('',$this->tpl_var['sessionvars']['examsessionuseranswer'][$data['questionid']]);; ?>');<?php } else { ?>completeAnswers('question[<?php echo $data['questionid']; ?>]','<?php echo $this->tpl_var['sessionvars']['examsessionuseranswer'][$data['questionid']]; ?>');<?php } ?></script>
                        </div>
                        <div class="clear"></div>
                        <?php } ?>
                    </div>
                    </span>
                    <?php } ?>
                    <ul id="qu_type_next" class="clear" name="qbar" style="margin:10px auto;display:none">
						<li><a href="javascript:;" onClick="javascript:prevquestion();">上一题</a></li>
	                	<li><a href="javascript:;" onClick="javascript:nextquestion();">下一题</a></li>
                	</ul>
                </div>
                <?php } ?>
                <ul id="qu_type_next" class="clear" name="qtbar" style="margin:10px auto;display:none">
					<li><a href="javascript:;" onClick="javascript:prevtype();">上一题型</a></li>
                	<li><a href="javascript:;" onClick="javascript:nexttype();">下一题型</a></li>
                </ul>
            	</span>
                <?php } ?>
                <?php } ?>
                <div id="amount">全卷已做 <b class="orange" id="yesdonumber"><?php echo $this->tpl_var['donumber']; ?></b> 题 / 共<b class="orange" id="allquestionnumber">0</b>题 剩余 <b class="orange" id="nodonumber">0</b> 题未作答</div>
                <div id="btn_exam_paper"><input type="hidden" name="insertscore" value="1"/><input type="button" value="" onClick="javascript:subpaper();"/></div>
          	</form>
          	</div>
    	</div>
    	<div class="bor_bottom"></div>
    </div>
	<!--主体右侧 结束-->
	<!--尾部-->
    <?php $this->_compileInclude('foot'); ?>
	<!--尾部 结束-->
    <!--返回顶部-->
    <div id="roll">
      <div id="roll_top"></div>
    </div>
    <!--返回顶部 结束-->
    <!--自动保存提示-->
    <div id="save_prompt">
    	<h4>自动保存提示</h4>
        <p>系统将每隔<b class="red">5分钟</b>自动保存试卷及答案，答题过程中请勿刷新页面。<br />【<a href="index.php?exam-app-exam-sign">查看试题列表</a>】<br />上次保存时间：<span id="lastsavetime"><?php echo date('H:i:s',TIME); ?></span></p>
    </div>
    <!--自动保存提示 结束-->
</div>
<script type="text/javascript">
$(document).ready(function(){
		$.get('index.php?exam-app-index-ajax-lefttime&rand'+Math.random(),function(data){
			var setting = {
				time:<?php echo $this->tpl_var['sessionvars']['examsessiontime']; ?>,
				hbox:$("#timer_h"),
				mbox:$("#timer_m"),
				sbox:$("#timer_s"),
				finish:function(){$('#form1').submit();}
			}
			setting.lefttime = parseInt(data);
			countdown(setting);
		});
		$('#roll').hide();
		$('#allquestionnumber').html($('.examquestionform').length);
		$('#nodonumber').html($('#allquestionnumber').html() - $('#yesdonumber').html());
		common();
		$(":input").bind('blur',recordanswer);
		setInterval(saveanswer,300000);
		$(window).scroll(function() {
			if($(window).scrollTop() >= 100){
				$('#roll').fadeIn(400);
		    }
		    else
		    {
		    $('#roll').fadeOut(200);
		    }
		 });
		 $('#roll_top').click(function(){$('html,body').animate({scrollTop: '0px'}, 800);});
		 recordanswer();
	});
function recordanswer(){
	var yesdonumber = 0;
    $(".qu_option").each(function(){
    	var _i = $("input",this);
    	for(i=0;i< _i.length;i++)
    	{
    		if($(_i.get(i)).attr('checked'))
    		{
    			yesdonumber++;
    			return ;
    		}
    	}
    	var _m = $("textarea",this);
    	if(_m.length == 1)
    	{
    		if(_m.val() && _m.val() != '')yesdonumber++;
    	}
    	else
    	{
    		_m.each(function(){if($(this).val() && $(this).val() != '')yesdonumber++;})
    	}
    });
    $('#yesdonumber').html(yesdonumber);
    $('#nodonumber').html(parseInt($('#allquestionnumber').html()) - parseInt($('#yesdonumber').html()));
}
var subpaper = function()
{
	art.dialog({lock:true,resize:false,width:320,height:150,title:'交卷提醒',ok:function(){$('#form1').submit();},okval:'交卷',cancel:true,cancelval:'取消',content:'您确认要交卷吗？'});
}
function saveanswer(){
	var params = $(':input').serialize();
     $.ajax({
       url:'?exam-app-exam-ajax-saveUserAnswer',
       async:false,
       type:'post',
       dataType:'json',
       data:params
     });
}
</script>
<script type="text/javascript" src="app/exam/styles/js/paperview.js"></script>
</body>
</html>
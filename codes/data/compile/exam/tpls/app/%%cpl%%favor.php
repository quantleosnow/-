<?php $this->_compileInclude('head'); ?>
<body>
<!--导航-->
<?php $this->_compileInclude('nav'); ?>
<div id="main">
	<!--主体左侧-->
	<?php $this->_compileInclude('left'); ?>
	<!--主体左侧 结束-->
	<!--主体右侧 -->
	<div id="right_760" class="right_760">
    	<?php $this->_compileInclude('bread'); ?>
    	<div class="bor_top"></div>
    	<div class="bor_mid">
    		<div id="hide_left"><a href="javascript:pr()"></a></div>
            <div id="exam_paper">
              <div id="answer_note" class="float_div1">
           	  <h2 class="page_title">
               	<ul id="note_type">
                    </ul><img src="app/exam/styles/image/house_tit.jpg" alt="我的收藏" />
                </h2>
                </div>
                <div class="clear"></div>
                  <div id="house">
                  	<form id="searchform" class="form-inline">
                        <span>题型：<select name="search[questype]" style="width:240px;">
                        	<option value="0">请选择题型</option>
                        	<?php $qid = 0;
 foreach($this->tpl_var['questype'] as $key => $quest){ 
 $qid++; ?>
                    		<option value="<?php echo $quest['questid']; ?>"<?php if($this->tpl_var['search']['questype'] == $quest['questid']){ ?> selected<?php } ?>><?php echo $quest['questype']; ?></option>
                    		<?php } ?>
                        </select></span>
                        <input type="hidden" value="<?php echo $this->tpl_var['type']; ?>" name="type" />
                        <input type="button" value="搜 索" id="search_btn" onclick="javascript:window.location='index.php?exam-app-favor&'+$(':input').serialize();"/>
                    </form>
                  </div>
                  <?php if($this->tpl_var['type']){ ?>
                  	<?php $ishead = 0; ?>
                  	<?php $ispre = 0; ?>
                  	<?php $qid = 0;
 foreach($this->tpl_var['favors']['data'] as $key => $question){ 
 $qid++; ?>
                  	<?php if($pre != $question['questionparent']){ ?>
                	<?php $ishead = 0; ?>
                	<?php } ?>
                	<?php if(!$ishead){ ?>
                	<div class="qu_content" onmouseover="this.className='qu_content_hover'" onmouseout="this.className='qu_content'">
                	<h3>题帽：<?php echo html_entity_decode($this->ev->stripSlashes($question['qrquestion'])); ?></h3>
                	</div>
                	<?php } ?>
	               	<div class="qu_content" onmouseover="this.className='qu_content_hover'" onmouseout="this.className='qu_content'">
	                	<h3><?php echo ($this->tpl_var['page']-1)*20+$qid; ?>、<?php echo html_entity_decode($this->ev->stripSlashes($question['question'])); ?></h3>
	                    <ul>
	                    	<?php echo html_entity_decode($this->ev->stripSlashes($question['questionselect'])); ?>
	                    </ul>
	                    <div class="answer">
	                    	<ul>
	                        	<li class="red">【正确答案】<?php echo html_entity_decode($this->ev->stripSlashes($question['questionanswer'])); ?></li>
	                        	<li>【所在章】<?php $kid = 0;
 foreach($question['qrknowsid'] as $key => $knowsid){ 
 $kid++; ?>&nbsp;&nbsp;<?php echo $this->tpl_var['globalsections'][$this->tpl_var['globalknows'][$knowsid['knowsid']]['knowssectionid']]['section']; ?>&nbsp;<?php } ?></li>
                        		<li>【知识点】<?php $kid = 0;
 foreach($question['qrknowsid'] as $key => $knowsid){ 
 $kid++; ?>&nbsp;&nbsp;<?php echo $this->tpl_var['globalknows'][$knowsid['knowsid']]['knows']; ?>&nbsp;<?php } ?></li>
	                        	<li>【答案解析】</li>
	                        	<li class="ml_10"><?php echo html_entity_decode($this->ev->stripSlashes($question['questiondescribe'])); ?></li>
	                        </ul>
	                    </div>
	                </div>
	                <?php $ishead++; ?>
	                <?php $pre=$question['questionparent']; ?>
	                <?php } ?>
                  <?php } else { ?>
	                <?php $qid = 0;
 foreach($this->tpl_var['favors']['data'] as $key => $question){ 
 $qid++; ?>
	               	<div class="qu_content" onmouseover="this.className='qu_content_hover'" onmouseout="this.className='qu_content'">
	                	<h3><?php echo ($this->tpl_var['page']-1)*20+$qid; ?>、<?php echo html_entity_decode($this->ev->stripSlashes($question['question'])); ?></h3>
	                    <ul>
	                    	<?php echo html_entity_decode($this->ev->stripSlashes($question['questionselect'])); ?>
	                    </ul>
	                    <div class="answer">
	                    	<ul>
	                        	<li class="red">【正确答案】<?php echo html_entity_decode($this->ev->stripSlashes($question['questionanswer'])); ?></li>
	                        	<li>【所在章】<?php $kid = 0;
 foreach($question['questionknowsid'] as $key => $knowsid){ 
 $kid++; ?>&nbsp;&nbsp;<?php echo $this->tpl_var['globalsections'][$this->tpl_var['globalknows'][$knowsid['knowsid']]['knowssectionid']]['section']; ?>&nbsp;<?php } ?></li>
                        		<li>【知识点】<?php $kid = 0;
 foreach($question['questionknowsid'] as $key => $knowsid){ 
 $kid++; ?>&nbsp;&nbsp;<?php echo $this->tpl_var['globalknows'][$knowsid['knowsid']]['knows']; ?>&nbsp;<?php } ?></li>
	                        	<li>【答案解析】</li>
	                        	<li class="ml_10"><?php echo html_entity_decode($this->ev->stripSlashes($question['questiondescribe'])); ?></li>
	                        </ul>
	                    </div>
	                </div>
	                <?php } ?>
                <?php } ?>
                <div class="pagination pagination-right">
		            <ul><?php echo $this->tpl_var['favors']['pages']; ?></ul>
		        </div>
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
</div>
<script>
$(document).ready(function(){
		$('#roll').hide();
		common();
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
	});
</script>
<script src="app/exam/styles/js/lib.js" type="text/javascript"></script>
</body>
</html>
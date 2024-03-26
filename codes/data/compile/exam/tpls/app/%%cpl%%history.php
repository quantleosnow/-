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
  	  	  <div id="answer_note">
                <h2 class="page_title">
       	    	  <ul id="note_type">
                    	<li><a href="?exam-app-history"<?php if($this->tpl_var['ehtype'] == 0){ ?> class="on"<?php } ?>>强化训练</a></li>
                    	<li><a href="?exam-app-history&ehtype=1"<?php if($this->tpl_var['ehtype'] == 1){ ?> class="on"<?php } ?>>随机考试</a></li>
                    	<li><a href="?exam-app-history&ehtype=2"<?php if($this->tpl_var['ehtype'] == 2){ ?> class="on"<?php } ?>>模拟考试</a></li>
                    </ul><img src="app/exam/styles/image/answer_note_tit.jpg" alt="答题记录" /></h2>
	   			<?php if($this->tpl_var['ehtype'] == 0){ ?>
	   			<div id="note_type_tit"><span class="font_12 float_r"><span class="red">提示：</span>答题记录中的强化训练最多记录最新20条信息。</span><b>强化训练</b></div>
				<?php } elseif($this->tpl_var['ehtype'] == 1){ ?>
				<div id="note_type_tit"><span class="font_12 float_r">您一共完成了<b class="red"><?php echo $this->tpl_var['exams']['number']; ?></b>次考试， 平均分为：<b class="red"><?php echo $this->tpl_var['avgscore']; ?></b>分 继续努力吧！</span><b>随机考试</b></div>
				<?php } elseif($this->tpl_var['ehtype'] == 2){ ?>
				<div id="note_type_tit"><span class="font_12 float_r">您一共完成了<b class="red"><?php echo $this->tpl_var['exams']['number']; ?></b>次考试， 平均分为：<b class="red"><?php echo $this->tpl_var['avgscore']; ?></b>分 继续努力吧！</span><b>模拟考试</b></div>
				<?php } ?>
				<table width="100%">
                        <tr>
                          <th>答题记录</th>
                          <th>答题时间</th>
                          <th>用时</th>
                          <th>得分</th>
                          <th>操作</th>
                        </tr>
                        <?php $eid = 0;
 foreach($this->tpl_var['exams']['data'] as $key => $exam){ 
 $eid++; ?>
                        <tr>
                          <td align="left"><?php echo $exam['ehexam']; ?></td>
                          <td><?php echo date('Y-m-d',$exam['ehstarttime']); ?></td>
                          <td><?php echo $exam['ehtime']; ?>分钟</td>
                          <td><b class="red"><?php if(!$exam['ehstatus'] && $exam['ehdecide']){ ?>评分中<?php } else { ?><?php echo $exam['ehscore']; ?><?php } ?></b></td>
                          <td class="mani_btn">
                            <?php if($exam['ehstatus']){ ?>
                            <a href="?exam-app-history-view&ehid=<?php echo $exam['ehid']; ?>">解析</a>
                            <a href="?exam-app-history-redo&ehid=<?php echo $exam['ehid']; ?>">重做</a>
                            <a href="?exam-app-history-wrongs&ehid=<?php echo $exam['ehid']; ?>">错题</a>
                            <a href="javascript:;" onclick="javascript:confdelinfo(<?php echo $exam['ehid']; ?>)">删除</a>
                            <?php } else { ?>
                            本试卷需教师评分，请等待
                            <?php } ?>
                          </td>
                        </tr>
                        <?php } ?>
                     </table>
                     <?php if($this->tpl_var['exams']['pages'] && $this->tpl_var['ehtype']){ ?>
                        <div class="pagination pagination-right">
				            <ul><?php echo $this->tpl_var['exams']['pages']; ?></ul>
				        </div>
                     <?php } ?>
            </div>
    	</div>
    	<div class="bor_bottom"></div>
    </div>
	<!--主体右侧 结束-->
	<!--尾部-->
    <?php $this->_compileInclude('foot'); ?>
    <!--尾部 结束-->
</div>
<script>
var delhistory = function(ehid)
{
 	$.get("?exam-app-history-del&ehid="+ehid+"&rand"+Math.random(),function(data){window.location.reload();});
}
var confdelinfo = function(url)
{
	art.dialog({
		ok: function(){delhistory(url);},
		okval: '确定',
		title:'删除答题记录',
		cancel: true,
		cancelval: '取消',
		content: '您确定要删除这个记录吗？'
	});
}
</script>
</body>
</html>
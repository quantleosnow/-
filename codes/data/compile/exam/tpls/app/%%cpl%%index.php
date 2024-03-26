<?php $this->_compileInclude('head'); ?>
<body>
<!--导航-->
<?php $this->_compileInclude('nav'); ?>
<div id="main">
	<?php $this->_compileInclude('left'); ?>
	<!--主体右侧 -->
	<div class="right_760">
    	<?php $this->_compileInclude('bread'); ?>
    	<div class="bor_top"></div>
    	<div class="bor_mid">
            <div id="clue">
            	<div id="clue_bor_top"></div>
            	<div id="clue_content">
                	<h3>友情提示</h3>
                    <p>今天是<span class="orange"><?php echo date('Y',TIME); ?></span>年<span class="orange"><?php echo date('m',TIME); ?></span>月<span class="orange"><?php echo date('d',TIME); ?></span>日 <span class="orange">星期<?php $wk = date('w'); ?><?php if($wk){ ?><?php echo $this->tpl_var['ols'][$wk]; ?><?php } else { ?>日<?php } ?></span><br />请您合理安排工作学习时间，祝您考试顺利。</p>
                </div>
            	<div id="clue_bor_bottom"></div>
            </div>
            <ul id="intro">
                <li><b>全真模拟：</b>全面模拟机考流程，给考生最贴近实际的机考体验。</li>
                <li><b>名师题库：</b>顶级名师团队精心编写，题型全面，覆盖各类考点。</li>
                <li><b>全新体验：</b>全新UI设计和交互体验，锻炼考生操作能力和速度。</li>
                <li id="begin_exam"><a href="index.php?exam-app-exampaper"><img src="app/exam/styles/image/btn_begin_exam.jpg" /></a> <a href="index.php?exam-app-basics-open"><img src="app/exam/styles/image/btn_open_basic.jpg" /></a></li>
            </ul>
    	</div>
    	<div class="bor_bottom"></div>
    </div>
	<!--主体右侧 结束-->
<?php $this->_compileInclude('foot'); ?>
</body>
</html>

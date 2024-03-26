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
            <div id="notice">
            	<h2 class="page_title"><img src="app/exam/styles/image/exam_notice_tit.jpg" alt="考试须知" /></h2>
                <ul>
                	<li><b>1、</b>点击“开始考试”按钮进入答题界面，考试开始计时。</li>
                	<li><b>2、</b>在随机考试过程中，您可以通过顶部的考试时间来掌握自己的做题时间。</li>
                	<li><b>3、</b>提交试卷后，可以通过“查看答案和解析”功能进行总结学习。</li>
                	<li><b>4、</b>系统自动记录考试的考试记录，学员考试结束后可以进入“答题记录”功能进行查看。</li>
                	<li>&nbsp;</li>
                </ul>
            </div>
            <div class="container-fluid">
				<div class="row-fluid">
					<div class="span12">
			            <ul class="thumbnails">
							<?php $eid = 0;
 foreach($this->tpl_var['exams']['data'] as $key => $exam){ 
 $eid++; ?>
							<li class="span4" style="margin:0.25em;">
								<div class="thumbnail">
									<img alt="300x200" src="app/exam/styles/image/paper.png" style="width:160px;"/>
									<div class="caption">
										<p class="text-center">
											<a class="btn btn-primary" href="index.php?exam-app-exam-selectquestions&examid=<?php echo $exam['examid']; ?>" title="<?php echo $exam['exam']; ?>"><?php echo $this->G->make('strings')->subString($exam['exam'],28); ?></a>
										</p>
									</div>
								</div>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="pagination pagination-right">
					<ul><?php echo $this->tpl_var['basics']['pages']; ?></ul>
		        </div>
			</div>
    	</div>
    	<div class="bor_bottom"></div>
    </div>
	<!--主体右侧 结束-->
	<!--尾部-->
	<?php $this->_compileInclude('foot'); ?>
    <!--尾部 结束-->
</div>
</body>
</html>
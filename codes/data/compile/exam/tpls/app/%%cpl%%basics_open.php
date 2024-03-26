<?php if(!$this->tpl_var['userhash']){ ?>
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
            	<h2 class="page_title"><img src="app/exam/styles/image/exam_notice_basic.jpg" alt="开通考场" /></h2>
            </div>
            <div class="container-fluid" id="datacontent">
<?php } ?>
				<div class="row-fluid">
					<form action="index.php?exam-app-basics-open" method="post">
						<table class="table">
							<thead>
				                <tr>
							        <th colspan="5">搜索</th>
				                </tr>
				            </thead>
							<tr>
								<td>
									是否免费：
								</td>
								<td>
									<select name="search[basicdemo]" class="input-medium">
										<option value="0">不限</option>
										<option value="1"<?php if($this->tpl_var['search']['basicdemo']){ ?> selected<?php } ?>>免费</option>
									</select>
								</td>
								<td>
									关键字：
								</td>
								<td>
									<input class="input-medium" name="search[keyword]" type="text" value="<?php echo $this->tpl_var['search']['keyword']; ?>"/>
								</td>
								<td></td>
							</tr>
					        <tr>
								<td>
									地区：
								</td>
					        	<td>
					        		<select name="search[basicareaid]" class="input-medium">
						        		<option value="0">选择地区</option>
								  		<?php $aid = 0;
 foreach($this->tpl_var['areas'] as $key => $area){ 
 $aid++; ?>
								  		<option value="<?php echo $area['areaid']; ?>"<?php if($area['areaid'] == $this->tpl_var['search']['basicareaid']){ ?> selected<?php } ?>><?php echo $area['area']; ?></option>
								  		<?php } ?>
							  		</select>
					        	</td>
								<td>
									科目：
								</td>
								<td>
					        		<select name="search[basicsubjectid]" class="input-medium">
						        		<option value="0">选择科目</option>
								  		<?php $sid = 0;
 foreach($this->tpl_var['subjects'] as $key => $subject){ 
 $sid++; ?>
								  		<option value="<?php echo $subject['subjectid']; ?>"<?php if($subject['subjectid'] == $this->tpl_var['search']['basicsubjectid']){ ?> selected<?php } ?>><?php echo $subject['subject']; ?></option>
								  		<?php } ?>
							  		</select>
					        	</td>
								<td>
									<button class="btn btn-primary" type="submit">提交</button>
								</td>
							</tr>
						</table>
						<div class="input">
							<input type="hidden" value="1" name="search[argsmodel]" />
						</div>
					</form>
					<div class="span12">
			            <ul class="thumbnails">
							<?php $bid = 0;
 foreach($this->tpl_var['basics']['data'] as $key => $basic){ 
 $bid++; ?>
							<li class="span4" style="margin:0.25em;">
								<div class="thumbnail">
									<a class="thumbnail" href="index.php?exam-app-basics-detail&basicid=<?php echo $basic['basicid']; ?>" title="<?php echo $basic['basic']; ?>"><img alt="300x200" src="app/exam/styles/image/paper.png" style="width:160px;"/></a>
									<div class="caption">
										<div class="text-center">
											<h6><?php echo $this->G->make('strings')->subString($basic['basic'],28); ?></h6>
											<p>
											<?php if($this->tpl_var['data']['openbasics'][$basic['basicid']]){ ?>
											<a class="btn" href="javascript:;" title="<?php echo $basic['basic']; ?>">已开通</a>
											<?php } else { ?>
												<?php if($basic['basicdemo']){ ?>
												<a class="btn btn-success" href="index.php?exam-app-basics-detail&basicid=<?php echo $basic['basicid']; ?>" title="<?php echo $basic['basic']; ?>">免费开通</a>
												<?php } else { ?>
												<a class="btn btn-primary" href="index.php?exam-app-basics-detail&basicid=<?php echo $basic['basicid']; ?>" title="<?php echo $basic['basic']; ?>">去开通</a>
												<?php } ?>
											<?php } ?>
											</p>
										</div>
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
<?php if(!$this->tpl_var['userhash']){ ?>
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
<?php } ?>
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
					<div class="span6">
						<div class="thumbnail"><img alt="300x200" src="app/exam/styles/image/paper.png" /></div>
					</div>
					<div class="span6">
						<div class="caption">
							<h3><?php echo $this->tpl_var['basic']['basic']; ?></h3>
							<p>&nbsp;</p>
							<p>科目：<?php echo $this->tpl_var['subjects'][$this->tpl_var['basic']['basicsubjectid']]['subject']; ?></p>
							<p>地区：<?php echo $this->tpl_var['areas'][$this->tpl_var['basic']['basicareaid']]['area']; ?></p>
							<p>您现有积分：<?php echo $this->tpl_var['_user']['usercoin']; ?> （<a href="index.php?user-center-payfor">充值</a>）</p>
							<?php if($this->tpl_var['isopen']){ ?><p>到期时间：<?php echo date('Y-m-d',$this->tpl_var['isopen']['obendtime']); ?></p><?php } ?>
						</div>
						<div>&nbsp;</div>
						<?php if(!$this->tpl_var['isopen']){ ?>
						<form action="index.php?exam-app-basics-openit" method="post">
							<?php if(!$this->tpl_var['basic']['basicdemo']){ ?>
								<?php if($this->tpl_var['price']){ ?>
								<p>
									<select name="opentype" class="input-medium">
										<?php $pid = 0;
 foreach($this->tpl_var['price'] as $key => $p){ 
 $pid++; ?>
										<option value="<?php echo $key; ?>"><?php echo $p['price']; ?>积分兑换<?php echo $p['time']; ?>天</option>
										<?php } ?>
									</select>
								</p>
								<p>
									<input value="<?php echo $this->tpl_var['basic']['basicid']; ?>" name="basicid" type="hidden"/>
									<input class="btn btn-primary" value="开通" type="submit"/>
								</p>
								<?php } else { ?>
								<p>
									<input class="btn" value="请管理员先在后台设置价格" type="button"/>
								</p>
								<?php } ?>
							<?php } else { ?>
							<p>
								<input value="<?php echo $this->tpl_var['basic']['basicid']; ?>" name="basicid" type="hidden"/>
								<input class="btn btn-primary" value="开通" type="submit"/>
							</p>
							<?php } ?>
						</form>
						<?php } else { ?>
						<p>
							<input class="btn" value="已开通" type="button"/>
						</p>
						<?php } ?>
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
<?php $this->_compileInclude('header'); ?>
<body>
<?php $this->_compileInclude('nav'); ?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span2">
			<?php $this->_compileInclude('menu'); ?>
		</div>
		<div class="span10">
			<ul class="breadcrumb">
				<li><a href="index.php?<?php echo $this->tpl_var['_app']; ?>-master"><?php echo $this->tpl_var['apps'][$this->tpl_var['_app']]['appname']; ?></a> <span class="divider">/</span></li>
				<li class="active">首页</li>
			</ul>
			<div class="row-fluid">
				<div class="span6">
					<div class="well">
						<h5>
							系统信息
						</h5>
						<p>
							本版本在线管理系统为2015年11月1日发布1.1beta版。
						</p>
					</div>
					<div class="well">
						<h5>
							版本信息
						</h5>
						<p>
							本版主要更新为考场开通、支付宝集成、万能api接口。
						</p>
						<p>
							2015年8月30日完成的版本为本版的前一个版本。
						</p>
					</div>
				</div>
				<div class="span6">
					<div class="well">
						<h5>
							开发者信息
						</h5>
						<p>
							QQ:357058607
						</p>
					</div>
					<div class="well">
						<h5>
							使用帮助
						</h5>
						<p>
							 
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
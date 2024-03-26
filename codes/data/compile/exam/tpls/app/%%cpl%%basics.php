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
            	<h2 class="page_title"><img src="app/exam/styles/image/exam_opened_basic.jpg" alt="开通考场" /><a class="btn" href="index.php?exam-app-basics-open" style="margin-left:18px;" title="添加新考场"><em class="icon-plus"></em></a></h2>
            </div>
            <div class="container-fluid" id="datacontent">
<?php } ?>
				<div class="row-fluid">
					<div class="span12">
			            <ul class="thumbnails">
							<?php $bid = 0;
 foreach($this->tpl_var['basics'] as $key => $basic){ 
 $bid++; ?>
							<li class="span4" style="margin:0.25em;">
								<div class="thumbnail">
									<img alt="300x200" src="app/exam/styles/image/paper.png" style="width:160px;"/>
									<div class="caption">
										<p class="text-center">
											<a class="btn<?php if($this->tpl_var['data']['currentbasic']['basicid'] != $basic['basicid']){ ?> btn-primary<?php } ?>" href="javascript:;" onclick="javascript:$.get('?<?php echo $this->tpl_var['_app']; ?>-app-index-setCurrentBasic&basicid=<?php echo $basic['basicid']; ?>&'+Math.random(),function(data){window.location.reload();});" title="<?php echo $basic['basic']; ?>"><?php echo $this->G->make('strings')->subString($basic['basic'],28); ?></a>
										</p>
									</div>
								</div>
							</li>
							<?php } ?>
						</ul>
					</div>
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
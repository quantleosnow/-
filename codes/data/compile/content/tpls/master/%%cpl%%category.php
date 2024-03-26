<?php if(!$this->tpl_var['userhash']){ ?>
<?php $this->_compileInclude('header'); ?>
<body>
<?php $this->_compileInclude('nav'); ?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span2">
			<?php $this->_compileInclude('menu'); ?>
		</div>
		<div class="span10" id="datacontent">
<?php } ?>
			<ul class="breadcrumb">
				<li><a href="index.php?<?php echo $this->tpl_var['_app']; ?>-master"><?php echo $this->tpl_var['apps'][$this->tpl_var['_app']]['appname']; ?></a> <span class="divider">/</span></li>
				<li class="active">分类管理</li>
			</ul>
			<ul class="nav nav-tabs">
				<li class="active">
					<a href="#">分类管理</a>
				</li>
				<li class="dropdown pull-right">
					<a class="dropdown-toggle" href="#" data-toggle="dropdown">添加分类<strong class="caret"></strong></a>
					<ul class="dropdown-menu">
						<li><a href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-category-add&parent=<?php echo $this->tpl_var['parent']; ?>">添加分类</a></li>
						<li><a href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-category&parent=<?php echo $this->tpl_var['categories'][$this->tpl_var['parent']]['catparent']; ?>">上级分类</a></li>
                    </ul>
				</li>
			</ul>
			<legend><?php if($this->tpl_var['parent']){ ?><?php echo $this->tpl_var['categories'][$this->tpl_var['parent']]['catname']; ?><?php } else { ?>一级分类<?php } ?></legend>
			<?php $cid = 0;
 foreach($this->tpl_var['categorys']['data'] as $key => $category){ 
 $cid++; ?>
			<div class="media well">
				<?php if($category['catimg']){ ?>
				<a class="pull-left plugin" href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-category&parent=<?php echo $category['catid']; ?>"><img class="media-object" src="<?php echo $category['catimg']; ?>"></a>
				<?php } else { ?>
				<a class="pull-left plugin" href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-category&parent=<?php echo $category['catid']; ?>"><img class="media-object" src="app/core/styles/images/noupload.gif"></a>
				<?php } ?>
				<div class="media-body">
                	<h4 class="media-heading"><a href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-category&parent=<?php echo $category['catid']; ?>"><?php echo $category['catname']; ?></a></h4>
                	<div>【ID:<?php echo $category['catid']; ?>】<?php echo $this->G->make('strings')->subString(strip_tags(html_entity_decode($category['catdes'])),135); ?></div>
                	<div class="pull-right">
	                	<div class="btn-group">
							<a class="btn" href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-contents&catid=<?php echo $category['catid']; ?>&page=<?php echo $this->tpl_var['page']; ?><?php echo $this->tpl_var['u']; ?>"><em class="icon-th-list"></em></a>
							<a class="btn" href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-category-edit&page=<?php echo $this->tpl_var['page']; ?>&catid=<?php echo $category['catid']; ?><?php echo $this->tpl_var['u']; ?>"><em class="icon-edit"></em></a>
							<a class="btn ajax" href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-category-del&catid=<?php echo $category['catid']; ?>&page=<?php echo $this->tpl_var['page']; ?><?php echo $this->tpl_var['u']; ?>"><em class="icon-remove"></em></a>
						</div>
					</div>
                </div>
            </div>
            <?php } ?>
			<div class="pagination pagination-right">
				<ul><?php echo $this->tpl_var['categorys']['pages']; ?></ul>
			</div>

<?php if(!$this->tpl_var['userhash']){ ?>
		</div>
	</div>
</div>
</body>
</html>
<?php } ?>
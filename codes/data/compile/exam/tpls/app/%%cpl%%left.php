	<!--主体左侧-->
	<div id="main_left" <?php if($this->tpl_var['nouseleft']){ ?>class="main_left0"<?php } else { ?>class="main_left"<?php } ?>>
    	<div id="lesson_nav_top"></div>
    	<div id="lesson_nav_mid">
        	<!--科目导航-->
			<ul id="lesson_menu" class="menu">
              <li>
                <div class="menu-hd"><span id="lesson-current"><a rel="reload" class="collapsible" href="#zl_<?php echo $this->tpl_var['data']['currentbasic']['basicid']; ?>"><?php echo $this->G->make('strings')->subString($this->tpl_var['data']['currentbasic']['basic'],27); ?></a></span></div>
                <div id="zl_<?php echo $this->tpl_var['data']['currentbasic']['basicid']; ?>" class="menu-bd" style="display:none">
                  <dl>
                  	<?php $bid = 0;
 foreach($this->tpl_var['data']['openbasics'] as $key => $basic){ 
 $bid++; ?>
                  	<dd><a title="<?php echo $basic['basic']; ?>" href="#zl_<?php echo $basic['basicid']; ?>" onclick="javascript:$.get('index.php?<?php echo $this->tpl_var['_app']; ?>-app-index-setCurrentBasic&basicid=<?php echo $basic['basicid']; ?>&'+Math.random(),function(data){window.location.reload();});"><?php echo $this->G->make('strings')->subString($basic['basic'],27); ?></a></dd>
                  	<?php if($bid > 5){ ?>
                  	<?php break;; ?>
                  	<?php } ?>
                  	<?php } ?>
                  </dl>
                </div>
              </li>
    		</ul>
            <ol class="menu_con">
      			<li<?php if($this->tpl_var['method'] == 'basics'){ ?> class="on"<?php } ?>><a href="?exam-app-basics">我的考场</a></li>
      			<li<?php if($this->tpl_var['method'] == 'exercise'){ ?> class="on"<?php } ?>><a href="?exam-app-exercise">强化训练</a></li>
                <li<?php if($this->tpl_var['method'] == 'exampaper'){ ?> class="on"<?php } ?>><a href="?exam-app-exampaper">随机考试</a></li>
                <li<?php if($this->tpl_var['method'] == 'exam'){ ?> class="on"<?php } ?>><a href="?exam-app-exam">正式考试</a></li>
                <li<?php if($this->tpl_var['method'] == 'record'){ ?> class="on"<?php } ?>><a href="?exam-app-record">我的错题</a></li>
                <li<?php if($this->tpl_var['method'] == 'favor'){ ?> class="on"<?php } ?>><a href="?exam-app-favor">我的收藏</a></li>
                <li<?php if($this->tpl_var['method'] == 'history'){ ?> class="on"<?php } ?>><a href="?exam-app-history">答题记录</a></li>
         	</ol>
          <!--科目导航 结束-->
        </div>
    	<div id="lesson_nav_bottom"></div>
    </div>
	<!--主体左侧 结束-->
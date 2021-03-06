<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
	<div class="container">
		<div id="flashes">
			<?php $this->renderPartial('//layouts/_flash_messages') ?>
		</div>
	</div>
	<div id="content" class="clearfix full-width">
		<?php echo $this->page_heading ? BsHtml::pageHeader($this->page_heading, $this->page_heading_subtext) : ''?>
		<?php echo $content; ?>
	</div><!-- content -->
<?php $this->endContent(); ?>
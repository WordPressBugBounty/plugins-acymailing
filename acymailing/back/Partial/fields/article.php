<?php
defined('ABSPATH') || die('Restricted Access');
?>
<div class="cell">
	<select2ajax :name="'<?php echo esc_attr($name); ?>'"
	             :value="'<?php echo esc_attr($value); ?>'"
	             v-model="<?php echo esc_attr($vModel); ?>"
	             :urlselected="'&ctrl=forms&task=getArticlesById&article_id='"
	             :ctrl="'forms'"
	             :task="'getArticles'"></select2ajax>
</div>

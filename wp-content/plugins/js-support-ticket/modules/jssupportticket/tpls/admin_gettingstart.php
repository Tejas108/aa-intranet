<?php wp_enqueue_script('formvalidate.js', jssupportticket::$_pluginpath . 'includes/js/jquery.form-validator.js'); ?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $.validate();
    });
</script>
<?php JSSTmessage::getMessage(); ?>
<div id="gettingstart-wrapper">
    <div class="box1-wrapper">
        <span class="gettingstart-text">
            <?php echo __('GETTING START','js-support-ticket'); ?>
        </span>
        <img class="gettingstart-image" src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/gettingstart/mainimage.png"/>
    </div>
    <div class="box2-wrapper">
        <div class="box2-inner">
            <img class="box2-image" src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/gettingstart/frontimage.png" />
            <span class="title"><?php echo __('Display in front end','js-support-ticket'); ?></span>
            <span class="detail"><?php echo __('To display JS Support Ticket control panel at front end, add','js-support-ticket'); ?></span>
            <span class="shortcode"><?php echo __('[jssupportticket]','js-support-ticket'); ?></span>
            <span class="detail"><?php echo __('shortcode in any page or post','js-support-ticket'); ?></span>
        </div>
    </div>
    <?php if(jssupportticket::$_data['pageexist'] > 0){ // page exist ?>
    <div class="box3-wrapper">
        <div class="box3-inner">
            <span class="title"><?php echo __('Page already created','js-support-ticket'); ?></span>
            <span class="detail"><?php echo __('System is already create a page with shortcode for you.','js-support-ticket'); ?></span>
            <?php echo __('JS Support Ticket page current status is','js-support-ticket'); ?>:
            <?php if(jssupportticket::$_data['poststatus'] == 'publish'){ ?>
                    <span class="shortcode published"><?php echo __('Publish','js-support-ticket'); ?></span>                    
            <?php }else{ ?>
                    <span class="shortcode disabled"><?php echo __('Draft','js-support-ticket'); ?></span>
            <?php } ?>            
            <span class="detail"><?php echo __('You can add shortcode in any page, please scorll down','js-support-ticket'); ?></span>
            <img class="box3-image" src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/gettingstart/shortcodeimage.png" />
        </div>
    </div>
    <?php } ?>
    <div class="shortcode-wrapper">
        <div class="shortcode-title"><?php echo __('Add shortcode in page','js-support-ticket'); ?></div>
        <div class="shortcode-subtitle"><?php echo __('Add JS Support Ticket control panel [jssupportticket] shortcode','js-support-ticket'); ?></div>
        <form method="post" action="<?php echo admin_url("?page=jssupportticket&task=addpageslug"); ?>">
            <div class="shortcode-pages"><?php echo JSSTformfield::select('ID', JSSTincluder::getJSModel('jssupportticket')->getPageList(), '', __('Select Page', 'js-support-ticket'), array('class' => 'inputbox', 'data-validation' => 'required')); ?></div>
            <div class="shortcode-button"><?php echo JSSTformfield::submitbutton('save', __('Add into page', 'js-support-ticket'), array('class' => 'button')); ?></div>
            <?php echo JSSTformfield::hidden('action', 'jssupportticket_addpageslug'); ?>
            <?php echo JSSTformfield::hidden('form_request', 'jssupportticket'); ?>
        </form>
        <div class="shortcode-all"><a target="_blank" href="admin.php?page=jssupportticket&jstlay=shortcodes"><?php echo __('Click here to view all shortcodes','js-support-ticket'); ?></a></div>
        <img class="cornerimage" src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/gettingstart/corner-image.png">
    </div>
    <div class="box2-wrapper">
        <div class="box2-inner">
            <img class="box2-image" src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/gettingstart/systememailimage.png" />
            <span class="title"><?php echo __('Check system emails','js-support-ticket'); ?></span>
            <span class="detail"><?php echo __('System emails use to send email alerts','js-support-ticket'); ?></span>
            <span class="path"><?php echo __('Admin > System Emails','js-support-ticket'); ?></span>
            <a href="admin.php?page=email" target="_blank"><span class="shortcode"><?php echo __('Click here to view system emails','js-support-ticket'); ?></span></a>
        </div>
    </div>
    <div class="box3-wrapper">
        <div class="box3-inner">
            <span class="title"><?php echo __('Check Admin Email','js-support-ticket'); ?></span>
            <span class="detail"><?php echo __('Admin get all alerts on given email address','js-support-ticket'); ?></span>
            <span class="path"><?php echo __('Admin > Configuration > Default System Email','js-support-ticket'); ?></span>
            <a href="admin.php?page=configuration" target="_blank"><span class="shortcode"><?php echo __('Click here to view admin email','js-support-ticket'); ?></span></a>
            <img class="box3-image" src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/gettingstart/adminemailimage.png" />
        </div>
    </div>
    <a class="language-wrapper" target="_blank" href="https://www.transifex.com/joom-sky/js-support-ticket/">
        <div class="languages">
            <span class="heading"><?php echo __('Download translation for','js-support-ticket'); ?></span>
            <span class="subheading"><?php echo __('JS Support Ticket','js-support-ticket'); ?></span>
        </div>
    </a>
    <div class="box4-wrapper">
        <img class="corner1" src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/gettingstart/blackcorner1.png" />
        <img class="corner2" src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/gettingstart/blackcorner2.png" />
        <span class="title"><?php echo __('JS Support Ticket Documentation','js-support-ticket'); ?></span>
        <a class="documentation" href="http://joomshark.com/index.php/documentations/jsdocumentation/categories/default/5" target="_blank"><?php echo __('Click here to view documentation','js-support-ticket'); ?></a>
    </div>
</div>
    
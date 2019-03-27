<?php
$titleArray = array(__('Scripto'), __('Register'));
$title = implode(' | ', $titleArray);
$head = array('title' => html_escape($title));
echo head($head);
?>
<?php if (!is_admin_theme()): ?>
<h1><?php echo $head['title']; ?></h1>
<?php endif; ?>
<div id="primary">
<?php echo flash(); ?>

<div id="scripto-login" class="scripto">
<!-- navigation -->
<p>
<a href="<?php echo html_escape(url('scripto/index/login')); ?>"><?php echo __('Log in to Scripto'); ?></a>
 | <a href="<?php echo html_escape(url('scripto/index/recent-changes')); ?>"><?php echo __('Recent changes'); ?></a>
 </p>
<?php if ($registeredOK): ?>
    <p><?php echo __('Congrats; you have registered.') ?></p>
    <p><a href="<?php echo html_escape(url('scripto/index/login')); ?>"><?php echo __('Continue here to login.'); ?></a></p>
<?php else: ?>
<p><?php echo __(
    'Log in to Scripto using your MediaWiki username and password to access your account '
  . 'and enable certain Scripto features. Login may not be required by the administrator.'
); ?></p>
<p><?php echo __('You need to register to use the transcription tools') ?></p>

<!-- register -->
<form action="<?php echo html_escape(url('scripto/index/register')); ?>" method="post">
<div class="field">
    <label for="scripto_mediawiki_username"><?php echo __('Username'); ?></label>
        <div class="inputs">
        <?php echo $this->formText('scripto_mediawiki_username', null, array('size' => 18)); ?>
    </div>
</div>
<div class="field">
    <label for="scripto_mediawiki_password"><?php echo __('Password'); ?></label>
        <div class="inputs">
        <?php echo $this->formPassword('scripto_mediawiki_password', null, array('size' => 18)); ?>
    </div>
</div>
<div class="field">
    <label for="scripto_mediawiki_realname"><?php echo __('Name'); ?></label>
        <div class="inputs">
        <?php echo $this->formText('scripto_mediawiki_realname', null, array('size' => 18)); ?>
    </div>
</div>
<div class="field">
    <label for="scripto_mediawiki_email"><?php echo __('Email'); ?></label>
        <div class="inputs">
        <?php echo $this->formText('scripto_mediawiki_email', null, array('size' => 18)); ?>
    </div>
</div>

<?php echo $this->formHidden('scripto_redirect_url', $this->redirectUrl); ?>
<?php echo $this->formSubmit('scripto_mediawiki_register', __('Register'), array('style' => 'display:inline; float:none;')); ?>
</form>
<?php endif; ?>
</div><!-- #scripto-register -->
</div>
<?php echo foot();

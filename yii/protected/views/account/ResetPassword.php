<?php $this->pageTitle='Reset Password'; ?>

<h1>Reset Password</h1>

<?= EHtml::beginForm(array('ResetPasswordConfirm')); ?>

<?= EHtml::errorSummary($form); ?>

    <p>
    	Hello <b><?= CHtml::encode($form->username) ?></b>.
    </p>
    <p>
        From this page you can reset your forgotten password to a new generated password.
        If you wish to proceed click the confirm button to reset your password.
        <br />
        If you do not wish to reset your password you can <?= CHtml::link("login", array("Login")) ?> here.
    </p>
    
	<div>
		<?= EHtml::activeHiddenField($form, 'username'); ?>
		<?= EHtml::activeHiddenField($form, 'resetPasswordCode'); ?>
		<?= EHtml::submitButton('Confirm'); ?>
	</div>	
<?= EHtml::endForm(); ?>


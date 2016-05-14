<?php $this->pageTitle='Password Successfully Reset'; ?>

<h1>Password Successfully Reset</h1>

<p>
	Welcome <b><?= CHtml::encode($form->username) ?></b>.
</p>
<p>
	Your password has been sucessfully reset to: <b><?= CHtml::encode($form->newPassword) ?></b>
	<br />
	Please be sure to note the new password as you will need it the next time you login.
</p>

<?= CHtml::beginForm(array("Login")); ?>
	<div>
		<?= CHtml::hiddenField("LoginForm[username]", $form->username) ?>
		<?= CHtml::hiddenField("LoginForm[password]", $form->newPassword) ?>
		<?= CHtml::submitButton('Continue'); ?>
	</div>	
<?= CHtml::endForm(); ?>



<?php $this->pageTitle='Login'; ?>

<h1>Login</h1>
<p>
	Please enter your username and password below. If you don't have an account,
	please <?= CHtml::link("register", array("Register")) ?>. If you've forgotten your
	password you can <?= CHtml::link("reset", array("ForgotPassword")) ?> it.
</p>
<?= EHtml::beginForm(); ?>

<?= EHtml::errorSummary($form); ?>

<div>
	<table>
		<tr>
			<td>Username:</td>
			<td>
				<?= EHtml::activeTextField($form,'username') ?>
			</td>
		</tr>
		<tr>
			<td>Password:</td>
			<td>
				<?= EHtml::activePasswordField($form,'password') ?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<div class="action">
				<?= EHtml::submitButton('Login'); ?>
				</div>
			</td>
		</tr>
	</table>
</div>

<?= EHtml::endForm(); ?>
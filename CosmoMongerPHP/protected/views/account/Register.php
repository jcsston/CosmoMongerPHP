<?php $this->pageTitle='Register'; ?>

<h1>Account Creation</h1>
<p>
	Use the form below to create a new account. 
</p>
<?= EHtml::beginForm(); ?>

<?= EHtml::errorSummary($form); ?>

	<div>
		<table cellspacing="5">
			<tr>
				<td>Username:</td>
				<td>
					<?= EHtml::activeTextField($form,"username") ?>
				</td>
			</tr>
			<tr>
				<td>Email:</td>
				<td>
					<?= EHtml::activeTextField($form,"email")?>
				</td>
			</tr>
			<tr>
				<td>Password:</td>
				<td>
					<?= EHtml::activePasswordField($form,"password")?>
				</td>
			</tr>
			<tr>
				<td>Confirm Password:</td>
				<td>
					<?= EHtml::activePasswordField($form,"confirmPassword")?>
				</td>
			</tr>
			<tr>
				<td>Verify you are human:</td>
				<td>
					<?php $this->widget('application.extensions.recaptcha.EReCaptcha', 
					   array('model'=>$user, 'attribute'=>'verifyCode',
							 'theme'=>'blackglass', 'language'=>'en_US', 
							 'publicKey'=>Yii::app()->params['RecaptchaPublicKey'])) ?>
					<?= CHtml::error($form, 'verifyCode'); ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><?= EHtml::submitButton('Register'); ?></td>
			</tr>
		</table>
	</div>	
<?= EHtml::endForm(); ?>

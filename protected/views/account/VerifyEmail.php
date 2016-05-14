<?php $this->pageTitle='Verify Email'; ?>

<h1>Verify Email</h1>

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
				<td>Verification Code:</td>
				<td>
					<?= EHtml::activePasswordField($form,"verificationCode")?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><?= EHtml::submitButton('Verify'); ?></td>
			</tr>
		</table>
	</div>	
<?= EHtml::endForm(); ?>

<p>
	Never received your verification code?
	<br /> 
	Click <?= CHtml::link("here", array("SendVerificationCode", "SendVerificationCodeForm[username]" => $form->username)) ?> to send a new verification code.
</p>
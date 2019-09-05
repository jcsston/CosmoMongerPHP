<?php $this->pageTitle='Forgotten Password'; ?>

<h1>Forgot Password</h1>
<p>
	Please enter your email below and click Send. An email message will be sent 
	to your email address with a link to reset your password.
</p>

<?= EHtml::beginForm(); ?>

<?= EHtml::errorSummary($form); ?>
	<div>
		<table cellspacing="5">
			<tr>
				<td>Email:</td>
				<td>
					<?= EHtml::activeTextField($form,"email")?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><?= EHtml::submitButton('Send'); ?></td>
			</tr>
		</table>
	</div>	
<?= EHtml::endForm(); ?>

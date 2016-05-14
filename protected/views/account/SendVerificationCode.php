<?php $this->pageTitle='Send Verification Code'; ?>

<h1>Send Verification Code Email</h1>

<?= EHtml::beginForm(); ?>

<?= EHtml::errorSummary($form); ?>
<table cellspacing="5">
	<tr>
		<td>Username:</td>
		<td>
			<?= EHtml::activeTextField($form,"username") ?>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><?= EHtml::submitButton('Send'); ?></td>
	</tr>
</table>
<?= EHtml::endForm(); ?>

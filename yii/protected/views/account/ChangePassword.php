<?php $this->pageTitle='Change Password'; ?>

<h1>Change Password</h1>
<p>
	Use the form below to change your password. 
</p>

<?= EHtml::beginForm(); ?>

<?= EHtml::errorSummary($form); ?>
	<div>
		<table cellspacing="5">
			<tr>
				<td>Current Password:</td>
				<td>
					<?= EHtml::activePasswordField($form,"currentPassword")?>
				</td>
			</tr>
			<tr>
				<td>New Password:</td>
				<td>
					<?= EHtml::activePasswordField($form,"newPassword")?>
				</td>
			</tr>
			<tr>
				<td>Confirm New Password:</td>
				<td>
					<?= EHtml::activePasswordField($form,"confirmPassword")?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><?= EHtml::submitButton('Change Password'); ?></td>
			</tr>
		</table>
	</div>	
<?= EHtml::endForm(); ?>


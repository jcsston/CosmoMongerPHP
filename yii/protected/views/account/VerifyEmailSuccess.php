<?php $this->pageTitle='Verified Email'; ?>

<h1>Verified Email</h1>

<p>Your e-mail <b><?= CHtml::encode($email) ?></b> has been verified.</p>
<p>You can now <?= CHtml::link("Login", array("Login", "LoginForm[username]"=>$username)) ?>.</p>

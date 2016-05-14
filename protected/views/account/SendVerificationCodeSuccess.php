<?php $this->pageTitle='Sent Verification Code'; ?>

<h1>Sent Verification Code</h1>

<p>Check your e-mail for a e-mail from admin@cosmomonger.com and click the link in the e-mail</p>
<p>You can also go <?= CHtml::link("here", array("VerifyEmail", 'VerifyEmailForm[username]' => $username)) ?> to enter in your verification code from the e-mail.</p>

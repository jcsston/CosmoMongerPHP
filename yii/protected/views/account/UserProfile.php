<?php $this->pageTitle='User Profile'; 
/*
    <script type="text/javascript">
    //<![CDATA[
        $(document).ready(function() {
            $("#joinDate").datetimeUTCtoLocal();
        });
    //]]>
    </script>
*/
?>

<h1>User Profile for <?= CHtml::encode($name) ?></h1>
<br />
<p class="up-links">Join Date: <span id="joinDate"><?= CHtml::encode($joinDate) ?></span></p>
<p class="up-links"><?= CHtml::link("Change My Password", array("Account/ChangePassword")) ?></p>
<p class="up-links">Email: <?= CHtml::encode($email) ?></p>
<p class="up-links"><?= CHtml::link("Change My Email Account", array("Account/ChangeEmail")) ?></p>


<?php $this->pageTitle='View Player Profile'; 
/*
    <script type="text/javascript" src="/Scripts/jquery.confirm-1.2.js"></script>
    <script type="text/javascript">
    //<![CDATA[
        $(document).ready(function() {
            $(":submit").confirm();
        });
    //]]>
    </script>
*/
?>

<h1>Player Profile</h1>
<table style="width: 100%">
<tr>
    <td class="vp-playerName" colspan="4"><?= CHtml::encode($name) ?></td>
</tr>
<tr>
    <td colspan="4">&nbsp;</td>
</tr>
<tr>
    <td class="vp-headers" colspan="2">Financial Data</td>
    <td class="vp-headers" colspan="2">Racial Data</td>
</tr>
<tr>
    <td class="vp-columnData"><u>Net Worth:</u></td>
    <td class="vp-columnData"><u><?= $netWorth ?></u></td>
    <td class="vp-columnData">Player's Race:</td>
    <td class="vp-columnData"><span id="playerRace"><?= CHtml::encode($raceName) ?></span></td>
</tr>
<tr>
    <td class="vp-columnData">Credits:</td>
    <td class="vp-columnData"><?= $shipCredits ?></td>
    <td class="vp-columnData">Racial Preference:</td>
    <td class="vp-columnData"><?= CHtml::encode($racialPreference) ?></td>
</tr>
<tr>
    <td class="vp-columnData">Bank Credits:</td>
    <td class="vp-columnData"><?= $bankCredits ?></td>
    <td class="vp-columnData">Racial Enemy:</td>
    <td class="vp-columnData"><?= CHtml::encode($racialEnemy) ?></td>
</tr>
<tr>
    <td class="vp-columnData">Ship Trade-In Value:</td>
    <td class="vp-columnData"><?= $shipTradeInValue ?></td>
</tr>
<tr>
    <td class="vp-columnData">Cargo Value:</td>
    <td class="vp-columnData"><?= $cargoWorth ?></td>
</tr>
<tr>
    <td colspan="4">&nbsp;</td>
</tr>
<tr>
    <td class="vp-headers" colspan="2">Want A New Player?</td>
    <td class="vp-headers" colspan="2">Time Played</td>
</tr>
<tr>
    <td align="center" colspan="2">Warning! This is irrevisible!</td>
    <td align="center" colspan="2">Time Limit = 168 hours</td>
</tr>
<tr>
    <td align="center" colspan="2">
<?= CHtml::beginForm(array('KillPlayer')); ?>
    <div>
        <input type="hidden" name="playerId" value="<?= $playerId ?>" /> 
        <input type="submit" value="Kill Current Player" />
    </div>
<?= CHtml::endForm(); ?>
    </td>
    <td class="vp-columnData"><?= CHtml::encode($playerAge) ?></td>
    <td class="vp-columnData">hours</td>
</tr>
</table>


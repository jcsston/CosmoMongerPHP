<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="en" />
<meta name="description" content="CosmoMonger is an on-line space-based trading game featuring real-time multi-player interaction." />
<meta name="keywords" content="CosmoMonger, space, trader, game, free, combat, police, pirates, traders, goods" />
<!--
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
-->
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />
<link href="<?= Yii::app()->request->baseUrl; ?>/Content/vader/ui.all.css" rel="stylesheet" type="text/css" />
<link href="<?= Yii::app()->request->baseUrl; ?>/Content/Site.css" rel="stylesheet" type="text/css" />
<link rel="icon" type="image/vnd.microsoft.icon" href="<?= Yii::app()->request->baseUrl; ?>/Content/favicon.ico" />
<script type="text/javascript" src="<?= Yii::app()->request->baseUrl; ?>/Scripts/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?= Yii::app()->request->baseUrl; ?>/Scripts/jquery.ui-1.7.1.min.js"></script>
<script type="text/javascript">
//<![CDATA[
	function centerMenu() {
		var slackSpace = $(document).width() - $("#menuh").width();
		if (slackSpace > 0) {
			var menuLeft = slackSpace / 2;
			$("#menuh-container").css("left", menuLeft + "px");
		}
	}

	$(document).ready(function() {
		centerMenu();
		$(window).resize(centerMenu);
		
		// Make the help links open in a new window
		$('a.new-window').click(function(){
			window.open(this.href);
			return false;
		});
	});
//]]>
</script>
<?php 
	if (!Yii::app()->user->isGuest) { 
?>
    <script type="text/javascript" src="<?= Yii::app()->request->baseUrl; ?>/Scripts/date.js"></script>
    <script type="text/javascript" src="<?= Yii::app()->request->baseUrl; ?>/Scripts/jquery.jgrowl.min.js"></script>
    <script type="text/javascript">
    //<![CDATA[
        var ajaxRequest = null;
        lastMessageCount = 0;
        function updateUnreadMessages() {
            ajaxRequest = $.getJSON('<?= CHtml::normalizeUrl(array('Communication/UnreadMessages')) ?>', { lastMessageCount: -1 }, displayUnreadMessages);
        }
        
        function displayUnreadMessages(data) {
            var messageCount = data.length;
            var messageDelay = 0;
            if (messageCount != lastMessageCount) {
                lastMessageCount = messageCount;
                for (var i = 0; i < data.length; i++) {
                    var msg = data[i];
                    
                    // Convert the UTC time to local
                    var utcMsgDate = new Date(msg.time);
                    var msgDate = new Date(utcMsgDate);
                    msgDate.add('m', -(utcMsgDate.getTimezoneOffset()));

                    var notice = 'Subject: ' + msg.subject
                        + '<br />Date: ' + msgDate.toLocaleDateString()
                        + '<br />Time: ' + msgDate.toLocaleTimeString()
                        + '<br />'
                        + ('Read Message'.link('<?= CHtml::normalizeUrl(array('Communication/ViewMessage')) ?>&messageId=' + msg.id));

                    // Give 6 seconds per message to read
                    messageDelay += 6000;
                    $.jGrowl(notice, { header: 'Unread message from ' + msg.from, life: messageDelay });
                }
            }
            setTimeout(updateUnreadMessages, 60 * 1000);
        }

        $(document).ready(function() {
            // Only nag the user if they are not in the Communication area
            if (location.pathname.indexOf("Communication") == -1) {
                updateUnreadMessages();
            }
        });

        $(window).unload(function() {
            // Abort any active Ajax request
            if (ajaxRequest) {
                ajaxRequest.abort();
            }
        });
    //]]>
    </script>
<?php } ?>
<title><?php echo $this->pageTitle; ?></title>
</head>

<body>
<div class="page">
<div id="header">
    <!-- The following CSS menu is from http://sperling.com/examples/menuh/ -->
    <div id="menuh-container">
        <div id="menuh">
<?php if (!Yii::app()->user->isGuest) { ?>
            <ul style="width: auto">
                <li style="cursor: default">
                        <div id="menuha">
                            Welcome aboard: <b><?= CHtml::encode(Yii::app()->user->name) ?></b>&nbsp;
                        </div>
                </li>
            </ul>
            <ul>	
                <li><?= CHtml::Link("Player", array('Player/Index')) ?>
                    <ul>
                        <li><?= CHtml::Link("User Profile", array('Account/UserProfile')) ?></li>
                        <li><?= CHtml::Link("Player Profile", array('Player/PlayerProfile')) ?></li>
                        <li><?= CHtml::Link("Current Ship", array('Ship/ViewShip')) ?></li>
                        <li><?= CHtml::Link("View Record", array('PlayerRecord/ViewRecord')) ?></li>
                        <li><?= CHtml::Link("Record History", array('PlayerRecord/ViewRecordHistory')) ?></li>
                        <li><?= CHtml::Link("Top Records", array('PlayerRecord/ListRecords')) ?></li>


                    </ul>
                </li>
            </ul>
            <ul>                    	                            
        	    <li><?= CHtml::Link("Communication", array('Communication/Index')) ?>
                    <ul>
                        <li><?= CHtml::Link("Inbox", array('Communication/Inbox')) ?></li>
                        <li><?= CHtml::Link("Sent", array('Communication/Sent')) ?></li>
                        <li><?= CHtml::Link("Compose", array('Communication/Compose')) ?></li>
                        <li><?= CHtml::Link("Buddy List", array('BuddyList/BuddyList')) ?></li>
                        <li><?= CHtml::Link("Ignore List", array('BuddyList/IgnoreList')) ?></li>                             
                    </ul>
                </li>
            </ul>
            <ul>	
                <li><?= CHtml::Link("System", array('Trade/Index')) ?>
                    <ul>
                        <li><?= CHtml::Link("List Goods", array('Trade/ListGoods')) ?></li>
                        <li><?= CHtml::Link("Good Price Table", array('Trade/PriceTable')) ?></li>
                        <li><?= CHtml::Link("Visit Bank", array('Bank/Bank')) ?></li>
                        <li><?= CHtml::Link("List Ships", array('Ship/ListShips')) ?></li>
                        <li><?= CHtml::Link("Ship Upgrades", array('Ship/ListUpgrades')) ?></li>
                    </ul>
                </li>
            </ul>
            <ul>	
                <li><?= CHtml::Link("Travel", array('Travel/Index')) ?>
                    <ul>
                        <li><?= CHtml::Link("Travel", array('Travel/Travel')) ?></li>
                        <li><?= CHtml::Link("Attack", array('Combat/Attack')) ?></li>
                    </ul>
                </li>
            </ul>
            <ul>
                <li><?= CHtml::Link("Logout", array('Account/Logout')) ?></li>
            </ul>
<?php } else { ?> 
            <ul>
                <li>
					<?= CHtml::link("Login", array('Account/Login')) ?>
                </li>
            </ul>
<?php } ?>
            <ul>
                <li>
					<a class="new-window" href="http://wiki.cosmomonger.com/<?= urlencode(Yii::app()->controller->id . "_" . Yii::app()->controller->action->id) ?>.ashx">Help</a>
			    </li>
			</ul>
        </div>
    </div>  
</div>
<div id="main" class="main">
<?php echo $content; ?>
</div>
</div>
<div id="footer">
 *If you need assistance, please click the "Help" link at the top of the screen.   
  <br />  
  <?php 
  /*
<% if (!this.Request.IsLocal) { %>
    <script type="text/javascript"><!--
        google_ad_client = "pub-7722569317874144";
        // 728x90, created 1/15/09
        google_ad_slot = "0383659721";
        google_ad_width = 728;
        google_ad_height = 90;
    //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
    <script type="text/javascript" src="http://cosmomonger.uservoice.com/pages/general/widgets/tab.js?alignment=right&amp;color=000000"></script>
<% } %> */ ?>
    <p>
		&copy; Copyright 2008-2009 <?= CHtml::link("CosmoMonger", array('Home/About')) ?>
		<br />
		rev <?= CosmoManager::getCodeVersion() ?> (db <?= CosmoManager::getDatabaseVersion() ?>) <br />
<?php 
		$processingTime = Yii::getLogger()->getExecutionTime();
/*
        <% 
            double processingTime = (DateTime.Now - this.ViewContext.HttpContext.Timestamp).TotalSeconds;
            Dictionary<string, object> props = new Dictionary<string, object>
            { 
                { "ViewPath", ((WebFormView)this.ViewContext.View).ViewPath },
                { "IPAddress", this.ViewContext.HttpContext.Request.UserHostAddress },
                { "SessionID", this.ViewContext.HttpContext.Session.SessionID },
                { "ProcessingTime", processingTime }
            };
            string message = this.ViewContext.RouteData.Values["controller"] + "." + this.ViewContext.RouteData.Values["action"];
            Microsoft.Practices.EnterpriseLibrary.Logging.Logger.Write(message, "Page Log", 800, 0, System.Diagnostics.TraceEventType.Verbose, "Master Page Log", props);
        %> 
*/
		?>
        Processing Time: <?= printf("%.1f", $processingTime) ?> seconds 
     </p>
</div>
</div>
</body>
</html>
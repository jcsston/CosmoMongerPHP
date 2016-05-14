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
<?php /* ?>
            <ul>	
                <li><%= Html.ActionLink("Player", "Index", "Player")%>
                    <ul>
                        <li><%= Html.ActionLink("User Profile", "UserProfile", "Account")%></li>
                        <li><%= Html.ActionLink("Player Profile", "PlayerProfile", "Player")%></li>
                        <li><%= Html.ActionLink("Current Ship", "ViewShip", "Ship")%></li>
                        <li><%= Html.ActionLink("View Record", "ViewRecord", "PlayerRecord")%></li>
                        <li><%= Html.ActionLink("Record History", "ViewRecordHistory", "PlayerRecord")%></li>
                        <li><%= Html.ActionLink("Top Records", "ListRecords", "PlayerRecord")%></li>


                    </ul>
                </li>
            </ul>
            <ul>                    	                            
        	    <li><%= Html.ActionLink("Communication", "Index", "Communication")%>
                    <ul>
                        <li><%= Html.ActionLink("Inbox", "Inbox", "Communication")%></li>
                        <li><%= Html.ActionLink("Sent", "Sent", "Communication")%></li>
                        <li><%= Html.ActionLink("Compose", "Compose", "Communication")%></li>
                        <li><%= Html.ActionLink("Buddy List", "BuddyList", "BuddyList")%></li>
                        <li><%= Html.ActionLink("Ignore List", "IgnoreList", "BuddyList")%></li>                             
                    </ul>
                </li>
            </ul>
            <ul>	
                <li><%= Html.ActionLink("System", "Index", "Trade")%>
                    <ul>
                        <li><%= Html.ActionLink("List Goods", "ListGoods", "Trade")%></li>
                        <li><%= Html.ActionLink("Good Price Table", "PriceTable", "Trade")%></li>
                        <li><%= Html.ActionLink("Visit Bank", "Bank", "Bank")%></li>
                        <li><%= Html.ActionLink("List Ships", "ListShips", "Ship")%></li>
                        <li><%= Html.ActionLink("Ship Upgrades", "ListUpgrades", "Ship")%></li>
                    </ul>
                </li>
            </ul>
            <ul>	
                <li><%= Html.ActionLink("Travel", "Index", "Travel")%>
                    <ul>
                        <li><%= Html.ActionLink("Travel", "Travel", "Travel")%></li>
                        <li><%= Html.ActionLink("Attack", "Attack", "Combat")%></li>
                    </ul>
                </li>
            </ul>
<?php */ ?>
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
<?php /*
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
        Processing Time: <%=processingTime%> seconds
		*/ ?>
		<?= Yii::powered(); ?>
     </p>
</div>
</div>
</body>
</html>
<?php


$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'AcyMailerPhp\\AcyMailerPhp' => $baseDir . '/front/libraries/mailer/mailer.php',
    'AcyMailerPhp\\Exception' => $baseDir . '/front/libraries/mailer/exception.php',
    'AcyMailerPhp\\OAuth' => $baseDir . '/front/libraries/mailer/OAuth.php',
    'AcyMailerPhp\\OAuthTokenProvider' => $baseDir . '/front/libraries/mailer/OAuthTokenProvider.php',
    'AcyMailerPhp\\SMTP' => $baseDir . '/front/libraries/mailer/smtp.php',
    'AcyMailing\\Classes\\ActionClass' => $baseDir . '/back/classes/action.php',
    'AcyMailing\\Classes\\AutomationClass' => $baseDir . '/back/classes/automation.php',
    'AcyMailing\\Classes\\CampaignClass' => $baseDir . '/back/classes/campaign.php',
    'AcyMailing\\Classes\\ConditionClass' => $baseDir . '/back/classes/condition.php',
    'AcyMailing\\Classes\\ConfigurationClass' => $baseDir . '/back/classes/configuration.php',
    'AcyMailing\\Classes\\FieldClass' => $baseDir . '/back/classes/field.php',
    'AcyMailing\\Classes\\FollowupClass' => $baseDir . '/back/classes/followup.php',
    'AcyMailing\\Classes\\FormClass' => $baseDir . '/back/classes/form.php',
    'AcyMailing\\Classes\\HistoryClass' => $baseDir . '/back/classes/history.php',
    'AcyMailing\\Classes\\ListClass' => $baseDir . '/back/classes/list.php',
    'AcyMailing\\Classes\\MailArchiveClass' => $baseDir . '/back/classes/mailarchive.php',
    'AcyMailing\\Classes\\MailClass' => $baseDir . '/back/classes/mail.php',
    'AcyMailing\\Classes\\MailStatClass' => $baseDir . '/back/classes/mailstat.php',
    'AcyMailing\\Classes\\MailboxClass' => $baseDir . '/back/classes/mailbox.php',
    'AcyMailing\\Classes\\MailpoetClass' => $baseDir . '/back/classes/mailpoet.php',
    'AcyMailing\\Classes\\OverrideClass' => $baseDir . '/back/classes/override.php',
    'AcyMailing\\Classes\\PluginClass' => $baseDir . '/back/classes/plugin.php',
    'AcyMailing\\Classes\\QueueClass' => $baseDir . '/back/classes/queue.php',
    'AcyMailing\\Classes\\RuleClass' => $baseDir . '/back/classes/rule.php',
    'AcyMailing\\Classes\\SegmentClass' => $baseDir . '/back/classes/segment.php',
    'AcyMailing\\Classes\\StepClass' => $baseDir . '/back/classes/step.php',
    'AcyMailing\\Classes\\TagClass' => $baseDir . '/back/classes/tag.php',
    'AcyMailing\\Classes\\UrlClass' => $baseDir . '/back/classes/url.php',
    'AcyMailing\\Classes\\UrlClickClass' => $baseDir . '/back/classes/urlclick.php',
    'AcyMailing\\Classes\\UserClass' => $baseDir . '/back/classes/user.php',
    'AcyMailing\\Classes\\UserStatClass' => $baseDir . '/back/classes/userstat.php',
    'AcyMailing\\Classes\\ZoneClass' => $baseDir . '/back/classes/zone.php',
    'AcyMailing\\Controllers\\AutomationController' => $baseDir . '/back/controllers/automation.php',
    'AcyMailing\\Controllers\\Automations\\Action' => $baseDir . '/back/controllers/automations/Action.php',
    'AcyMailing\\Controllers\\Automations\\Condition' => $baseDir . '/back/controllers/automations/Condition.php',
    'AcyMailing\\Controllers\\Automations\\Filter' => $baseDir . '/back/controllers/automations/Filter.php',
    'AcyMailing\\Controllers\\Automations\\Info' => $baseDir . '/back/controllers/automations/Info.php',
    'AcyMailing\\Controllers\\Automations\\Listing' => $baseDir . '/back/controllers/automations/Listing.php',
    'AcyMailing\\Controllers\\Automations\\MassAction' => $baseDir . '/back/controllers/automations/MassAction.php',
    'AcyMailing\\Controllers\\Automations\\Summary' => $baseDir . '/back/controllers/automations/Summary.php',
    'AcyMailing\\Controllers\\BouncesController' => $baseDir . '/back/controllers/bounces.php',
    'AcyMailing\\Controllers\\Bounces\\Listing' => $baseDir . '/back/controllers/bounces/Listing.php',
    'AcyMailing\\Controllers\\Bounces\\Rule' => $baseDir . '/back/controllers/bounces/Rule.php',
    'AcyMailing\\Controllers\\CampaignsController' => $baseDir . '/back/controllers/campaigns.php',
    'AcyMailing\\Controllers\\Campaigns\\Actions' => $baseDir . '/back/controllers/campaigns/Actions.php',
    'AcyMailing\\Controllers\\Campaigns\\AutoCampaigns' => $baseDir . '/back/controllers/campaigns/AutoCampaigns.php',
    'AcyMailing\\Controllers\\Campaigns\\Edition' => $baseDir . '/back/controllers/campaigns/Edition.php',
    'AcyMailing\\Controllers\\Campaigns\\Followup' => $baseDir . '/back/controllers/campaigns/Followup.php',
    'AcyMailing\\Controllers\\Campaigns\\ListEmails' => $baseDir . '/back/controllers/campaigns/ListEmails.php',
    'AcyMailing\\Controllers\\Campaigns\\Listing' => $baseDir . '/back/controllers/campaigns/Listing.php',
    'AcyMailing\\Controllers\\Campaigns\\Tests' => $baseDir . '/back/controllers/campaigns/Tests.php',
    'AcyMailing\\Controllers\\ConfigurationController' => $baseDir . '/back/controllers/configuration.php',
    'AcyMailing\\Controllers\\Configuration\\Language' => $baseDir . '/back/controllers/configuration/Language.php',
    'AcyMailing\\Controllers\\Configuration\\License' => $baseDir . '/back/controllers/configuration/License.php',
    'AcyMailing\\Controllers\\Configuration\\Listing' => $baseDir . '/back/controllers/configuration/Listing.php',
    'AcyMailing\\Controllers\\Configuration\\Mail' => $baseDir . '/back/controllers/configuration/Mail.php',
    'AcyMailing\\Controllers\\Configuration\\Queue' => $baseDir . '/back/controllers/configuration/Queue.php',
    'AcyMailing\\Controllers\\Configuration\\Security' => $baseDir . '/back/controllers/configuration/Security.php',
    'AcyMailing\\Controllers\\Configuration\\Subscription' => $baseDir . '/back/controllers/configuration/Subscription.php',
    'AcyMailing\\Controllers\\DashboardController' => $baseDir . '/back/controllers/dashboard.php',
    'AcyMailing\\Controllers\\Dashboard\\Listing' => $baseDir . '/back/controllers/dashboard/Listing.php',
    'AcyMailing\\Controllers\\Dashboard\\Migration' => $baseDir . '/back/controllers/dashboard/Migration.php',
    'AcyMailing\\Controllers\\Dashboard\\Walkthrough' => $baseDir . '/back/controllers/dashboard/Walkthrough.php',
    'AcyMailing\\Controllers\\DeactivateController' => $baseDir . '/back/controllers/deactivate.php',
    'AcyMailing\\Controllers\\DynamicsController' => $baseDir . '/back/controllers/dynamics.php',
    'AcyMailing\\Controllers\\EntitySelectController' => $baseDir . '/back/controllers/entitySelect.php',
    'AcyMailing\\Controllers\\FieldsController' => $baseDir . '/back/controllers/fields.php',
    'AcyMailing\\Controllers\\Fields\\Edition' => $baseDir . '/back/controllers/fields/Edition.php',
    'AcyMailing\\Controllers\\Fields\\Listing' => $baseDir . '/back/controllers/fields/Listing.php',
    'AcyMailing\\Controllers\\FileController' => $baseDir . '/back/controllers/file.php',
    'AcyMailing\\Controllers\\FollowupsController' => $baseDir . '/back/controllers/followups.php',
    'AcyMailing\\Controllers\\FormsController' => $baseDir . '/back/controllers/forms.php',
    'AcyMailing\\Controllers\\Forms\\Edition' => $baseDir . '/back/controllers/forms/Edition.php',
    'AcyMailing\\Controllers\\Forms\\Listing' => $baseDir . '/back/controllers/forms/Listing.php',
    'AcyMailing\\Controllers\\GoproController' => $baseDir . '/back/controllers/gopro.php',
    'AcyMailing\\Controllers\\LanguageController' => $baseDir . '/back/controllers/language.php',
    'AcyMailing\\Controllers\\ListsController' => $baseDir . '/back/controllers/lists.php',
    'AcyMailing\\Controllers\\Lists\\Ajax' => $baseDir . '/back/controllers/lists/Ajax.php',
    'AcyMailing\\Controllers\\Lists\\Edition' => $baseDir . '/back/controllers/lists/Edition.php',
    'AcyMailing\\Controllers\\Lists\\Listing' => $baseDir . '/back/controllers/lists/Listing.php',
    'AcyMailing\\Controllers\\MailboxActions\\Edition' => $baseDir . '/back/controllers/mailboxActions/Edition.php',
    'AcyMailing\\Controllers\\MailboxActions\\Listing' => $baseDir . '/back/controllers/mailboxActions/Listing.php',
    'AcyMailing\\Controllers\\MailsController' => $baseDir . '/back/controllers/mails.php',
    'AcyMailing\\Controllers\\Mails\\Automation' => $baseDir . '/back/controllers/mails/Automation.php',
    'AcyMailing\\Controllers\\Mails\\Edition' => $baseDir . '/back/controllers/mails/Edition.php',
    'AcyMailing\\Controllers\\Mails\\Listing' => $baseDir . '/back/controllers/mails/Listing.php',
    'AcyMailing\\Controllers\\OverrideController' => $baseDir . '/back/controllers/override.php',
    'AcyMailing\\Controllers\\PluginsController' => $baseDir . '/back/controllers/plugins.php',
    'AcyMailing\\Controllers\\Plugins\\Available' => $baseDir . '/back/controllers/plugins/Available.php',
    'AcyMailing\\Controllers\\Plugins\\Installed' => $baseDir . '/back/controllers/plugins/Installed.php',
    'AcyMailing\\Controllers\\QueueController' => $baseDir . '/back/controllers/queue.php',
    'AcyMailing\\Controllers\\Queue\\Campaigns' => $baseDir . '/back/controllers/queue/Campaigns.php',
    'AcyMailing\\Controllers\\Queue\\Detailed' => $baseDir . '/back/controllers/queue/Detailed.php',
    'AcyMailing\\Controllers\\Queue\\Scheduled' => $baseDir . '/back/controllers/queue/Scheduled.php',
    'AcyMailing\\Controllers\\SegmentsController' => $baseDir . '/back/controllers/segments.php',
    'AcyMailing\\Controllers\\Segments\\Campaign' => $baseDir . '/back/controllers/segments/Campaign.php',
    'AcyMailing\\Controllers\\Segments\\Edition' => $baseDir . '/back/controllers/segments/Edition.php',
    'AcyMailing\\Controllers\\Segments\\Listing' => $baseDir . '/back/controllers/segments/Listing.php',
    'AcyMailing\\Controllers\\StatsController' => $baseDir . '/back/controllers/stats.php',
    'AcyMailing\\Controllers\\Stats\\ClickMap' => $baseDir . '/back/controllers/stats/ClickMap.php',
    'AcyMailing\\Controllers\\Stats\\Detailed' => $baseDir . '/back/controllers/stats/Detailed.php',
    'AcyMailing\\Controllers\\Stats\\GlobalStats' => $baseDir . '/back/controllers/stats/GlobalStats.php',
    'AcyMailing\\Controllers\\Stats\\LinksDetails' => $baseDir . '/back/controllers/stats/LinksDetails.php',
    'AcyMailing\\Controllers\\Stats\\Lists' => $baseDir . '/back/controllers/stats/Lists.php',
    'AcyMailing\\Controllers\\Stats\\UserLinksDetails' => $baseDir . '/back/controllers/stats/UserLinksDetails.php',
    'AcyMailing\\Controllers\\ToggleController' => $baseDir . '/back/controllers/toggle.php',
    'AcyMailing\\Controllers\\UpdateController' => $baseDir . '/back/controllers/update.php',
    'AcyMailing\\Controllers\\UsersController' => $baseDir . '/back/controllers/users.php',
    'AcyMailing\\Controllers\\Users\\Edition' => $baseDir . '/back/controllers/users/Edition.php',
    'AcyMailing\\Controllers\\Users\\Export' => $baseDir . '/back/controllers/users/Export.php',
    'AcyMailing\\Controllers\\Users\\Import' => $baseDir . '/back/controllers/users/Import.php',
    'AcyMailing\\Controllers\\Users\\Listing' => $baseDir . '/back/controllers/users/Listing.php',
    'AcyMailing\\Controllers\\Users\\Subscription' => $baseDir . '/back/controllers/users/Subscription.php',
    'AcyMailing\\Controllers\\ZonesController' => $baseDir . '/back/controllers/zones.php',
    'AcyMailing\\FrontControllers\\ApiController' => $baseDir . '/front/controllers/api.php',
    'AcyMailing\\FrontControllers\\Api\\Campaigns' => $baseDir . '/front/controllers/api/Campaigns.php',
    'AcyMailing\\FrontControllers\\Api\\Emails' => $baseDir . '/front/controllers/api/Emails.php',
    'AcyMailing\\FrontControllers\\Api\\FollowUp' => $baseDir . '/front/controllers/api/FollowUp.php',
    'AcyMailing\\FrontControllers\\Api\\Lists' => $baseDir . '/front/controllers/api/Lists.php',
    'AcyMailing\\FrontControllers\\Api\\Statistics' => $baseDir . '/front/controllers/api/Statistics.php',
    'AcyMailing\\FrontControllers\\Api\\Subscription' => $baseDir . '/front/controllers/api/Subscription.php',
    'AcyMailing\\FrontControllers\\Api\\Templates' => $baseDir . '/front/controllers/api/Templates.php',
    'AcyMailing\\FrontControllers\\Api\\Users' => $baseDir . '/front/controllers/api/Users.php',
    'AcyMailing\\FrontControllers\\ArchiveController' => $baseDir . '/front/controllers/archive.php',
    'AcyMailing\\FrontControllers\\CronController' => $baseDir . '/front/controllers/cron.php',
    'AcyMailing\\FrontControllers\\FrontcampaignsController' => $baseDir . '/front/controllers/frontcampaigns.php',
    'AcyMailing\\FrontControllers\\FrontconfigurationController' => $baseDir . '/front/controllers/frontconfiguration.php',
    'AcyMailing\\FrontControllers\\FrontdynamicsController' => $baseDir . '/front/controllers/frontdynamics.php',
    'AcyMailing\\FrontControllers\\FrontentityselectController' => $baseDir . '/front/controllers/frontentityselect.php',
    'AcyMailing\\FrontControllers\\FrontfileController' => $baseDir . '/front/controllers/frontfile.php',
    'AcyMailing\\FrontControllers\\FrontlistsController' => $baseDir . '/front/controllers/frontlists.php',
    'AcyMailing\\FrontControllers\\FrontmailsController' => $baseDir . '/front/controllers/frontmails.php',
    'AcyMailing\\FrontControllers\\FrontservicesController' => $baseDir . '/front/controllers/frontservices.php',
    'AcyMailing\\FrontControllers\\FrontstatsController' => $baseDir . '/front/controllers/frontstats.php',
    'AcyMailing\\FrontControllers\\FronttoggleController' => $baseDir . '/front/controllers/fronttoggle.php',
    'AcyMailing\\FrontControllers\\FronturlController' => $baseDir . '/front/controllers/fronturl.php',
    'AcyMailing\\FrontControllers\\FrontusersController' => $baseDir . '/front/controllers/frontusers.php',
    'AcyMailing\\FrontControllers\\FrontzonesController' => $baseDir . '/front/controllers/frontzones.php',
    'AcyMailing\\FrontControllers\\ModuleloaderController' => $baseDir . '/front/controllers/moduleloader.php',
    'AcyMailing\\FrontViews\\ArchiveViewArchive' => $baseDir . '/front/views/archive/view.html.php',
    'AcyMailing\\FrontViews\\FrontcampaignsViewFrontcampaigns' => $baseDir . '/front/views/frontcampaigns/view.html.php',
    'AcyMailing\\FrontViews\\FrontdynamicsViewFrontdynamics' => $baseDir . '/front/views/frontdynamics/view.html.php',
    'AcyMailing\\FrontViews\\FrontfileViewFrontfile' => $baseDir . '/front/views/frontfile/view.html.php',
    'AcyMailing\\FrontViews\\FrontlistsViewFrontlists' => $baseDir . '/front/views/frontlists/view.html.php',
    'AcyMailing\\FrontViews\\FrontmailsViewFrontmails' => $baseDir . '/front/views/frontmails/view.html.php',
    'AcyMailing\\FrontViews\\FrontusersViewFrontusers' => $baseDir . '/front/views/frontusers/view.html.php',
    'AcyMailing\\Helpers\\AcyCssInliner' => $baseDir . '/back/helpers/cssInliner.php',
    'AcyMailing\\Helpers\\AutomationHelper' => $baseDir . '/back/helpers/automation.php',
    'AcyMailing\\Helpers\\BounceHelper' => $baseDir . '/back/helpers/bounce.php',
    'AcyMailing\\Helpers\\CaptchaHelper' => $baseDir . '/back/helpers/captcha.php',
    'AcyMailing\\Helpers\\CronHelper' => $baseDir . '/back/helpers/cron.php',
    'AcyMailing\\Helpers\\EditorHelper' => $baseDir . '/back/helpers/editor.php',
    'AcyMailing\\Helpers\\EncodingHelper' => $baseDir . '/back/helpers/encoding.php',
    'AcyMailing\\Helpers\\EntitySelectHelper' => $baseDir . '/back/helpers/entitySelect.php',
    'AcyMailing\\Helpers\\ExportHelper' => $baseDir . '/back/helpers/export.php',
    'AcyMailing\\Helpers\\HeaderHelper' => $baseDir . '/back/helpers/header.php',
    'AcyMailing\\Helpers\\ImageHelper' => $baseDir . '/back/helpers/image.php',
    'AcyMailing\\Helpers\\ImportHelper' => $baseDir . '/back/helpers/import.php',
    'AcyMailing\\Helpers\\MailboxHelper' => $baseDir . '/back/helpers/maibox.php',
    'AcyMailing\\Helpers\\MailerHelper' => $baseDir . '/back/helpers/mailer.php',
    'AcyMailing\\Helpers\\MigrationHelper' => $baseDir . '/back/helpers/migration.php',
    'AcyMailing\\Helpers\\PaginationHelper' => $baseDir . '/back/helpers/pagination.php',
    'AcyMailing\\Helpers\\PluginHelper' => $baseDir . '/back/helpers/plugin.php',
    'AcyMailing\\Helpers\\QueueHelper' => $baseDir . '/back/helpers/queue.php',
    'AcyMailing\\Helpers\\RegacyHelper' => $baseDir . '/back/helpers/regacy.php',
    'AcyMailing\\Helpers\\SplashscreenHelper' => $baseDir . '/back/helpers/splashscreen.php',
    'AcyMailing\\Helpers\\TabHelper' => $baseDir . '/back/helpers/tab.php',
    'AcyMailing\\Helpers\\ToolbarHelper' => $baseDir . '/back/helpers/toolbar.php',
    'AcyMailing\\Helpers\\UpdateHelper' => $baseDir . '/back/helpers/update.php',
    'AcyMailing\\Helpers\\Update\\Cms' => $baseDir . '/back/helpers/update/Cms.php',
    'AcyMailing\\Helpers\\Update\\Configuration' => $baseDir . '/back/helpers/update/Configuration.php',
    'AcyMailing\\Helpers\\Update\\DefaultData' => $baseDir . '/back/helpers/update/DefaultData.php',
    'AcyMailing\\Helpers\\Update\\Patchv6' => $baseDir . '/back/helpers/update/Patchv6.php',
    'AcyMailing\\Helpers\\Update\\Patchv7' => $baseDir . '/back/helpers/update/Patchv7.php',
    'AcyMailing\\Helpers\\Update\\Patchv8' => $baseDir . '/back/helpers/update/Patchv8.php',
    'AcyMailing\\Helpers\\Update\\Patchv9' => $baseDir . '/back/helpers/update/Patchv9.php',
    'AcyMailing\\Helpers\\Update\\SQLPatch' => $baseDir . '/back/helpers/update/SQLPatch.php',
    'AcyMailing\\Helpers\\UpdatemeHelper' => $baseDir . '/back/helpers/updateme.php',
    'AcyMailing\\Helpers\\UserHelper' => $baseDir . '/back/helpers/user.php',
    'AcyMailing\\Helpers\\WorkflowHelper' => $baseDir . '/back/helpers/workflow.php',
    'AcyMailing\\Init\\ElementorForm' => $baseDir . '/wpinit/elementorForm.php',
    'AcyMailing\\Init\\acyActivation' => $baseDir . '/wpinit/activation.php',
    'AcyMailing\\Init\\acyAddons' => $baseDir . '/wpinit/addons.php',
    'AcyMailing\\Init\\acyBeaver' => $baseDir . '/wpinit/beaver.php',
    'AcyMailing\\Init\\acyCron' => $baseDir . '/wpinit/cron.php',
    'AcyMailing\\Init\\acyDeactivate' => $baseDir . '/wpinit/deactivate.php',
    'AcyMailing\\Init\\acyElementor' => $baseDir . '/wpinit/elementor.php',
    'AcyMailing\\Init\\acyFakePhpMailer' => $baseDir . '/wpinit/fake_phpmailer.php',
    'AcyMailing\\Init\\acyForms' => $baseDir . '/wpinit/forms.php',
    'AcyMailing\\Init\\acyGutenberg' => $baseDir . '/wpinit/gutenberg.php',
    'AcyMailing\\Init\\acyMenu' => $baseDir . '/wpinit/menu.php',
    'AcyMailing\\Init\\acyMessage' => $baseDir . '/wpinit/message.php',
    'AcyMailing\\Init\\acyOauth' => $baseDir . '/wpinit/Oauth.php',
    'AcyMailing\\Init\\acyOverrideEmail' => $baseDir . '/wpinit/override_email.php',
    'AcyMailing\\Init\\acyRouter' => $baseDir . '/wpinit/router.php',
    'AcyMailing\\Init\\acySecurity' => $baseDir . '/wpinit/security.php',
    'AcyMailing\\Init\\acyUpdate' => $baseDir . '/wpinit/update.php',
    'AcyMailing\\Init\\acyUsersynch' => $baseDir . '/wpinit/usersynch.php',
    'AcyMailing\\Init\\acyWpRocket' => $baseDir . '/wpinit/wprocket.php',
    'AcyMailing\\Libraries\\acymClass' => $baseDir . '/back/libraries/class.php',
    'AcyMailing\\Libraries\\acymController' => $baseDir . '/back/libraries/controller.php',
    'AcyMailing\\Libraries\\acymObject' => $baseDir . '/back/libraries/object.php',
    'AcyMailing\\Libraries\\acymParameter' => $baseDir . '/back/libraries/parameter.php',
    'AcyMailing\\Libraries\\acymPlugin' => $baseDir . '/back/libraries/plugin.php',
    'AcyMailing\\Libraries\\acymView' => $baseDir . '/back/libraries/view.php',
    'AcyMailing\\Libraries\\acympunycode' => $baseDir . '/back/libraries/punycode.php',
    'AcyMailing\\Types\\AclType' => $baseDir . '/back/types/acl.php',
    'AcyMailing\\Types\\CharsetType' => $baseDir . '/back/types/charset.php',
    'AcyMailing\\Types\\DelayType' => $baseDir . '/back/types/delay.php',
    'AcyMailing\\Types\\FailactionType' => $baseDir . '/back/types/failaction.php',
    'AcyMailing\\Types\\FileTreeType' => $baseDir . '/back/types/fileTree.php',
    'AcyMailing\\Types\\OperatorType' => $baseDir . '/back/types/operator.php',
    'AcyMailing\\Types\\OperatorinType' => $baseDir . '/back/types/operatorin.php',
    'AcyMailing\\Types\\StepsType' => $baseDir . '/back/types/steps.php',
    'AcyMailing\\Types\\UploadfileType' => $baseDir . '/back/types/uploadFile.php',
    'AcyMailing\\Views\\AutomationViewAutomation' => $baseDir . '/back/views/automation/view.html.php',
    'AcyMailing\\Views\\BouncesViewBounces' => $baseDir . '/back/views/bounces/view.html.php',
    'AcyMailing\\Views\\CampaignsViewCampaigns' => $baseDir . '/back/views/campaigns/view.html.php',
    'AcyMailing\\Views\\ConfigurationViewConfiguration' => $baseDir . '/back/views/configuration/view.html.php',
    'AcyMailing\\Views\\DashboardViewDashboard' => $baseDir . '/back/views/dashboard/view.html.php',
    'AcyMailing\\Views\\DynamicsViewDynamics' => $baseDir . '/back/views/dynamics/view.html.php',
    'AcyMailing\\Views\\FieldsViewFields' => $baseDir . '/back/views/fields/view.html.php',
    'AcyMailing\\Views\\FileViewFile' => $baseDir . '/back/views/file/view.html.php',
    'AcyMailing\\Views\\FormsViewForms' => $baseDir . '/back/views/forms/view.html.php',
    'AcyMailing\\Views\\GoproViewGopro' => $baseDir . '/back/views/gopro/view.html.php',
    'AcyMailing\\Views\\LanguageViewLanguage' => $baseDir . '/back/views/language/view.html.php',
    'AcyMailing\\Views\\ListsViewLists' => $baseDir . '/back/views/lists/view.html.php',
    'AcyMailing\\Views\\MailsViewMails' => $baseDir . '/back/views/mails/view.html.php',
    'AcyMailing\\Views\\OverrideViewOverride' => $baseDir . '/back/views/override/view.html.php',
    'AcyMailing\\Views\\PluginsViewPlugins' => $baseDir . '/back/views/plugins/view.html.php',
    'AcyMailing\\Views\\QueueViewQueue' => $baseDir . '/back/views/queue/view.html.php',
    'AcyMailing\\Views\\SegmentsViewSegments' => $baseDir . '/back/views/segments/view.html.php',
    'AcyMailing\\Views\\StatsViewStats' => $baseDir . '/back/views/stats/view.html.php',
    'AcyMailing\\Views\\UsersViewUsers' => $baseDir . '/back/views/users/view.html.php',
    'Pelago\\Emogrifier\\CssInliner' => $baseDir . '/front/libraries/pelago/emogrifier/src/CssInliner.php',
    'Pelago\\Emogrifier\\Css\\CssDocument' => $baseDir . '/front/libraries/pelago/emogrifier/src/Css/CssDocument.php',
    'Pelago\\Emogrifier\\Css\\StyleRule' => $baseDir . '/front/libraries/pelago/emogrifier/src/Css/StyleRule.php',
    'Pelago\\Emogrifier\\HtmlProcessor\\AbstractHtmlProcessor' => $baseDir . '/front/libraries/pelago/emogrifier/src/HtmlProcessor/AbstractHtmlProcessor.php',
    'Pelago\\Emogrifier\\HtmlProcessor\\CssToAttributeConverter' => $baseDir . '/front/libraries/pelago/emogrifier/src/HtmlProcessor/CssToAttributeConverter.php',
    'Pelago\\Emogrifier\\HtmlProcessor\\HtmlNormalizer' => $baseDir . '/front/libraries/pelago/emogrifier/src/HtmlProcessor/HtmlNormalizer.php',
    'Pelago\\Emogrifier\\HtmlProcessor\\HtmlPruner' => $baseDir . '/front/libraries/pelago/emogrifier/src/HtmlProcessor/HtmlPruner.php',
    'Pelago\\Emogrifier\\Utilities\\ArrayIntersector' => $baseDir . '/front/libraries/pelago/emogrifier/src/Utilities/ArrayIntersector.php',
    'Pelago\\Emogrifier\\Utilities\\CssConcatenator' => $baseDir . '/front/libraries/pelago/emogrifier/src/Utilities/CssConcatenator.php',
    'Psr\\Container\\ContainerExceptionInterface' => $vendorDir . '/psr/container/src/ContainerExceptionInterface.php',
    'Psr\\Container\\ContainerInterface' => $vendorDir . '/psr/container/src/ContainerInterface.php',
    'Psr\\Container\\NotFoundExceptionInterface' => $vendorDir . '/psr/container/src/NotFoundExceptionInterface.php',
    'Sabberworm\\CSS\\CSSList\\AtRuleBlockList' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/CSSList/AtRuleBlockList.php',
    'Sabberworm\\CSS\\CSSList\\CSSBlockList' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/CSSList/CSSBlockList.php',
    'Sabberworm\\CSS\\CSSList\\CSSList' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/CSSList/CSSList.php',
    'Sabberworm\\CSS\\CSSList\\Document' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/CSSList/Document.php',
    'Sabberworm\\CSS\\CSSList\\KeyFrame' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/CSSList/KeyFrame.php',
    'Sabberworm\\CSS\\Comment\\Comment' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Comment/Comment.php',
    'Sabberworm\\CSS\\Comment\\Commentable' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Comment/Commentable.php',
    'Sabberworm\\CSS\\OutputFormat' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/OutputFormat.php',
    'Sabberworm\\CSS\\OutputFormatter' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/OutputFormatter.php',
    'Sabberworm\\CSS\\Parser' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Parser.php',
    'Sabberworm\\CSS\\Parsing\\OutputException' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Parsing/OutputException.php',
    'Sabberworm\\CSS\\Parsing\\ParserState' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Parsing/ParserState.php',
    'Sabberworm\\CSS\\Parsing\\SourceException' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Parsing/SourceException.php',
    'Sabberworm\\CSS\\Parsing\\UnexpectedEOFException' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Parsing/UnexpectedEOFException.php',
    'Sabberworm\\CSS\\Parsing\\UnexpectedTokenException' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Parsing/UnexpectedTokenException.php',
    'Sabberworm\\CSS\\Property\\AtRule' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Property/AtRule.php',
    'Sabberworm\\CSS\\Property\\CSSNamespace' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Property/CSSNamespace.php',
    'Sabberworm\\CSS\\Property\\Charset' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Property/Charset.php',
    'Sabberworm\\CSS\\Property\\Import' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Property/Import.php',
    'Sabberworm\\CSS\\Property\\KeyframeSelector' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Property/KeyframeSelector.php',
    'Sabberworm\\CSS\\Property\\Selector' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Property/Selector.php',
    'Sabberworm\\CSS\\Renderable' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Renderable.php',
    'Sabberworm\\CSS\\RuleSet\\AtRuleSet' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/RuleSet/AtRuleSet.php',
    'Sabberworm\\CSS\\RuleSet\\DeclarationBlock' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/RuleSet/DeclarationBlock.php',
    'Sabberworm\\CSS\\RuleSet\\RuleSet' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/RuleSet/RuleSet.php',
    'Sabberworm\\CSS\\Rule\\Rule' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Rule/Rule.php',
    'Sabberworm\\CSS\\Settings' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Settings.php',
    'Sabberworm\\CSS\\Value\\CSSFunction' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/CSSFunction.php',
    'Sabberworm\\CSS\\Value\\CSSString' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/CSSString.php',
    'Sabberworm\\CSS\\Value\\CalcFunction' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/CalcFunction.php',
    'Sabberworm\\CSS\\Value\\CalcRuleValueList' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/CalcRuleValueList.php',
    'Sabberworm\\CSS\\Value\\Color' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/Color.php',
    'Sabberworm\\CSS\\Value\\LineName' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/LineName.php',
    'Sabberworm\\CSS\\Value\\PrimitiveValue' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/PrimitiveValue.php',
    'Sabberworm\\CSS\\Value\\RuleValueList' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/RuleValueList.php',
    'Sabberworm\\CSS\\Value\\Size' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/Size.php',
    'Sabberworm\\CSS\\Value\\URL' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/URL.php',
    'Sabberworm\\CSS\\Value\\Value' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/Value.php',
    'Sabberworm\\CSS\\Value\\ValueList' => $baseDir . '/front/libraries/sabberworm/php-css-parser/src/Value/ValueList.php',
    'Symfony\\Component\\CssSelector\\CssSelectorConverter' => $baseDir . '/front/libraries/symfony/css-selector/CssSelectorConverter.php',
    'Symfony\\Component\\CssSelector\\Exception\\ExceptionInterface' => $baseDir . '/front/libraries/symfony/css-selector/Exception/ExceptionInterface.php',
    'Symfony\\Component\\CssSelector\\Exception\\ExpressionErrorException' => $baseDir . '/front/libraries/symfony/css-selector/Exception/ExpressionErrorException.php',
    'Symfony\\Component\\CssSelector\\Exception\\InternalErrorException' => $baseDir . '/front/libraries/symfony/css-selector/Exception/InternalErrorException.php',
    'Symfony\\Component\\CssSelector\\Exception\\ParseException' => $baseDir . '/front/libraries/symfony/css-selector/Exception/ParseException.php',
    'Symfony\\Component\\CssSelector\\Exception\\SyntaxErrorException' => $baseDir . '/front/libraries/symfony/css-selector/Exception/SyntaxErrorException.php',
    'Symfony\\Component\\CssSelector\\Node\\AbstractNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/AbstractNode.php',
    'Symfony\\Component\\CssSelector\\Node\\AttributeNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/AttributeNode.php',
    'Symfony\\Component\\CssSelector\\Node\\ClassNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/ClassNode.php',
    'Symfony\\Component\\CssSelector\\Node\\CombinedSelectorNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/CombinedSelectorNode.php',
    'Symfony\\Component\\CssSelector\\Node\\ElementNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/ElementNode.php',
    'Symfony\\Component\\CssSelector\\Node\\FunctionNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/FunctionNode.php',
    'Symfony\\Component\\CssSelector\\Node\\HashNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/HashNode.php',
    'Symfony\\Component\\CssSelector\\Node\\NegationNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/NegationNode.php',
    'Symfony\\Component\\CssSelector\\Node\\NodeInterface' => $baseDir . '/front/libraries/symfony/css-selector/Node/NodeInterface.php',
    'Symfony\\Component\\CssSelector\\Node\\PseudoNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/PseudoNode.php',
    'Symfony\\Component\\CssSelector\\Node\\SelectorNode' => $baseDir . '/front/libraries/symfony/css-selector/Node/SelectorNode.php',
    'Symfony\\Component\\CssSelector\\Node\\Specificity' => $baseDir . '/front/libraries/symfony/css-selector/Node/Specificity.php',
    'Symfony\\Component\\CssSelector\\Parser\\Handler\\CommentHandler' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Handler/CommentHandler.php',
    'Symfony\\Component\\CssSelector\\Parser\\Handler\\HandlerInterface' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Handler/HandlerInterface.php',
    'Symfony\\Component\\CssSelector\\Parser\\Handler\\HashHandler' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Handler/HashHandler.php',
    'Symfony\\Component\\CssSelector\\Parser\\Handler\\IdentifierHandler' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Handler/IdentifierHandler.php',
    'Symfony\\Component\\CssSelector\\Parser\\Handler\\NumberHandler' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Handler/NumberHandler.php',
    'Symfony\\Component\\CssSelector\\Parser\\Handler\\StringHandler' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Handler/StringHandler.php',
    'Symfony\\Component\\CssSelector\\Parser\\Handler\\WhitespaceHandler' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Handler/WhitespaceHandler.php',
    'Symfony\\Component\\CssSelector\\Parser\\Parser' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Parser.php',
    'Symfony\\Component\\CssSelector\\Parser\\ParserInterface' => $baseDir . '/front/libraries/symfony/css-selector/Parser/ParserInterface.php',
    'Symfony\\Component\\CssSelector\\Parser\\Reader' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Reader.php',
    'Symfony\\Component\\CssSelector\\Parser\\Shortcut\\ClassParser' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Shortcut/ClassParser.php',
    'Symfony\\Component\\CssSelector\\Parser\\Shortcut\\ElementParser' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Shortcut/ElementParser.php',
    'Symfony\\Component\\CssSelector\\Parser\\Shortcut\\EmptyStringParser' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Shortcut/EmptyStringParser.php',
    'Symfony\\Component\\CssSelector\\Parser\\Shortcut\\HashParser' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Shortcut/HashParser.php',
    'Symfony\\Component\\CssSelector\\Parser\\Token' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Token.php',
    'Symfony\\Component\\CssSelector\\Parser\\TokenStream' => $baseDir . '/front/libraries/symfony/css-selector/Parser/TokenStream.php',
    'Symfony\\Component\\CssSelector\\Parser\\Tokenizer\\Tokenizer' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Tokenizer/Tokenizer.php',
    'Symfony\\Component\\CssSelector\\Parser\\Tokenizer\\TokenizerEscaping' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Tokenizer/TokenizerEscaping.php',
    'Symfony\\Component\\CssSelector\\Parser\\Tokenizer\\TokenizerPatterns' => $baseDir . '/front/libraries/symfony/css-selector/Parser/Tokenizer/TokenizerPatterns.php',
    'Symfony\\Component\\CssSelector\\XPath\\Extension\\AbstractExtension' => $baseDir . '/front/libraries/symfony/css-selector/XPath/Extension/AbstractExtension.php',
    'Symfony\\Component\\CssSelector\\XPath\\Extension\\AttributeMatchingExtension' => $baseDir . '/front/libraries/symfony/css-selector/XPath/Extension/AttributeMatchingExtension.php',
    'Symfony\\Component\\CssSelector\\XPath\\Extension\\CombinationExtension' => $baseDir . '/front/libraries/symfony/css-selector/XPath/Extension/CombinationExtension.php',
    'Symfony\\Component\\CssSelector\\XPath\\Extension\\ExtensionInterface' => $baseDir . '/front/libraries/symfony/css-selector/XPath/Extension/ExtensionInterface.php',
    'Symfony\\Component\\CssSelector\\XPath\\Extension\\FunctionExtension' => $baseDir . '/front/libraries/symfony/css-selector/XPath/Extension/FunctionExtension.php',
    'Symfony\\Component\\CssSelector\\XPath\\Extension\\HtmlExtension' => $baseDir . '/front/libraries/symfony/css-selector/XPath/Extension/HtmlExtension.php',
    'Symfony\\Component\\CssSelector\\XPath\\Extension\\NodeExtension' => $baseDir . '/front/libraries/symfony/css-selector/XPath/Extension/NodeExtension.php',
    'Symfony\\Component\\CssSelector\\XPath\\Extension\\PseudoClassExtension' => $baseDir . '/front/libraries/symfony/css-selector/XPath/Extension/PseudoClassExtension.php',
    'Symfony\\Component\\CssSelector\\XPath\\Translator' => $baseDir . '/front/libraries/symfony/css-selector/XPath/Translator.php',
    'Symfony\\Component\\CssSelector\\XPath\\TranslatorInterface' => $baseDir . '/front/libraries/symfony/css-selector/XPath/TranslatorInterface.php',
    'Symfony\\Component\\CssSelector\\XPath\\XPathExpr' => $baseDir . '/front/libraries/symfony/css-selector/XPath/XPathExpr.php',
);
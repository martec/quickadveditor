<?php
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Plugin info
function quickadveditor_info ()
{

	global $db, $lang;

	$lang->load('config_quickadveditor');

	return array(
		"name"			  => "Quick Advanced Editor",
		"description"	 => $lang->quickadveditor_plug_desc,
		"website"		 => "",
		"author"		=> "martec",
		"authorsite"	=> "",
		"version"		 => "5.5.1",
		"guid"			   => "",
		"compatibility" => "18*"
	);
}

function quickadveditor_install()
{
	global $db, $lang;

	$lang->load('config_quickadveditor');

	$groupid = $db->insert_query('settinggroups', array(
		'name'		=> 'quickadveditor',
		'title'		=> 'Quick Advanced Editor',
		'description'	=> $lang->quickadveditor_sett_desc,
		'disporder'	=> $dorder,
		'isdefault'	=> '0'
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_qurp_heigh',
		'title'		=> $lang->quickadveditor_qurp_heigh_title,
		'description'	=> $lang->quickadveditor_qurp_heigh_desc,
		'optionscode'	=> 'text',
		'value'		=> '280',
		'disporder'	=> '1',
		'gid'		=> $groupid
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_qued_heigh',
		'title'		=> $lang->quickadveditor_qued_heigh_title,
		'description'	=> $lang->quickadveditor_qued_heigh_desc,
		'optionscode'	=> 'text',
		'value'		=> '300',
		'disporder'	=> '2',
		'gid'		=> $groupid
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_smile',
		'title'		=> $lang->quickadveditor_smile_title,
		'description'	=> $lang->quickadveditor_smile_desc,
		'optionscode'	=> 'onoff',
		'value'		=> '0',
		'disporder'	=> '3',
		'gid'		=> $groupid
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_qedit',
		'title'		=> $lang->quickadveditor_qedit_title,
		'description'	=> $lang->quickadveditor_qedit_desc,
		'optionscode'	=> 'onoff',
		'value'		=> '1',
		'disporder'	=> '4',
		'gid'		=> $groupid
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_autosave',
		'title'		=> $lang->quickadveditor_autosave_title,
		'description'	=> $lang->quickadveditor_autosave_desc,
		'optionscode'	=> 'onoff',
		'value'		=> '1',
		'disporder'	=> '5',
		'gid'		=> $groupid
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_canonicallink',
		'title'		=> $lang->quickadveditor_canonical_title,
		'description'	=> $lang->quickadveditor_canonical_desc,
		'optionscode'	=> 'onoff',
		'value'		=> '1',
		'disporder'	=> '6',
		'gid'		=> $groupid
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_save_lang',
		'title'		=> $lang->quickadveditor_save_title,
		'description'	=> $lang->quickadveditor_save_desc,
		'optionscode'	=> 'text',
		'value'		=> $lang->quickadveditor_save_default,
		'disporder'	=> '7',
		'gid'		=> $groupid
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_restor_lang',
		'title'		=> $lang->quickadveditor_restor_title,
		'description'	=> $lang->quickadveditor_restor_desc,
		'optionscode'	=> 'text',
		'value'		=> $lang->quickadveditor_restor_default,
		'disporder'	=> '8',
		'gid'		=> $groupid
	));

	rebuild_settings();
}

function quickadveditor_is_installed()
{
	global $db;

	$query = $db->simple_select("settinggroups", "COUNT(*) as rows", "name = 'quickadveditor'");
	$rows  = $db->fetch_field($query, 'rows');

	return ($rows > 0);
}

function quickadveditor_uninstall()
{
	global $db;

	$db->write_query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name IN('quickadveditor_smile')");
	$db->write_query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name IN('quickadveditor_qedit')");
	$db->write_query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name IN('quickadveditor_autosave')");
	$db->write_query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name IN('quickadveditor_canonicallink')");
	$db->write_query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name IN('quickadveditor_save_lang')");
	$db->write_query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name IN('quickadveditor_restor_lang')");
	$db->delete_query("settinggroups", "name = 'quickadveditor'");
}

function quickadveditor_activate()
{
	global $db;
	include_once MYBB_ROOT.'inc/adminfunctions_templates.php';

	$template = array(
		"tid"		 => NULL,
		"title"		   => "codebutquick",
		"template"	  => "<link rel=\"stylesheet\" href=\"{\$mybb->asset_url}/jscripts/sceditor/editor_themes/{\$theme[\'editortheme\']}\" type=\"text/css\" media=\"all\" />
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/sceditor/jquery.sceditor.bbcode.min.js\"></script>
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/bbcodes_sceditor.js\"></script>
<script type=\"text/javascript\">
var partialmode = {\$mybb->settings[\'partialmode\']},
opt_editor = {
	plugins: \"bbcode\",
	style: \"{\$mybb->asset_url}/jscripts/sceditor/jquery.sceditor.mybb.css\",
	rtl: {\$lang->settings[\'rtl\']},
	locale: \"mybblang\",
	enablePasteFiltering: true,
	emoticonsEnabled: {\$emoticons_enabled},
	emoticons: {
		// Emoticons to be included in the dropdown
		dropdown: {
			{\$dropdownsmilies}
		},
		// Emoticons to be included in the more section
		more: {
			{\$moresmilies}
		},
		// Emoticons that are not shown in the dropdown but will still be converted. Can be used for things like aliases
		hidden: {
			{\$hiddensmilies}
		}
	},
	emoticonsCompat: true,
	toolbar: \"{\$basic1}{\$align}{\$font}{\$size}{\$color}{\$removeformat}{\$basic2}image,{\$email}{\$link}|video{\$emoticon}|{\$list}{\$code}quote|maximize,source\",
};
{\$editor_language}

if({\$mybb->settings[\'quickadveditor_qedit\']}!=0) {
	(\$.fn.on || \$.fn.live).call(\$(document), \'click\', \'.quick_edit_button\', function () {
		\$.jGrowl(\'<img src=\"images/spinner_big.gif\" />\');
		ed_id = \$(this).attr(\'id\');
		var pid = ed_id.replace( /[^0-9]/g, \'\');
		\$(\'#quickedit_\'+pid).height({\$mybb->settings[\'quickadveditor_qued_heigh\']}+\'px\');
		setTimeout(function() {
			\$(\'#quickedit_\'+pid).sceditor(opt_editor);
			if (\$(\'#quickedit_\'+pid).sceditor(\'instance\')) {
				\$(\'#quickedit_\'+pid).sceditor(\'instance\').focus();
			}
			offset = \$(\'#quickedit_\'+pid).next().offset().top - 60;
			setTimeout(function() {
				\$(\'html, body\').animate({
					scrollTop: offset
				}, 700);
				setTimeout(function() {
					\$(\'#pid_\'+pid).find(\'button[type=\"submit\"]\').attr( \'id\', \'quicksub_\'+pid );
				},200);
				if($(\".jGrowl-notification:last-child\").length) {
					$(\".jGrowl-notification:last-child\").remove();
				}
			},200);
			if(\'{\$sourcemode}\' != \'\') {
				\$(\'textarea[name*=\"value\"]\').sceditor(\'instance\').sourceMode(true);
			}
		},400);
	});
}

(\$.fn.on || \$.fn.live).call(\$(document), \'click\', \'button[id*=\"quicksub_\"]\', function () {
	ed_id = \$(this).attr(\'id\');
	pid = ed_id.replace( /[^0-9]/g, \'\');
	\$(\'#quickedit_\'+pid).sceditor(\'instance\').updateOriginal();
});


\$(document).ready(function() {
	\$(\'#message\').height({\$mybb->settings[\'quickadveditor_qurp_heigh\']}+\'px\');
	var link_can = document.querySelector(\"link[rel=\'canonical\']\");
	\$(\'#message\').sceditor(opt_editor);
	MyBBEditor = $(\'#message\').sceditor(\'instance\');
	{\$sourcemode}
	if({\$mybb->settings[\'quickadveditor_autosave\']}!=0) {
		setInterval(function() {
			if (MyBBEditor) {
				if (MyBBEditor.val() != localStorage.getItem(link_can + \'quickreply\')) {
					if (MyBBEditor.val()) {
						if(!\$(\'#autosave\').length) {
							\$(\'<div/>\', { id: \'autosave\', class: \'bottom-right\' }).appendTo(\'body\');
						}
						setTimeout(function() {
							\$(\'#autosave\').jGrowl(\'{\$mybb->settings[\'quickadveditor_save_lang\']}\', { life: 500 });
						},200);
						localStorage.setItem(link_can + \'quickreply\', MyBBEditor.val());
					}
					else {
						localStorage.removeItem(link_can + \'quickreply\');
					}
				}
			}
		},15000);

		setTimeout(function() {
			restitem = localStorage.getItem(link_can + \'quickreply\');
			if (restitem) {
				var restorebut = [
					\'<a class=\"sceditor-button\" title=\"{\$mybb->settings[\'quickadveditor_restor_lang\']}\" onclick=\"MyBBEditor.insert(restitem);\">\',
						\'<div style=\"background-image: url(images/rest.png); opacity: 1; cursor: pointer;\">{\$mybb->settings[\'quickadveditor_restor_lang\']}</div>\',
					\'</a>\'
				];

				\$(restorebut.join(\'\')).appendTo(\'.sceditor-group:last\');
			}
		},600);
		MyBBEditor.blur(function(e) {
			if (MyBBEditor.val()) {
				localStorage.setItem(link_can + \'quickreply\', MyBBEditor.val())
			}
			else {
				localStorage.removeItem(link_can + \'quickreply\');
			}
		});
	}
});

/**********************************
 * Thread compatibility functions *
 **********************************/
if(typeof Thread !== \'undefined\')
{
	var quickReplyFunc = Thread.quickReply;
	Thread.quickReply = function(e) {
		var link_can = document.querySelector(\"link[rel=\'canonical\']\").href;
		if(MyBBEditor) {
			MyBBEditor.updateOriginal();
			if({\$mybb->settings[\'quickadveditor_autosave\']}!=0) {
				localStorage.removeItem(link_can + \'quickreply\');
			}
			$(\'form[id*=\"quick_reply_form\"]\').bind(\'reset\', function() {
				MyBBEditor.val(\'\').emoticons(true);
			});
		}

		return quickReplyFunc.call(this, e);
	};
};
</script>",
		"sid"		 => "-1"
	);
	$db->insert_query("templates", $template);

	$template2 = array(
		"tid"		 => NULL,
		"title"		   => "codebutquick_pm",
		"template"	  => "<link rel=\"stylesheet\" href=\"{\$mybb->asset_url}/jscripts/sceditor/editor_themes/{\$theme[\'editortheme\']}\" type=\"text/css\" media=\"all\" />
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/sceditor/jquery.sceditor.bbcode.min.js\"></script>
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/bbcodes_sceditor.js\"></script>
<script type=\"text/javascript\">
var partialmode = {\$mybb->settings[\'partialmode\']},
opt_editor = {
	plugins: \"bbcode\",
	style: \"{\$mybb->asset_url}/jscripts/sceditor/jquery.sceditor.mybb.css\",
	rtl: {\$lang->settings[\'rtl\']},
	locale: \"mybblang\",
	enablePasteFiltering: true,
	emoticonsEnabled: {\$emoticons_enabled},
	emoticons: {
		// Emoticons to be included in the dropdown
		dropdown: {
			{\$dropdownsmilies}
		},
		// Emoticons to be included in the more section
		more: {
			{\$moresmilies}
		},
		// Emoticons that are not shown in the dropdown but will still be converted. Can be used for things like aliases
		hidden: {
			{\$hiddensmilies}
		}
	},
	emoticonsCompat: true,
	toolbar: \"{\$basic1}{\$align}{\$font}{\$size}{\$color}{\$removeformat}{\$basic2}image,{\$email}{\$link}|video{\$emoticon}|{\$list}{\$code}quote|maximize,source\",
};
{\$editor_language}

(\$.fn.on || \$.fn.live).call(\$(document), \'click\', \'input[accesskey*=\"s\"]\', function () {
	MyBBEditor.updateOriginal();
    localStorage.removeItem(location.href + \'quickreply\');
});

(\$.fn.on || \$.fn.live).call(\$(document), \'click\', \'input[name*=\"preview\"]\', function () {
	MyBBEditor.updateOriginal();
});

\$(document).ready(function() {
	\$(\'#message\').height({\$mybb->settings[\'quickadveditor_qurp_heigh\']}+\'px\');
	\$(\'#message\').sceditor(opt_editor);
	MyBBEditor = $(\'#message\').sceditor(\'instance\');
	{\$sourcemode}
	if({\$mybb->settings[\'quickadveditor_autosave\']}!=0) {
		setInterval(function() {
			if (MyBBEditor) {
				if (MyBBEditor.val() != localStorage.getItem(location.href + \'quickreply\')) {
					if (MyBBEditor.val()) {
						if(!\$(\'#autosave\').length) {
							\$(\'<div/>\', { id: \'autosave\', class: \'bottom-right\' }).appendTo(\'body\');
						}
						setTimeout(function() {
							\$(\'#autosave\').jGrowl(\'{\$mybb->settings[\'quickadveditor_save_lang\']}\', { life: 500 });
						},200);
						localStorage.setItem(location.href + \'quickreply\', MyBBEditor.val());
					}
					else {
						localStorage.removeItem(location.href + \'quickreply\');
					}
				}
			}
		},15000);

		setTimeout(function() {
			restitem = localStorage.getItem(location.href + \'quickreply\');
			if (restitem) {
				var restorebut = [
					\'<a class=\"sceditor-button\" title=\"{\$mybb->settings[\'quickadveditor_restor_lang\']}\" onclick=\"MyBBEditor.insert(restitem);\">\',
						\'<div style=\"background-image: url(images/rest.png); opacity: 1; cursor: pointer;\">{\$mybb->settings[\'quickadveditor_restor_lang\']}</div>\',
					\'</a>\'
				];

				\$(restorebut.join(\'\')).appendTo(\'.sceditor-group:last\');
			}
		},600);
		MyBBEditor.blur(function(e) {
			if (MyBBEditor.val()) {
				localStorage.setItem(location.href + \'quickreply\', MyBBEditor.val())
			}
			else {
				localStorage.removeItem(location.href + \'quickreply\');
			}
		});
	}
});
</script>",
		"sid"		 => "-1"
	);
	$db->insert_query("templates", $template2);

	find_replace_templatesets(
		'showthread_quickreply',
		'#' . preg_quote('</textarea>') . '#i',
		'</textarea>{$codebutquick}'
	);

	find_replace_templatesets(
		'private_quickreply',
		'#' . preg_quote('</textarea>') . '#i',
		'</textarea>{$codebutquick}'
	);

	find_replace_templatesets(
		'showthread_quickreply',
		'#' . preg_quote('<span class="smalltext">{$lang->message_note}<br />') . '#i',
		'<span class="smalltext">{$lang->message_note}<br />{$smilieinserter}'
	);

	find_replace_templatesets(
		'private_quickreply',
		'#' . preg_quote('<span class="smalltext">{$lang->message_note}<br />') . '#i',
		'<span class="smalltext">{$lang->message_note}<br />{$smilieinserter}'
	);

	find_replace_templatesets(
		'showthread',
		'#' . preg_quote('<body>') . '#i',
		'<body>
	{$codebutquickedt}'
	);

	find_replace_templatesets(
		'showthread',
		'#' . preg_quote('{$headerinclude}') . '#i',
		'{$headerinclude}
{$can_link}'
	);
}

function quickadveditor_deactivate()
{
	global $db;
	include_once MYBB_ROOT."inc/adminfunctions_templates.php";

	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='codebutquick'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='codebutquick_pm'");

	find_replace_templatesets(
		'showthread_quickreply',
		'#' . preg_quote('</textarea>{$codebutquick}') . '#i',
		'</textarea>'
	);

	find_replace_templatesets(
		'private_quickreply',
		'#' . preg_quote('</textarea>{$codebutquick}') . '#i',
		'</textarea>'
	);

	find_replace_templatesets(
		'showthread_quickreply',
		'#' . preg_quote('<span class="smalltext">{$lang->message_note}<br />{$smilieinserter}') . '#i',
		'<span class="smalltext">{$lang->message_note}<br />'
	);

	find_replace_templatesets(
		'private_quickreply',
		'#' . preg_quote('<span class="smalltext">{$lang->message_note}<br />{$smilieinserter}') . '#i',
		'<span class="smalltext">{$lang->message_note}<br />'
	);

	find_replace_templatesets(
		'showthread',
		'#' . preg_quote('<body>
	{$codebutquickedt}') . '#i',
		'<body>'
	);

	find_replace_templatesets(
		'showthread',
		'#' . preg_quote('{$headerinclude}
{$can_link}') . '#i',
		'{$headerinclude}'
	);
}

$plugins->add_hook('global_start', 'advedt_cache_codebutquick');
function advedt_cache_codebutquick()
{
	global $templatelist, $mybb;

	if (isset($templatelist)) {
		$templatelist .= ',';
	}

	if (THIS_SCRIPT == 'showthread.php') {
		if($mybb->settings['quickadveditor_smile'] != 0) {
			$templatelist .= 'codebutquick,smilieinsert,smilieinsert_smilie,smilieinsert_getmore';
		}
		else {
			$templatelist .= 'codebutquick';
		}
	}
	if (THIS_SCRIPT == 'private.php') {
		if($mybb->settings['quickadveditor_smile'] != 0) {
			$templatelist .= 'codebutquick_pm,smilieinsert,smilieinsert_smilie,smilieinsert_getmore';
		}
		else {
			$templatelist .= 'codebutquick_pm';
		}
	}
}

function mycode_inserter_quick_lite($smilies = true)
{
	global $db, $mybb, $theme, $templates, $lang, $smiliecache, $cache;

	$editor_lang_strings = array(
		"editor_bold" => "Bold",
		"editor_italic" => "Italic",
		"editor_underline" => "Underline",
		"editor_strikethrough" => "Strikethrough",
		"editor_subscript" => "Subscript",
		"editor_superscript" => "Superscript",
		"editor_alignleft" => "Align left",
		"editor_center" => "Center",
		"editor_alignright" => "Align right",
		"editor_justify" => "Justify",
		"editor_fontname" => "Font Name",
		"editor_fontsize" => "Font Size",
		"editor_fontcolor" => "Font Color",
		"editor_removeformatting" => "Remove Formatting",
		"editor_cut" => "Cut",
		"editor_cutnosupport" => "Your browser does not allow the cut command. Please use the keyboard shortcut Ctrl/Cmd-X",
		"editor_copy" => "Copy",
		"editor_copynosupport" => "Your browser does not allow the copy command. Please use the keyboard shortcut Ctrl/Cmd-C",
		"editor_paste" => "Paste",
		"editor_pastenosupport" => "Your browser does not allow the paste command. Please use the keyboard shortcut Ctrl/Cmd-V",
		"editor_pasteentertext" => "Paste your text inside the following box:",
		"editor_pastetext" => "PasteText",
		"editor_numlist" => "Numbered list",
		"editor_bullist" => "Bullet list",
		"editor_undo" => "Undo",
		"editor_redo" => "Redo",
		"editor_rows" => "Rows:",
		"editor_cols" => "Cols:",
		"editor_inserttable" => "Insert a table",
		"editor_inserthr" => "Insert a horizontal rule",
		"editor_code" => "Code",
		"editor_width" => "Width (optional):",
		"editor_height" => "Height (optional):",
		"editor_insertimg" => "Insert an image",
		"editor_email" => "E-mail:",
		"editor_insertemail" => "Insert an email",
		"editor_url" => "URL:",
		"editor_insertlink" => "Insert a link",
		"editor_unlink" => "Unlink",
		"editor_more" => "More",
		"editor_insertemoticon" => "Insert an emoticon",
		"editor_videourl" => "Video URL:",
		"editor_videotype" => "Video Type:",
		"editor_insert" => "Insert",
		"editor_insertyoutubevideo" => "Insert a YouTube video",
		"editor_currentdate" => "Insert current date",
		"editor_currenttime" => "Insert current time",
		"editor_print" => "Print",
		"editor_viewsource" => "View source",
		"editor_description" => "Description (optional):",
		"editor_enterimgurl" => "Enter the image URL:",
		"editor_enteremail" => "Enter the e-mail address:",
		"editor_enterdisplayedtext" => "Enter the displayed text:",
		"editor_enterurl" => "Enter URL:",
		"editor_enteryoutubeurl" => "Enter the YouTube video URL or ID:",
		"editor_insertquote" => "Insert a Quote",
		"editor_invalidyoutube" => "Invalid YouTube video",
		"editor_dailymotion" => "Dailymotion",
		"editor_metacafe" => "MetaCafe",
		"editor_veoh" => "Veoh",
		"editor_vimeo" => "Vimeo",
		"editor_youtube" => "Youtube",
		"editor_facebook" => "Facebook",
		"editor_liveleak" => "LiveLeak",
		"editor_insertvideo" => "Insert a video",
		"editor_php" => "PHP",
		"editor_maximize" => "Maximize"
	);
	$editor_language = "(function ($) {\n$.sceditor.locale[\"mybblang\"] = {\n";

	$editor_languages_count = count($editor_lang_strings);
	$i = 0;
	foreach($editor_lang_strings as $lang_string => $key)
	{
		$i++;
		$js_lang_string = str_replace("\"", "\\\"", $key);
		$string = str_replace("\"", "\\\"", $lang->$lang_string);
		$editor_language .= "\t\"{$js_lang_string}\": \"{$string}\"";

		if($i < $editor_languages_count)
		{
			$editor_language .= ",";
		}

		$editor_language .= "\n";
	}

	$editor_language .= "}})(jQuery);";

	if(defined("IN_ADMINCP"))
	{
		global $page;
		$codeinsertquick = $page->build_codebuttons_editor($editor_language, $smilies);
	}
	else
	{
		// Smilies
		$emoticon = "";
		$emoticons_enabled = "false";
		if($smilies && $mybb->settings['smilieinserter'] != 0 && $mybb->settings['smilieinsertercols'] && $mybb->settings['smilieinsertertot'])
		{
			$emoticon = ",emoticon";
			$emoticons_enabled = "true";

			if(!$smiliecache)
			{
				if(!is_array($smilie_cache))
				{
					$smilie_cache = $cache->read("smilies");
				}
				foreach($smilie_cache as $smilie)
				{
					if($smilie['showclickable'] != 0)
					{
						$smilie['image'] = str_replace("{theme}", $theme['imgdir'], $smilie['image']);
						$smiliecache[$smilie['sid']] = $smilie;
					}
				}
			}

			unset($smilie);

			if(is_array($smiliecache))
			{
				reset($smiliecache);

				$dropdownsmilies = $moresmilies = $hiddensmilies = "";
				$i = 0;

				foreach($smiliecache as $smilie)
				{
					$finds = explode("\n", $smilie['find']);
					$finds_count = count($finds);

					// Only show the first text to replace in the box
					$smilie['find'] = $finds[0];

					$find = htmlspecialchars_uni($smilie['find']);
					$image = htmlspecialchars_uni($smilie['image']);
					if($i < $mybb->settings['smilieinsertertot'])
					{
						$dropdownsmilies .= '"'.$find.'": "'.$image.'",';
					}
					else
					{
						$moresmilies .= '"'.$find.'": "'.$image.'",';
					}

					for($j = 1; $j < $finds_count; ++$j)
					{
						$find = htmlspecialchars_uni($finds[$j]);
						$hiddensmilies .= '"'.$find.'": "'.$image.'",';
					}
					++$i;
				}
			}
		}

		$basic1 = $basic2 = $align = $font = $size = $color = $removeformat = $email = $link = $list = $code = $sourcemode = "";

		if($mybb->settings['allowbasicmycode'] == 1)
		{
			$basic1 = "bold,italic,underline,strike|";
			$basic2 = "horizontalrule,";
		}

		if($mybb->settings['allowalignmycode'] == 1)
		{
			$align = "left,center,right,justify|";
		}

		if($mybb->settings['allowfontmycode'] == 1)
		{
			$font = "font,";
		}

		if($mybb->settings['allowsizemycode'] == 1)
		{
			$size = "size,";
		}

		if($mybb->settings['allowcolormycode'] == 1)
		{
			$color = "color,";
		}

		if($mybb->settings['allowfontmycode'] == 1 || $mybb->settings['allowsizemycode'] == 1 || $mybb->settings['allowcolormycode'] == 1)
		{
			$removeformat = "removeformat|";
		}

		if($mybb->settings['allowemailmycode'] == 1)
		{
			$email = "email,";
		}

		if($mybb->settings['allowlinkmycode'] == 1)
		{
			$link = "link,unlink";
		}

		if($mybb->settings['allowlistmycode'] == 1)
		{
			$list = "bulletlist,orderedlist|";
		}

		if($mybb->settings['allowcodemycode'] == 1)
		{
			$code = "code,php,";
		}

		if($mybb->user['sourceeditor'] == 1)
		{
			$sourcemode = "MyBBEditor.sourceMode(true);";
		}

		if (!strpos($_SERVER['PHP_SELF'],'private.php')) {
			eval("\$codeinsertquick = \"".$templates->get("codebutquick")."\";");
		}
		else {
			eval("\$codeinsertquick = \"".$templates->get("codebutquick_pm")."\";");
		}
	}

	return $codeinsertquick;
}

$plugins->add_hook("showthread_start", "codebuttonsquick_lite");
function codebuttonsquick_lite () {

	global $smilieinserter, $codebutquick, $codebutquickedt, $mybb;

	$codebutquick = mycode_inserter_quick_lite();
	$smilieinserter = $codebutquickedt = '';
	if($mybb->settings['quickadveditor_smile'] != 0) {
		$smilieinserter = build_clickable_smilies();
	}
	if($mybb->settings['quickreply'] == 0) {
		$codebutquickedt = mycode_inserter_quick_lite();
	}
}

$plugins->add_hook("private_start", "codebuttonsquick_lite_pm");
function codebuttonsquick_lite_pm () {

	global $smilieinserter, $codebutquick, $mybb;

	$codebutquick = mycode_inserter_quick_lite();
	$smilieinserter = '';
	if($mybb->settings['quickadveditor_smile'] != 0) {
		$smilieinserter = build_clickable_smilies();
	}
}

$plugins->add_hook('postbit', 'canonical_postbit');

function canonical_lite($link)
{
    global $settings, $plugins, $can_link;

    if($link)
    {
        $plugins->add_hook('showthread_start', 'google_seo_meta_output');
        $can_link = "<link rel=\"canonical\" href=\"{$settings['bburl']}/$link\" />";
    }
}

function canonical_postbit(&$post)
{
	global $templates, $lang, $mybb, $postcounter, $tid, $page;

	if($mybb->settings['quickadveditor_canonicallink'] != 0) {
		if (($postcounter - 1) % $mybb->settings['postsperpage'] == "0") {
			if($tid > 0)
			{
				if($page > 1)
				{
					canonical_lite(get_thread_link($tid, $page));
				}

				else
				{
					canonical_lite(get_thread_link($tid));
				}
			}
		}
	}
}

?>
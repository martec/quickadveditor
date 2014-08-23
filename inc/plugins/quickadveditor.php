<?php
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Plugin info
function quickadveditor_info ()
{
	return array(
		"name"			  => "Quick Advanced Editor",
		"description"	 => "Advanced editor in quick reply",
		"website"		 => "",
		"author"		=> "martec",
		"authorsite"	=> "",
		"version"		 => "3.2.3",
		"guid"			   => "",
		"compatibility" => "17*,18*"
	);
}

function quickadveditor_install()
{
	global $db, $lang;

	$lang->load('config_quickadveditor');

	$groupid = $db->insert_query('settinggroups', array(
		'name'		=> 'quickadveditor',
		'title'		=> 'Quick Advanced Editor',
		'description'	=> 'Settings related to the Quick Advanced Editor.',
		'disporder'	=> $dorder,
		'isdefault'	=> '0'
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_smile',
		'title'		=> $lang->quickadveditor_smile_title,
		'description'	=> $lang->quickadveditor_smile_desc,
		'optionscode'	=> 'onoff',
		'value'		=> '1',
		'disporder'	=> '1',
		'gid'		=> $groupid
	));

	$db->insert_query('settings', array(
		'name'		=> 'quickadveditor_qedit',
		'title'		=> $lang->quickadveditor_qedit_title,
		'description'	=> $lang->quickadveditor_qedit_desc,
		'optionscode'	=> 'onoff',
		'value'		=> '1',
		'disporder'	=> '2',
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
if({\$(\'#clickable_smilies\')) {
	\$(\'#clickable_smilies\').closest(\'div\').hide();
}
var partialmode = {\$mybb->settings[\'partialmode\']},
opt_editor = {
	plugins: \"bbcode\",
	height: 270,
	style: \"{\$mybb->asset_url}/jscripts/sceditor/jquery.sceditor.mybb.css\",
	rtl: {\$lang->settings[\'rtl\']},
	locale: \"mybblang\",
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
	(\$.fn.on || \$.fn.live).call(\$(document), \'focus\', \'textarea[name*=\"value\"]\', function () {
		\$(this).sceditor(opt_editor);
		setTimeout(function() {
			if (\$(\'textarea[name*=\"value\"]\').sceditor(\'instance\')) {
				\$(\'textarea[name*=\"value\"]\').sceditor(\'instance\').focus();
			}
			offset = \$(\'textarea[name*=\"value\"]\').next().offset().top - 60;
			setTimeout(function() {
				\$(\'html, body\').animate({
					scrollTop: offset
				}, 700);
			},200);
		},100);
		{\$sourcemode}
	});
}

(\$.fn.on || \$.fn.live).call(\$(document), \'focus\', \'#message\', function () {
	if (typeof sceditor == \'undefined\') {
		\$(this).sceditor(opt_editor);
		if({\$(\'#clickable_smilies\')) {
			\$(\'#clickable_smilies\').closest(\'div\').show();
		}
		MyBBEditor = \$(this).sceditor(\'instance\');
			setTimeout(function() {
				if (MyBBEditor) {
					MyBBEditor.focus();
				}
				offset = \$(\'#message\').next().offset().top - 60;
				setTimeout(function() {
					\$(\'html, body\').animate({
						scrollTop: offset
					}, 700);
				},200);
			},100);
		{\$sourcemode}
	}
});

(\$.fn.on || \$.fn.live).call(\$(document), \'click\', \'a[id*=\"multiquote_link_\"]\', function () {
	if (typeof sceditor == \'undefined\') {
		\$(\'#message\').sceditor(opt_editor);
		if({\$(\'#clickable_smilies\')) {
			\$(\'#clickable_smilies\').closest(\'div\').show();
		}
		MyBBEditor = \$(\'#message\').sceditor(\'instance\');
		{\$sourcemode}
	}
});

if(Cookie.get(\'multiquote\')) {
	\$(\'#message\').sceditor(opt_editor);
	if({\$(\'#clickable_smilies\')) {
		\$(\'#clickable_smilies\').closest(\'div\').show();
	}
	MyBBEditor = \$(\'#message\').sceditor(\'instance\');
	{\$sourcemode}
};

/**********************************
 * Thread compatibility functions *
 **********************************/
if(typeof Thread !== \'undefined\')
{
	var quickReplyFunc = Thread.quickReply;
	Thread.quickReply = function(e) {

		if(MyBBEditor) {
			MyBBEditor.updateOriginal();
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

	find_replace_templatesets(
		'showthread_quickreply',
		'#' . preg_quote('</textarea>') . '#i',
		'</textarea>{$codebutquick}'
	);

	find_replace_templatesets(
		'showthread_quickreply',
		'#' . preg_quote('<span class="smalltext">{$lang->message_note}<br />') . '#i',
		'<span class="smalltext">{$lang->message_note}<br />{$smilieinserter}'
	);

	find_replace_templatesets(
		'showthread',
		'#' . preg_quote('<body>') . '#i',
		'<body>
	{$codebutquickedt}'
	);	
}

function quickadveditor_deactivate()
{
	global $db;
	include_once MYBB_ROOT."inc/adminfunctions_templates.php";

	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='codebutquick'");

	find_replace_templatesets(
		'showthread_quickreply',
		'#' . preg_quote('</textarea>{$codebutquick}') . '#i',
		'</textarea>'
	);

	find_replace_templatesets(
		'showthread_quickreply',
		'#' . preg_quote('<span class="smalltext">{$lang->message_note}<br />{$smilieinserter}') . '#i',
		'<span class="smalltext">{$lang->message_note}<br />'
	);

	find_replace_templatesets(
		'showthread',
		'#' . preg_quote('<body>
	{$codebutquickedt}') . '#i',
		'<body>'
	);	
}

function mycode_inserter_quick($smilies = true)
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

		eval("\$codeinsertquick = \"".$templates->get("codebutquick")."\";");
	}

	return $codeinsertquick;
}

$plugins->add_hook("showthread_start", "codebuttonsquick");

function codebuttonsquick () {

	global $smilieinserter, $codebutquick, $codebutquickedt, $mybb;

	$codebutquick = mycode_inserter_quick();
	$smilieinserter = $codebutquickedt = '';
	if($mybb->settings['quickadveditor_smile'] != 0) {
		$smilieinserter = build_clickable_smilies();
	}
	if($mybb->settings['quickreply'] == 0) {
		$codebutquickedt = mycode_inserter_quick();
	}	
}

?>
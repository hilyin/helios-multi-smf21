<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines https://www.simplemachines.org
 * @copyright 2022 Simple Machines and individual contributors
 * @license https://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.1.3
 *
 * Helios Multi theme design and graphics:
 * @author Blocweb (Bloc)
 * @copyright 2011 Blocweb. All Rights Reserved.
 * @license BSD - see licence.txt in this theme's directory (kept from the original theme)
 * Ported to SMF 2.1 by hilyin, 2026: https://github.com/hilyin/helios-multi-smf21
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	https://www.simplemachines.org/
*/

/**
 * Initialize the template... mainly little settings.
 */
function template_init()
{
	global $settings, $txt;

	/* $context, $options and $txt may be available for use, but may not be fully populated yet. */

	// The version this template/theme is for. This should probably be the version of SMF it was created for.
	$settings['theme_version'] = '2.1';

	// Set the following variable to true if this theme requires the optional theme strings file to be loaded.
	$settings['require_theme_strings'] = false;

	// Set the following variable to true if this theme wants to display the avatar of the user that posted the last and the first post on the message index and recent pages.
	$settings['avatars_on_indexes'] = false;

	// Set the following variable to true if this theme wants to display the avatar of the user that posted the last post on the board index.
	$settings['avatars_on_boardIndex'] = false;

	// Set the following variable to true if this theme wants to display the login and register buttons in the main forum menu.
	$settings['login_main_menu'] = true;

	// 2.0 string used by the header collapse arrow; 2.1 no longer ships it.
	if (!isset($txt['upshrink_description']))
		$txt['upshrink_description'] = 'Shrink or expand the header.';

	// This defines the formatting for the page indexes used throughout the forum.
	$settings['page_index'] = array(
		'extra_before' => '<span class="pages">' . $txt['pages'] . '</span>',
		'previous_page' => '<span class="main_icons previous_page"></span>',
		'current_page' => '<span class="current_page">%1$d</span> ',
		'page' => '<a class="nav_page" href="{URL}">%2$s</a> ',
		'expand_pages' => '<span class="expand_pages" onclick="expandPages(this, {LINK}, {FIRST_PAGE}, {LAST_PAGE}, {PER_PAGE});"> ... </span>',
		'next_page' => '<span class="main_icons next_page"></span>',
		'extra_after' => '',
	);

	// Allow css/js files to be disabled for this specific theme.
	// Add the identifier as an array key. IE array('smf_script'); Some external files might not add identifiers, on those cases SMF uses its filename as reference.
	if (!isset($settings['disable_files']))
		$settings['disable_files'] = array();

	// Helios Multi: the six pod colours are theme variants (css/index_<colour>.css).
	// css/ holds this theme's own copies of the 2.1 stylesheets, recoloured by bin/helios-buildcss.py.
	// helios.css (the Helios layout) loads after them, then the colour variant (re-queued in template_html_above).
	$settings['theme_variants'] = array('blue', 'brown', 'golden', 'green', 'red', 'silver');
	$settings['default_variant'] = 'blue';
	loadCSSFile('helios.css', array('order_pos' => 9600), 'smf_helios');

	// Remember a member's colour pick; guests keep it for the session (Load.php handles ?variant=).
	global $user_info, $smcFunc;
	if (!empty($_REQUEST['variant']) && !empty($user_info['id']) && in_array($_REQUEST['variant'], $settings['theme_variants']))
	{
		$smcFunc['db_insert']('replace',
			'{db_prefix}themes',
			array('id_member' => 'int', 'id_theme' => 'int', 'variable' => 'string-255', 'value' => 'string-65534'),
			array($user_info['id'], $settings['theme_id'], 'theme_variant', $_REQUEST['variant']),
			array('id_member', 'id_theme', 'variable')
		);
		cache_put_data('theme_settings-' . $settings['theme_id'] . ':' . $user_info['id'], null, 60);
	}
}

/**
 * The main sub template above the content.
 */
function template_html_above()
{
	global $context, $scripturl, $txt, $modSettings, $boardurl, $boarddir;

	// SMF queues the colour variant at order 300; move it after helios.css so its pod images win.
	if (!empty($context['theme_variant']))
		loadCSSFile('index' . $context['theme_variant'] . '.css', array('order_pos' => 9700), 'smf_index' . $context['theme_variant']);

	// Show right to left, the language code, and the character set for ease of translating.
	echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', !empty($txt['lang_locale']) ? ' lang="' . str_replace("_", "-", substr($txt['lang_locale'], 0, strcspn($txt['lang_locale'], "."))) . '"' : '', '>
<head>
	<meta charset="', $context['character_set'], '">';

	// Helios: link the forum's own icons, if it has them in the forum root.
	if (file_exists($boarddir . '/favicon.png'))
		echo '
	<link rel="icon" href="', $boardurl, '/favicon.png" type="image/png">';
	if (file_exists($boarddir . '/apple-touch-icon.png'))
		echo '
	<link rel="apple-touch-icon" href="', $boardurl, '/apple-touch-icon.png">';

	/*
		You don't need to manually load index.css, this will be set up for you.
		Note that RTL will also be loaded for you.
		To load other CSS and JS files you should use the functions
		loadCSSFile() and loadJavaScriptFile() respectively.
		This approach will let you take advantage of SMF's automatic CSS
		minimization and other benefits. You can, of course, manually add any
		other files you want after template_css() has been run.

	*	Short example:
			- CSS: loadCSSFile('filename.css', array('minimize' => true));
			- JS:  loadJavaScriptFile('filename.js', array('minimize' => true));
			You can also read more detailed usages of the parameters for these
			functions on the SMF wiki.

	*	Themes:
			The most efficient way of writing multi themes is to use a master
			index.css plus variant.css files. If you've set them up properly
			(through $settings['theme_variants']), the variant files will be loaded
			for you automatically.
			Additionally, tweaking the CSS for the editor requires you to include
			a custom 'jquery.sceditor.theme.css' file in the css folder if you need it.

	*	MODs:
			If you want to load CSS or JS files in here, the best way is to use the
			'integrate_load_theme' hook for adding multiple files, or using
			'integrate_pre_css_output', 'integrate_pre_javascript_output' for a single file.
	*/

	// load in any css from mods or themes so they can overwrite if wanted
	template_css();

	// load in any javascript files from mods and themes
	template_javascript();

	echo '
	<title>', $context['page_title_html_safe'], '</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">';

	// Content related meta tags, like description, keywords, Open Graph stuff, etc...
	foreach ($context['meta_tags'] as $meta_tag)
	{
		echo '
	<meta';

		foreach ($meta_tag as $meta_key => $meta_value)
			echo ' ', $meta_key, '="', $meta_value, '"';

		echo '>';
	}

	/*	What is your Lollipop's color?
		Theme Authors, you can change the color here to make sure your theme's main color gets visible on tab */
	echo '
	<meta name="theme-color" content="#557EA0">';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex">';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '">';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help">
	<link rel="contents" href="', $scripturl, '">', ($context['allow_search'] ? '
	<link rel="search" href="' . $scripturl . '?action=search">' : '');

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?action=.xml;type=rss2', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">
	<link rel="alternate" type="application/atom+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['atom'], '" href="', $scripturl, '?action=.xml;type=atom', !empty($context['current_board']) ? ';board=' . $context['current_board'] : '', '">';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['links']['next']))
		echo '
	<link rel="next" href="', $context['links']['next'], '">';

	if (!empty($context['links']['prev']))
		echo '
	<link rel="prev" href="', $context['links']['prev'], '">';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0">';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body id="', $context['browser_body_id'], '" class="action_', !empty($context['current_action']) ? $context['current_action'] : (!empty($context['current_board']) ?
		'messageindex' : (!empty($context['current_topic']) ? 'display' : 'home')), !empty($context['current_board']) ? ' board_' . $context['current_board'] : '', '">
<div id="footerfix">';
}

/**
 * Helios Multi header: colour picker, user pod, news pod, stats pod, menu bar and the link tree strip,
 * laid out like the SMF 2.0 Helios Multi theme but built from 2.1 markup so the popups and JS keep working.
 */
function template_body_above()
{
	global $context, $settings, $scripturl, $txt, $modSettings, $maintenance, $smcFunc, $options;

	echo '
	<div id="helios_page">';

	// Colour picker; keeps the current page.
	$helios_url = $scripturl . '?' . (empty($_SERVER['QUERY_STRING']) ? '' : rtrim(preg_replace('~(^|;|&)variant=[^;&]*~', '', $_SERVER['QUERY_STRING']), ';&') . ';');
	echo '
		<ul id="helios_colours">';

	foreach (array('brown', 'red', 'golden', 'green', 'blue', 'silver') as $colour)
		echo '
			<li><a href="', $smcFunc['htmlspecialchars']($helios_url . 'variant=' . $colour), '" class="helios_', $colour, $context['theme_variant'] == '_' . $colour ? ' active' : '', '" title="', $colour, '"></a></li>';

	echo '
		</ul>
		<h1 class="forumtitle"><a id="top" href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? $context['forum_name_html_safe'] : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name_html_safe'] . '">', '</a></h1>';

	// User pod: avatar and greeting, as in the 2.0 theme.
	echo '
		<div id="top_section" class="helios_pod">
			<div class="helios_user">';

	if ($context['user']['is_logged'])
	{
		if (!empty($context['user']['avatar']['image']))
			echo '
				<a href="', $scripturl, '?action=profile" class="helios_avatar">', $context['user']['avatar']['image'], '</a>';

		echo '
				<div class="helios_greeting">
					', $txt['hello_member'], ' <strong>', $context['user']['name'], '</strong>';

		if ($context['allow_pm'])
			echo ', you have <a href="', $scripturl, '?action=pm">', comma_format($context['user']['messages']), ' ', $context['user']['messages'] == 1 ? 'message' : 'messages', '</a>, ', comma_format($context['user']['unread_messages']), ' ', $context['user']['unread_messages'] == 1 ? 'is' : 'are', ' new';

		echo '.';

		// 2.1 alerts have no dropdown in this theme, so mention them here.
		if (!empty($context['user']['alerts']))
			echo '<br>
					You have <a href="', $scripturl, '?action=profile;area=showalerts;u=', $context['user']['id'], '">', comma_format($context['user']['alerts']), ' new ', $context['user']['alerts'] == 1 ? 'alert' : 'alerts', '</a>.';

		if (!empty($context['unapproved_members']))
			echo '<br>
					There ', $context['unapproved_members'] == 1 ? 'is <a href="' . $scripturl . '?action=admin;area=viewmembers;sa=browse;type=approve">one member</a>' : 'are <a href="' . $scripturl . '?action=admin;area=viewmembers;sa=browse;type=approve">' . $context['unapproved_members'] . ' members</a>', ' awaiting approval.';

		if (!empty($context['in_maintenance']) && $context['user']['is_admin'])
			echo '<br>
					<strong>', $txt['maintain_mode_on'], '</strong>';

		echo '<br><br>
					<a href="', $scripturl, '?action=unread">', $txt['unread_since_visit'], '</a><br>
					<a href="', $scripturl, '?action=unreadreplies">', $txt['show_unread_replies'], '</a>
				</div>';
	}
	elseif (empty($maintenance))
		echo '
				<div class="helios_greeting">
					', $txt['hello_guest'], ' <strong>', $txt['guest_title'], '</strong>. Please <a href="', $scripturl, '?action=login" onclick="return reqOverlayDiv(this.href, ', JavaScriptEscape($txt['login']), ', \'login\');">login</a>', $context['can_register'] ? ' or <a href="' . $scripturl . '?action=signup">register</a>' : '', '.
					<form class="helios_quicklogin" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '">
						<input type="text" name="user" size="12" placeholder="', $txt['username'], '" aria-label="', $txt['username'], '" autocomplete="username" required>
						<input type="password" name="passwrd" size="12" placeholder="', $txt['password'], '" aria-label="', $txt['password'], '" autocomplete="current-password" required>
						<input type="submit" value="', $txt['login'], '" class="button">
						<label><input type="checkbox" name="cookieneverexp" checked> Stay logged in</label>
						<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '">
						<input type="hidden" name="', $context['login_token_var'], '" value="', $context['login_token'], '">
					</form>
				</div>';
	else
		echo '
				<div class="helios_greeting">', sprintf($txt['welcome_guest'], $context['forum_name_html_safe'], $scripturl . '?action=login', 'return true;'), '</div>';

	echo '
			</div>
		</div><!-- #top_section -->';

	// News pod.
	if (!empty($settings['enable_news']) && !empty($context['random_news_line']))
		echo '
		<div id="helios_news" class="helios_pod">', $context['random_news_line'], '</div>';

	// Stats pod: forum totals on the left, time on the right.
	echo '
		<div id="helios_stats">
			<span class="helios_stat_text">';

	if (!empty($modSettings['totalMessages']))
		echo '<strong>', comma_format($modSettings['totalMessages']), '</strong> ', $txt['posts'], ' ', $txt['in'], ' <strong>', comma_format($modSettings['totalTopics']), '</strong> ', $txt['topics'], ' ', $txt['by'], ' <strong>', comma_format($modSettings['totalMembers']), '</strong> ', $txt['members'], ' - ', $txt['latest_member'], ': <a href="', $scripturl, '?action=profile;u=', $modSettings['latestMember'], '">', $modSettings['latestRealName'], '</a>';

	echo '</span>
			<time datetime="', smf_gmstrftime('%FT%TZ'), '">', $context['current_time'], '</time>
		</div>';

	// Menu bar between its two caps, plus the 2.1 mobile menu.
	echo '
		<div id="helios_menubar">
			<span class="helios_cap helios_cap_left"></span>
			<img id="upshrink" src="', $settings['images_url'], '/', empty($options['collapse_header']) ? 'upshrink.gif' : 'upshrink2.gif', '" alt="*" title="', $txt['upshrink_description'], '">
			<a class="mobile_user_menu">
				<span class="menu_icon"></span>
				<span class="text_menu">', $txt['mobile_user_menu'], '</span>
			</a>
			<div id="main_menu">
				<div id="mobile_user_menu" class="popup_container">
					<div class="popup_window description">
						<div class="popup_heading">', $txt['mobile_user_menu'], '
							<a href="javascript:void(0);" class="main_icons hide_popup"></a>
						</div>
						', template_menu(), '
					</div>
				</div>
			</div>
			<span class="helios_cap helios_cap_right"></span>
		</div>';

	// Link tree in the board strip.
	theme_linktree();

	// The main content goes inside a pod (the board index draws its own pods).
	echo '
		<div id="content_section"', $context['current_action'] == '' && empty($context['current_board']) && empty($context['current_topic']) ? '' : ' class="helios_pod"', '>
			<div id="main_content_section">';
}

/**
 * Helios Multi footer: the max-bottom strip with copyright and theme credit.
 */
function template_body_below()
{
	global $context, $txt, $scripturl, $modSettings, $settings, $options;

	echo '
			</div><!-- #main_content_section -->
		</div><!-- #content_section -->
		<div id="footer">
			<div class="helios_footer_text">
				<ul>
					<li class="floatright"><a href="', $scripturl, '?action=help">', $txt['help'], '</a> ', (!empty($modSettings['requireAgreement'])) ? '| <a href="' . $scripturl . '?action=agreement">' . $txt['terms_and_rules'] . '</a>' : '', ' | <a href="#top">', $txt['go_up'], ' &#9650;</a></li>
					<li class="copyright">', theme_copyright(), '</li>
				</ul>
				<p class="helios_credit"><a href="', $settings['theme_url'], '/licence.txt">Helios Multi</a> &copy; Bloc, ported to SMF 2.1</p>';

	if ($context['show_load_time'])
		echo '
				<p>', sprintf($txt['page_created_full'], $context['load_time'], $context['load_queries']), '</p>';

	echo '
			</div>
		</div><!-- #footer -->
		<div id="helios_badges">
			<a href="https://www.mysql.com/" target="_blank" rel="noopener"><img src="', $settings['images_url'], '/powered-mysql.gif" alt="Powered by MySQL"></a>
			<a href="https://www.php.net/" target="_blank" rel="noopener"><img src="', $settings['images_url'], '/powered-php.gif" alt="Powered by PHP"></a>
			<a href="https://validator.w3.org/" target="_blank" rel="noopener"><img src="', $settings['images_url'], '/valid-xhtml10.gif" alt="W3C XHTML"></a>
			<a href="https://jigsaw.w3.org/css-validator/" target="_blank" rel="noopener"><img src="', $settings['images_url'], '/valid-css.gif" alt="W3C CSS"></a>
		</div>
	</div><!-- #helios_page -->
	<script>
		var oMainHeaderToggle = new smc_Toggle({
			bToggleEnabled: true,
			bCurrentlyCollapsed: ', empty($options['collapse_header']) ? 'false' : 'true', ',
			aSwappableContainers: [\'top_section\', \'helios_news\'],
			aSwapImages: [{
				sId: \'upshrink\',
				srcExpanded: smf_images_url + \'/upshrink.gif\',
				altExpanded: ', JavaScriptEscape($txt['upshrink_description']), ',
				srcCollapsed: smf_images_url + \'/upshrink2.gif\',
				altCollapsed: ', JavaScriptEscape($txt['upshrink_description']), '
			}],
			oThemeOptions: {
				bUseThemeSettings: ', $context['user']['is_guest'] ? 'false' : 'true', ',
				sOptionName: \'collapse_header\',
				sSessionVar: smf_session_var,
				sSessionId: smf_session_id
			},
			oCookieOptions: {
				bUseCookie: ', $context['user']['is_guest'] ? 'true' : 'false', ',
				sCookieName: \'upshrink\'
			}
		});
	</script>
</div><!-- #footerfix -->';
}

/**
 * This shows any deferred JavaScript and closes out the HTML
 */
function template_html_below()
{
	// Load in any javascipt that could be deferred to the end of the page
	template_javascript(true);

	echo '
</body>
</html>';
}

/**
 * Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
 *
 * @param bool $force_show Whether to force showing it even if settings say otherwise
 */
function theme_linktree($force_show = false)
{
	global $context, $shown_linktree, $scripturl, $txt;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
				<div class="navigate_section">
					<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
						<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Don't show a separator for the first one.
		// Better here. Always points to the next level when the linktree breaks to a second line.
		// Picked a better looking HTML entity, and added support for RTL plus a span for styling.
		if ($link_num != 0)
			echo '
							<span class="dividers"> | </span>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'], ' ';

		// Show the link, including a URL if it should have one.
		if (isset($tree['url']))
			echo '
							<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>';
		else
			echo '
							<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo ' ', $tree['extra_after'];

		echo '
						</li>';
	}

	echo '
					</ul>
				</div><!-- .navigate_section -->';

	$shown_linktree = true;
}

/**
 * Show the menu up top. Something like [home] [help] [profile] [logout]...
 */
function template_menu()
{
	global $context;

	echo '
					<ul class="dropmenu menu_nav">';

	// Note: Menu markup has been cleaned up to remove unnecessary spans and classes.
	foreach (helios_menu_buttons() as $act => $button)
	{
		echo '
						<li class="button_', $act, '', !empty($button['sub_buttons']) ? ' subsections"' : '"', '>
							<a', $button['active_button'] ? ' class="active"' : '', ' href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', isset($button['onclick']) ? ' onclick="' . $button['onclick'] . '"' : '', '>
								', $button['icon'], '<span class="textmenu">', $button['title'], !empty($button['amt']) ? ' <span class="amt">' . $button['amt'] . '</span>' : '', '</span>
							</a>';

		// 2nd level menus
		if (!empty($button['sub_buttons']))
		{
			echo '
							<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
								<li', !empty($childbutton['sub_buttons']) ? ' class="subsections"' : '', '>
									<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', isset($childbutton['onclick']) ? ' onclick="' . $childbutton['onclick'] . '"' : '', '>
										', $childbutton['title'], !empty($childbutton['amt']) ? ' <span class="amt">' . $childbutton['amt'] . '</span>' : '', '
									</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
									<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
										<li>
											<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', isset($grandchildbutton['onclick']) ? ' onclick="' . $grandchildbutton['onclick'] . '"' : '', '>
												', $grandchildbutton['title'], !empty($grandchildbutton['amt']) ? ' <span class="amt">' . $grandchildbutton['amt'] . '</span>' : '', '
											</a>
										</li>';

					echo '
									</ul>';
				}

				echo '
								</li>';
			}
			echo '
							</ul>';
		}
		echo '
						</li>';
	}

	echo '
					</ul><!-- .menu_nav -->';
}

/**
 * Helios: the 2.0 menu also had Help, Profile and My Messages, which 2.1 moved elsewhere, plus an
 * optional custom link (theme settings: helios_extra_link_title / helios_extra_link_url, shown to
 * logged-in members). Returns $context['menu_buttons'] with them put back in the old order:
 * Home | Help | Search | Admin | Moderate | Profile | My Messages | Calendar | <custom link> | Members | Logout
 */
function helios_menu_buttons()
{
	global $context, $scripturl, $txt, $settings;

	$extra = array(
		'help' => array('after' => array('home'), 'title' => $txt['help'], 'href' => $scripturl . '?action=help', 'action' => 'help', 'show' => true),
		'profile' => array('after' => array('moderate', 'admin', 'search', 'help'), 'title' => $txt['profile'], 'href' => $scripturl . '?action=profile', 'action' => 'profile', 'show' => $context['user']['is_logged']),
		'pm' => array('after' => array('profile'), 'title' => 'My Messages', 'href' => $scripturl . '?action=pm', 'action' => 'pm', 'show' => $context['user']['is_logged'] && $context['allow_pm'], 'amt' => $context['user']['unread_messages'] ?? 0),
		'helios_link' => array('after' => array('calendar', 'pm', 'profile', 'search'), 'title' => $settings['helios_extra_link_title'] ?? '', 'href' => $settings['helios_extra_link_url'] ?? '', 'action' => '', 'show' => $context['user']['is_logged'] && !empty($settings['helios_extra_link_title']) && !empty($settings['helios_extra_link_url'])),
	);

	$buttons = $context['menu_buttons'];
	$any_active = false;
	foreach ($extra as $key => $item)
	{
		if (!$item['show'] || isset($buttons[$key]))
			continue;

		$active = $item['action'] !== '' && $context['current_action'] === $item['action'];
		$any_active = $any_active || $active;
		$new = array($key => array(
			'title' => $item['title'],
			'href' => $item['href'],
			'icon' => '',
			'active_button' => $active,
			'sub_buttons' => array(),
			'amt' => $item['amt'] ?? 0,
		));

		// Insert after the first anchor that exists, else at the end.
		$keys = array_keys($buttons);
		$pos = count($keys);
		foreach ($item['after'] as $anchor)
			if (($i = array_search($anchor, $keys, true)) !== false)
			{
				$pos = $i + 1;
				break;
			}
		$buttons = array_slice($buttons, 0, $pos, true) + $new + array_slice($buttons, $pos, null, true);
	}

	// 2.1 highlights Home for actions it has no button for; move that to the button we added.
	if ($any_active)
		foreach ($buttons as $key => $button)
			if (!isset($extra[$key]))
				$buttons[$key]['active_button'] = false;

	// Calendar is a plain link like in 2.0 (no hover sub-menu); Post Event is on the calendar page.
	if (isset($buttons['calendar']))
		$buttons['calendar']['sub_buttons'] = array();

	return $buttons;
}

/**
 * Generate a strip of buttons.
 *
 * @param array $button_strip An array with info for displaying the strip
 * @param string $direction The direction
 * @param array $strip_options Options for the button strip
 */
function template_button_strip($button_strip, $direction = '', $strip_options = array())
{
	global $context, $txt;

	if (!is_array($strip_options))
		$strip_options = array();

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		// As of 2.1, the 'test' for each button happens while the array is being generated. The extra 'test' check here is deprecated but kept for backward compatibility (update your mods, folks!)
		if (!isset($value['test']) || !empty($context[$value['test']]))
		{
			if (!isset($value['id']))
				$value['id'] = $key;

			$button = '
				<a class="button button_strip_' . $key . (!empty($value['active']) ? ' active' : '') . (isset($value['class']) ? ' ' . $value['class'] : '') . '" ' . (!empty($value['url']) ? 'href="' . $value['url'] . '"' : '') . ' ' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '>'.(!empty($value['icon']) ? '<span class="main_icons '.$value['icon'].'"></span>' : '').'' . $txt[$value['text']] . '</a>';

			if (!empty($value['sub_buttons']))
			{
				$button .= '
					<div class="top_menu dropmenu ' . $key . '_dropdown">
						<div class="viewport">
							<div class="overview">';
				foreach ($value['sub_buttons'] as $element)
				{
					if (isset($element['test']) && empty($context[$element['test']]))
						continue;

					$button .= '
								<a href="' . $element['url'] . '"><strong>' . $txt[$element['text']] . '</strong>';
					if (isset($txt[$element['text'] . '_desc']))
						$button .= '<br><span>' . $txt[$element['text'] . '_desc'] . '</span>';
					$button .= '</a>';
				}
				$button .= '
							</div><!-- .overview -->
						</div><!-- .viewport -->
					</div><!-- .top_menu -->';
			}

			$buttons[] = $button;
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"' : ''), '>
			', implode('', $buttons), '
		</div>';
}

/**
 * Generate a list of quickbuttons.
 *
 * @param array $list_items An array with info for displaying the strip
 * @param string $list_class Used for integration hooks and as a class name
 * @param string $output_method The output method. If 'echo', simply displays the buttons, otherwise returns the HTML for them
 * @return void|string Returns nothing unless output_method is something other than 'echo'
 */
function template_quickbuttons($list_items, $list_class = null, $output_method = 'echo')
{
	global $txt;

	// Enable manipulation with hooks
	if (!empty($list_class))
		call_integration_hook('integrate_' . $list_class . '_quickbuttons', array(&$list_items));

	// Make sure the list has at least one shown item
	foreach ($list_items as $key => $li)
	{
		// Is there a sublist, and does it have any shown items
		if ($key == 'more')
		{
			foreach ($li as $subkey => $subli)
				if (isset($subli['show']) && !$subli['show'])
					unset($list_items[$key][$subkey]);

			if (empty($list_items[$key]))
				unset($list_items[$key]);
		}
		// A normal list item
		elseif (isset($li['show']) && !$li['show'])
			unset($list_items[$key]);
	}

	// Now check if there are any items left
	if (empty($list_items))
		return;

	// Print the quickbuttons
	$output = '
		<ul class="quickbuttons' . (!empty($list_class) ? ' quickbuttons_' . $list_class : '') . '">';

	// This is used for a list item or a sublist item
	$list_item_format = function($li)
	{
		$html = '
			<li' . (!empty($li['class']) ? ' class="' . $li['class'] . '"' : '') . (!empty($li['id']) ? ' id="' . $li['id'] . '"' : '') . (!empty($li['custom']) ? ' ' . $li['custom'] : '') . '>';

		if (isset($li['content']))
			$html .= $li['content'];
		else
			$html .= '
				<a href="' . (!empty($li['href']) ? $li['href'] : 'javascript:void(0);') . '"' . (!empty($li['javascript']) ? ' ' . $li['javascript'] : '') . '>
					' . (!empty($li['icon']) ? '<span class="main_icons ' . $li['icon'] . '"></span>' : '') . (!empty($li['label']) ? $li['label'] : '') . '
				</a>';

		$html .= '
			</li>';

		return $html;
	};

	foreach ($list_items as $key => $li)
	{
		// Handle the sublist
		if ($key == 'more')
		{
			$output .= '
			<li class="post_options">
				<a href="javascript:void(0);">' . $txt['post_options'] . '</a>
				<ul>';

			foreach ($li as $subli)
				$output .= $list_item_format($subli);

			$output .= '
				</ul>
			</li>';
		}
		// Ordinary list item
		else
			$output .= $list_item_format($li);
	}

	$output .= '
		</ul><!-- .quickbuttons -->';

	// There are a few spots where the result needs to be returned
	if ($output_method == 'echo')
		echo $output;
	else
		return $output;
}

/**
 * The upper part of the maintenance warning box
 */
function template_maint_warning_above()
{
	global $txt, $context, $scripturl;

	echo '
	<div class="errorbox" id="errors">
		<dl>
			<dt>
				<strong id="error_serious">', $txt['forum_in_maintenance'], '</strong>
			</dt>
			<dd class="error" id="error_list">
				', sprintf($txt['maintenance_page'], $scripturl . '?action=admin;area=serversettings;' . $context['session_var'] . '=' . $context['session_id']), '
			</dd>
		</dl>
	</div>';
}

/**
 * The lower part of the maintenance warning box.
 */
function template_maint_warning_below()
{

}

?>
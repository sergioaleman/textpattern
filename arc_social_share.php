global $prefs, $txpcfg;

function arc_social_share_delicious($atts, $thing=null)
{
	extract(lAtts(array(
		'class' => '',
		'title' => null,
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'Share on Delicious' : parse($thing);

	$utmSource = $utm ? 'delicious.com' : null;

	$url = _arc_social_share_url($url, $utmSource);
	$title = _arc_social_share_title($title);

	$link = "http://delicious.com/post?url=$url&amp;title=$title";

	$html = href($thing, $link, ' class="'.$class.'"');

	return $html;
}

function arc_social_share_facebook($atts, $thing=null)
{
	extract(lAtts(array(
		'class' => '',
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'Facebook' : parse($thing);

	$utmSource = $utm ? 'facebook.com' : null;

	$url = _arc_social_share_url($url, $utmSource);

	$html = href($thing, "https://www.facebook.com/sharer/sharer.php?u=$url"
		, ' class="'.$class.'"');


         return $html;

}

function arc_social_share_gplus($atts, $thing=null)
{
	extract(lAtts(array(
		'class' => '',
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'Google+' : parse($thing);

	$utmSource = $utm ? 'gplus' : null;

	$url = _arc_social_share_url($url, $utmSource);

	$html = href($thing, "https://plus.google.com/share?url=$url"
		, ' class="'.$class.'"');

	return $html;
}

function arc_social_share_linkedin($atts, $thing=null)
{
	global $prefs;

	extract(lAtts(array(
		'class' => '',
		'source' => null,
		'summary' => null,
		'title' => null,
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'LinkedIn' : parse($thing);

	$utmSource = $utm ? 'linkedin' : null;

	$url = _arc_social_share_url($url, $utmSource);
	$title = _arc_social_share_title($title);
	$source = $source===null && !empty($prefs['sitename']) ? urldecode($prefs['sitename']) : urlencode($source);

	$link = "http://www.linkedin.com/shareArticle?mini=true&amp;url=$url&amp;title=$title&amp;source=$source";

	if (!empty($summary)) {
		$link .= "&amp;summary=$summary";
	}

	$html = href($thing, $link, ' class="'.$class.'"');

	return $html;
}

function arc_social_share_pinterest($atts, $thing=null)
{
	extract(lAtts(array(
		'class' => '',
		'image' => null,
		'title' => null,
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'Share on Pinterest' : parse($thing);

	$utmSource = $utm ? 'pinterest' : null;

	$url = _arc_social_share_url($url, $utmSource);
	$title = _arc_social_share_title($title);
	$image = _arc_social_share_image($image);

	$link = "http://www.pinterest.com/pin/create/button/?url=$url&amp;description=$title";
	if ($image) {
		$link .= "&amp;media=$image";
	}

	$html = href($thing, $link, ' class="'.$class.'"');

	return $html;
}

function arc_social_share_pocket($atts, $thing=null)
{
	extract(lAtts(array(
		'class' => '',
		'title' => null,
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'Add to Pocket' : parse($thing);

	$utmSource = $utm ? 'getpocket.com' : null;

	$url = _arc_social_share_url($url, $utmSource);
	$title = _arc_social_share_title($title);

	$link = "https://getpocket.com/save?url=$url&amp;title=$title";

	$html = href($thing, $link, ' class="'.$class.'"');

	return $html;
}

function arc_social_share_reddit($atts, $thing=null)
{
	global $thisarticle;

	extract(lAtts(array(
		'class' => '',
		'title' => null,
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'Share on Reddit' : parse($thing);

	$utmSource = $utm ? 'reddit' : null;

	$url = _arc_social_share_url($url, $utmSource);
	$title = _arc_social_share_title($title);

	$link = "http://www.reddit.com/submit?url=$url&amp;title=$title";

	$html = href($thing, $link, ' class="'.$class.'"');

	return $html;
}

function arc_social_share_stumbleupon($atts, $thing=null)
{
	extract(lAtts(array(
		'class' => '',
		'title' => null,
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'Share on StumbleUpon' : parse($thing);

	$utmSource = $utm ? 'stumbleupon' : null;

	$url = _arc_social_share_url($url, $utmSource);
	$title = _arc_social_share_title($title);

	$link = "http://www.stumbleupon.com/submit?url=$url&amp;title=$title";

	$html = href($thing, $link, ' class="'.$class.'"');

	return $html;
}

function arc_social_share_tumblr($atts, $thing=null)
{
	extract(lAtts(array(
		'class' => '',
		'title' => null,
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'Share on Tumblr' : parse($thing);

	$utmSource = $utm ? 'tumblr' : null;

	$url = _arc_social_share_url($url, $utmSource);
	$title = _arc_social_share_title($title);

	$link = "http://www.tumblr.com/share?v=3&amp;u=$url&amp;t=$title";

	$html = href($thing, $link, ' class="'.$class.'"');

	return $html;

}

function arc_social_share_twitter($atts, $thing=null)
{
	global $thisarticle;

	extract(lAtts(array(
		'class' => '',
		'mention' => null,
		'title' => null,
		'url' => null,
		'utm' => false
	), $atts));

	$thing = ($thing===null) ? 'Twitter' : parse($thing);

	$utmSource = $utm ? 'twitter.com' : null;

	$url = _arc_social_share_url($url, $utmSource);
	$title = _arc_social_share_title($title);

	$link = "http://twitter.com/home?status=$title+$url";

	if (!empty($mention)) {
		$link .= urlencode(" /@$mention");
	}

	$html = href($thing, $link, ' class="'.$class.'"');

	return $html;
}

function _arc_social_share_title($title=null)
{
	global $thisarticle;

	$title = $title===null && !empty($thisarticle['title']) ? urlencode($thisarticle['title']) : urlencode($title);

	return $title;
}

function _arc_social_share_url($url, $source=null)
{
	global $thisarticle;

	$url = $url===null && !empty($thisarticle['thisid']) ? permlinkurl_id($thisarticle['thisid']) : $url;

	if (!empty($url) && !empty($source))
	{
		// Add Google Analytics urchin tracking module to the URL
		$query = "utm_source=$source&utm_medium=social&utm_campaign=arc_social_share";
		$query .= !empty($thisarticle['thisid']) ? '&utm_content=txp:' . $thisarticle['thisid'] : '';
		$separator = (parse_url($url, PHP_URL_QUERY) == NULL) ? '?' : '&';
		$url .= $separator . $query;

	}

	if (!empty($url))
	{
		// Encode the URL
		$url = urlencode($url);
	}

	return $url;
}

function _arc_social_share_image($image=null)
{
	global $thisarticle;

	if ($image===null && !empty($thisarticle['article_image'])) {

		$image = $thisarticle['article_image'];

		if (intval($image)) {

			if ($rs = safe_row('*', 'txp_image', 'id = ' . intval($image))) {
				$image = urlencode(imagesrcurl($rs['id'], $rs['ext']));
			} else {
				$image = null;
			}

		}

	}

	return $image;
}


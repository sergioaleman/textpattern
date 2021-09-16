global $prefs, $txpcfg;

register_callback('_arc_meta_install', 'plugin_lifecycle.arc_meta', 'installed');
register_callback('_arc_meta_uninstall', 'plugin_lifecycle.arc_meta', 'deleted');
register_callback('arc_meta_options', 'plugin_prefs.arc_meta');
add_privs('plugin_prefs.arc_meta', '1,2');

function arc_meta_title($atts)
{
    global $parentid, $thisarticle, $id, $q, $c, $context, $s, $sitename, $prefs;

    extract(lAtts(array(
        'separator' => ' | ',
        'title' => null,
        'article_title' => $prefs['arc_meta_article_title'],
        'comment_title' => $prefs['arc_meta_comment_title'],
        'search_title' => $prefs['arc_meta_search_title'],
        'category_title' => $prefs['arc_meta_category_title'],
        'section_title' => $prefs['arc_meta_section_title'],
        'section_category_title' => $prefs['arc_meta_section_category_title'],
        'homepage_title' => $prefs['arc_meta_homepage_title']
    ), $atts));

    if ($title === null) {

        $meta = _arc_meta();

        $tokens = array(
            '_%n_' => txpspecialchars($sitename),
            '_%t_' => txpspecialchars($prefs['site_slogan'])
        );

        if (!empty($parent_id) || !empty($thisarticle['title'])) {
            $tokens['_%a_'] = empty($meta['title']) ? escape_title($thisarticle['title']) : $meta['title'];
            $tokens['_%s_'] = txpspecialchars(fetch_section_title($thisarticle['section']));
            $pattern = !empty($parent_id) ? $comment_title : $article_title;
        } elseif ($q) {
            $tokens['_%q_'] = txpspecialchars($q);
            $pattern = $search_title;
        } elseif ($c) {
            $tokens['_%c_'] = empty($meta['title']) ? txpspecialchars(fetch_category_title($c, $context)) : $meta['title'];
            if ($s and $s != 'default') {
                $tokens['_%s_'] = txpspecialchars(fetch_section_title($s));
                $pattern = $section_category_title;
            } else {
                $pattern = $category_title;
            }
        } elseif ($s and $s != 'default') {
            $tokens['_%s_'] = empty($meta['title']) ? txpspecialchars(fetch_section_title($s)) : $meta['title'];
            $pattern = $section_title;
        } else {
            $pattern = !empty($meta['title']) ? $meta['title'] : $homepage_title;
        }

        $title = preg_replace(array_keys($tokens), array_values($tokens), $pattern);

    }

    $html = tag($title, 'title');

    return $html;
}

function arc_meta_canonical($atts)
{
    extract(lAtts(array(
        'url' => null
    ), $atts));

    $url = $url !==null ? $url : _arc_meta_url();

    $html = "<link rel=\"canonical\" href=\"$url\" />";

    return $html;
}

function arc_meta_description($atts)
{
    extract(lAtts(array(
        'description' => null
    ), $atts));

    if ($description===null) {
        $meta = _arc_meta();
        $description = !empty($meta['description']) ? txpspecialchars($meta['description'], ENT_QUOTES) : _arc_meta_description();
    }

    if ($description) {
        return "<meta name=\"description\" content=\"$description\" />";
    }

    return '';
}

function arc_meta_robots($atts)
{
    extract(lAtts(array(
        'robots' => null
    ), $atts));

    if ($robots === null) {
        $meta = _arc_meta();
        $robots = !empty($meta['robots']) ? $meta['robots'] : null;
    }

    $out = '';

    if (get_pref('production_status') != 'live') {
        $out .= "<meta name=\"robots\" content=\"noindex, nofollow\" />";
        $out .= $robots ? "<!-- $robots -->" : null;
    } elseif ($robots) {
        $out .= "<meta name=\"robots\" content=\"$robots\" />";
    }

    return $out;
}

function arc_meta_keywords($atts)
{
    global $thisarticle;

    extract(lAtts(array(
        'keywords' => null
    ), $atts));

    $keywords = $keywords===null && isset($thisarticle['keywords']) ? $thisarticle['keywords'] : null;

    if ($keywords) {
        $keywords = txpspecialchars($keywords);
        return "<meta name=\"keywords\" content=\"$keywords\" />";
    }

    return '';

}

function arc_meta_open_graph($atts)
{
    global $thisarticle, $prefs, $s, $c;

    extract(lAtts(array(
        'site_name' => $prefs['sitename'],
        'title' => null,
        'description' => null,
        'url' => null,
        'image' => null
    ), $atts));

    $title = $title===null ? _arc_meta_title() : $title;
    $description = $description===null ? _arc_meta_description() : $description;
    $url = $url===null ? _arc_meta_url() : $url;
    $image = $image===null ? _arc_meta_image() : $image;

    $html = '';
    if ($site_name) {
        $html .= "<meta property=\"og:site_name\" content=\"$site_name\" />";
    }
    if ($title)    {
        $html .= "<meta property=\"og:title\" content=\"$title\" />";
    }
    if ($description) {
        $html .= "<meta property=\"og:description\" content=\"$description\" />";
    }
    if ($url) {
        $html .= "<meta property=\"og:url\" content=\"$url\" />";
    }
    if ($image) {
        $html .= "<meta property=\"og:image\" content=\"$image\" />";
    }

    return $html;
}

function arc_meta_twitter_card($atts)
{
    global $thisarticle, $prefs, $s, $c;

    extract(lAtts(array(
        'card' => 'summary',
        'title' => null,
        'description' => null,
        'url' => null,
        'image' => null
    ), $atts));

    $title = $title===null ? _arc_meta_title() : $title;
    $description = $description===null ? _arc_meta_description() : $description;
    $url = $url===null ? _arc_meta_url() : $url;
    $image = $image===null ? _arc_meta_image() : $image;

    $html = "<meta name=\"twitter:card\" content=\"$card\" />";
    $html .= "<meta name=\"twitter:title\" content=\"$title\" />";
    $html .= "<meta name=\"twitter:description\" content=\"$description\" />";

    if ($url) {
        $html .= "<meta name=\"twitter:url\" content=\"$url\" />";
    }
    if ($image) {
        $html .= "<meta name=\"twitter:image:src\" content=\"$image\" />";
    }

    return $html;

}

function arc_meta_organization($atts)
{
    global $prefs;

    extract(lAtts(array(
        'name' => $prefs['sitename'],
        'logo' => null,
        'facebook' => null,
        'gplus' => null,
        'twitter' => null
    ), $atts));

    if (empty($logo)) {
        trigger_error('arc_meta_organization missing logo attribute', E_USER_WARNING);
    }

    $data = array(
        '@context' => 'http://schema.org',
        '@type' => 'Organization',
        'name' => $name,
        'logo' => $logo,
        'url' => hu
    );

    $sameAs = array();
    if (!empty($facebook)) {
        $sameAs[] = $facebook;
    }
    if (!empty($gplus)) {
        $sameAs[] = $gplus;
    }
    if (!empty($twitter)) {
        $sameAs[] = $twitter;
    }

    if (!empty($sameAs)) {
        $data['sameAs'] = $sameAs;
    }

    return '<script type="application/ld+json">' . str_replace('\\/', '/', json_encode($data)) . '</script>';

}

function arc_meta_person($atts)
{
    global $prefs;

    extract(lAtts(array(
        'name' => $prefs['sitename'],
        'logo' => null,
        'facebook' => null,
        'gplus' => null,
        'twitter' => null
    ), $atts));

    $data = array(
        '@context' => 'http://schema.org',
        '@type' => 'Person',
        'name' => $name,
        'url' => hu
    );

    $sameAs = array();
    if (!empty($facebook)) {
        $sameAs[] = $facebook;
    }
    if (!empty($gplus)) {
        $sameAs[] = $gplus;
    }
    if (!empty($twitter)) {
        $sameAs[] = $twitter;
    }

    if (!empty($sameAs)) {
        $data['sameAs'] = $sameAs;
    }

    return '<script type="application/ld+json">' . str_replace('\\/', '/', json_encode($data)) . '</script>';

}

function _arc_meta_title()
{
    global $thisarticle, $prefs, $s, $c;

    if (!empty($thisarticle['thisid'])) {
        $title = txpspecialchars($thisarticle['title']);
    } elseif (!empty($s) and $s != 'default') {
        $title = txpspecialchars(fetch_section_title($s));
    } elseif (!empty($c)) {
        $title = txpspecialchars(fetch_category_title($c));
    } else {
        $title = txpspecialchars($prefs['sitename']);
    }

    return $title;

}

function _arc_meta_url()
{
    global $thisarticle, $s, $c;

    if (!empty($thisarticle['thisid'])) {
        $url = permlinkurl($thisarticle);
    } elseif (!empty($s) and $s != 'default') {
        $url = pagelinkurl(array('s' => $s));
    } elseif (!empty($c)) {
        $url = pagelinkurl(array('c' => $c));
    } else {
        $url = hu;
    }
    return $url;
}

function _arc_meta_image()
{
    global $thisarticle;

    $image = $thisarticle['article_image'];

    if (intval($image)) {

        if ($rs = safe_row('*', 'txp_image', 'id = ' . intval($image))) {
            $image = imagesrcurl($rs['id'], $rs['ext']);
        } else {
            $image = null;
        }

    } else {

        $meta = _arc_meta();
        if (!empty($meta['image']) && $rs = safe_row('*', 'txp_image', 'id = ' . intval($meta['image']))) {
            $image = imagesrcurl($rs['id'], $rs['ext']);
        } else {
            $image = null;
        }

    }

    return $image;
}

function _arc_meta_description()
{
    global $thisarticle;

    $meta = _arc_meta();

    if (!empty($meta['description'])) {
        $description = txpspecialchars($meta['description']);
    } elseif (!empty($thisarticle['excerpt'])) {
        $description = strip_tags($thisarticle['excerpt']);
        $description = substr($description, 0, 200);
        $description = txpspecialchars($description);
    } elseif (!empty($thisarticle['body'])) {
        $description = strip_tags($thisarticle['body']);
        $description = substr($description, 0, 200);
        $description = txpspecialchars($description);
    } else {
        $description = null;
    }

    return $description;
}

function _arc_meta($type = null, $typeId = null)
{
    global $thisarticle, $s, $c, $arc_meta;

    if (empty($arc_meta)) {

        if (empty($type) || empty($typeId)) {

            if (!empty($thisarticle['thisid'])) {
                $typeId = $thisarticle['thisid'];
                $type = 'article';
            } elseif (!empty($c)) {
                $typeId = $c;
                $type = 'category';
            } elseif (!empty($s)) {
                $typeId = $s;
                $type = 'section';
            }

        }

        $arc_meta = array(
            'id' => null,
            'title' => null,
            'description' => null,
            'image' => null,
            'robots' => null
        );

        if (!empty($typeId) && !empty($type)) {

            $meta = safe_row('*', 'arc_meta', "type_id='$typeId' AND type='$type'");
            $arc_meta = array_merge($arc_meta, $meta);
            return $arc_meta;
        }

    }

    return $arc_meta;
}

if (@txpinterface == 'admin') {

    register_callback('_arc_meta_article_meta', 'article_ui', 'keywords');
    register_callback('_arc_meta_article_meta_save', 'ping');
    register_callback('_arc_meta_article_meta_save', 'article_saved');
    register_callback('_arc_meta_article_meta_save', 'article_posted');

    register_callback('_arc_meta_section_meta', 'section_ui', 'extend_detail_form');
    register_callback('_arc_meta_section_meta_save', 'section', 'section_save');

    register_callback('_arc_meta_category_meta', 'category_ui', 'extend_detail_form');
    register_callback('_arc_meta_category_meta_save', 'category', 'cat_article_save');

    if (!empty($prefs['arc_meta_section_tab'])) {
        add_privs('arc_meta_section_tab', '1,2,3,4');
        register_tab($prefs['arc_meta_section_tab'], 'arc_meta_section_tab', 'Sections Meta Data');
        register_callback('arc_meta_section_tab', 'arc_meta_section_tab');
    }
}

function _arc_meta_install()
{
    $sql = "CREATE TABLE IF NOT EXISTS " . PFX . "arc_meta (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `type` varchar(8) NOT NULL,
        `type_id` varchar(128) NOT NULL,
        `title` varchar(250) DEFAULT NULL,
        `override_title` tinyint(1) DEFAULT NULL,
        `description` varchar(250) DEFAULT NULL,
        `robots` varchar(45) DEFAULT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

    if (!safe_query($sql)) {
        return 'Error - unable to create arc_meta table';
    }

    $dbTable = getThings('DESCRIBE ' . safe_pfx('arc_meta'));

    if (!in_array('robots', $dbTable)) {
        safe_alter('arc_meta', 'ADD robots VARCHAR(45)');
    }

    if (!in_array('image', $dbTable)) {
        safe_alter('arc_meta', 'ADD image INT(11) DEFAULT NULL');
        // Increased size of title and description columns.
        safe_alter('arc_meta', 'CHANGE title title VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
        safe_alter('arc_meta', 'CHANGE description description VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL');
    }

    // Setup the plugin preferences.
    _arc_meta_install_prefs();

    return;
}

/**
 * Setup the plugin preferences if they have not yet been set.
 */
function _arc_meta_install_prefs()
{
    if (!isset($prefs['arc_meta_article_title'])) {
        set_pref('arc_meta_article_title', '%a | %n', 'arc_meta', 1, 'text_input');
    }
    if (!isset($prefs['arc_meta_comment_title'])) {
        set_pref('arc_meta_comment_title', gTxt('comments_on').' %a | %n', 'arc_meta', 1, 'text_input');
    }
    if (!isset($prefs['arc_meta_search_title'])) {
        set_pref('arc_meta_search_title', gTxt('search_results') . ': ' . '%q | %n', 'arc_meta', 1, 'text_input');
    }
    if (!isset($prefs['arc_meta_category_title'])) {
        set_pref('arc_meta_category_title', '%c | %n', 'arc_meta', 1, 'text_input');
    }
    if (!isset($prefs['arc_meta_section_title'])) {
        set_pref('arc_meta_section_title', '%s | %n', 'arc_meta', 1, 'text_input');
    }
    if (!isset($prefs['arc_meta_section_category_title'])) {
        set_pref('arc_meta_section_category_title', '%c - %s | %n', 'arc_meta', 1, 'text_input');
    }
    if (!isset($prefs['arc_meta_homepage_title'])) {
        set_pref('arc_meta_homepage_title', '%n | %t', 'arc_meta', 1, 'text_input');
    }
    if (!isset($prefs['arc_meta_section_tab'])) {
        set_pref('arc_meta_section_tab', 'content', 'arc_meta', 1, 'text_input');
    }
    return;
}

function _arc_meta_uninstall()
{
    $sql = "DROP TABLE IF EXISTS ".PFX."arc_meta;";
    if (!safe_query($sql)) {
        return 'Error - unable to delete arc_meta table';
    }

    $sql = "DELETE FROM  ".PFX."txp_prefs WHERE event='arc_meta';";
    if (!safe_query($sql)) {
        return 'Error - unable to delete arc_meta preferences';
    }
    return;
}

function arc_meta_section_tab($event, $step)
{
    switch ($step) {
        case 'edit':
            arc_meta_section_edit();
            break;

        case 'save':
            _arc_meta_section_meta_save($event, $step);

        default:
            arc_meta_section_list();
            break;
    }
}

function arc_meta_section_list()
{
    global $event;

    pagetop('Section Meta Data');

    $html = '<h1 class="txp-heading">Section Meta Data</h1>';

    $rs = safe_query(
        'SELECT sections.*, arc_meta.title AS meta_title FROM ' . safe_pfx('txp_section') . ' sections LEFT JOIN ' . safe_pfx('arc_meta') . ' arc_meta ON arc_meta.type = "section" AND arc_meta.type_id = sections.name WHERE 1=1 ORDER BY CASE WHEN sections.name = "default" THEN 1 ELSE 2 END, sections.name ASC'
    );

    if ($rs) {

        $html .= n . '<div id="' . $event . '_container" class="txp-container">';

        $html .= n . '<div class="txp-listtables">' . n
                . n . startTable('', '', 'txp-list')
                . n . '<thead>'
                . n . tr(hCell('Name') . hCell('Title') . hCell('Meta Title') . hCell('Manage'))
            . n . '</thead>';

        while ($row = nextRow($rs)) {
            $editLink = href(
                gTxt('edit'),
                '?event=arc_meta_section_tab&amp;step=edit&amp;name=' . $row['name']
            );
            $html .= n . tr(
                n . td($row['name']) . td($row['title']) . td($row['meta_title']) . td($editLink)
            );
        }

        $html .= n . endTable();

        $html .= n . '</div>';

    }

    echo $html;
}

function arc_meta_section_edit()
{
    global $event;

    $name = gps('name');

    $rs = safe_query(
        'SELECT sections.title AS section, arc_meta.* FROM ' . safe_pfx('txp_section') . ' sections LEFT JOIN ' . safe_pfx('arc_meta') . ' arc_meta ON arc_meta.type = "section" AND arc_meta.type_id = sections.name WHERE sections.name="' . doSlash($name) . '"'
    );

    $meta = nextRow($rs);

    pagetop('Section Meta Data');

    $form = '';

    $form .= '<div class="txp-edit">';
    $form .= hed('Edit Section Meta Data', 2);

    // We include the section title as a disabled field for the user's
    // reference.
    $form .= "<p class='edit-section-arc_meta_section'>";
    $form .= "<span class='edit-label'> " . tag('Section', 'label', ' for="section"') . '</span>';
    $form .= "<span class='edit-value'> " . fInput('text', 'section', $meta['section'], '', '', '', '32', '', 'section', true) . '</span>';
    $form .= '</p>';

    // Meta data fields
    $form .= hInput('arc_meta_id', $meta['id']);
    $form .= hInput('name', $name);
    $form .= "<p class='edit-section-arc_meta_title'>";
    $form .= "<span class='edit-label'> " . tag('Meta title', 'label', ' for="arc_meta_title"') . '</span>';
    $form .= "<span class='edit-value'> " . fInput('text', 'arc_meta_title', $meta['title'], '', '', '', '32', '', 'arc_meta_title') . '</span>';
    $form .= '</p>';
    $form .= "<p class='edit-section-arc_meta_description'>";
    $form .= "<span class='edit-label'> " . tag('Meta description', 'label', ' for="arc_meta_description"') . '</span>';
    $form .= "<span class='edit-value'> " . text_area('arc_meta_description', null, null, $meta['description'], 'arc_meta_description') . '</span>';
    $form .= '</p>';
    $form .= "<p class='edit-section-arc_meta_image'>";
    $form .= "<span class='edit-label'> " . tag('Meta image', 'label', ' for="arc_meta_image"') . '</span>';
    $form .= "<span class='edit-value'> " . fInput('number', 'arc_meta_image', $meta['image'], '', '', '', '32', '', 'arc_meta_image') . '</span>';
    $form .= '</p>';
    $form .= "<p class='edit-category-arc_meta_robots'>";
    $form .= "<span class='edit-label'> " . tag('Meta robots', 'label', ' for="arc_meta_description"') . '</span>';
    $form .= "<span class='edit-value'> " . selectInput('arc_meta_robots', _arc_meta_robots(), $meta['robots'], 'arc_meta_robots') . '</span>';
    $form .= '</p>';

    $form .= sInput('save') . eInput($event) . fInput('submit', 'save', gTxt('Save'), 'publish');

    $form .= '</div>';

    $html = '<div id="' . $event . '_container" class="txp-container">' . form($form, '', '', 'post', 'edit-form') . '</div>';

    echo $html;
}

function arc_meta_options($event, $step)
{
    global $prefs;

    if ($step == 'prefs_save') {
        pagetop('arc_meta', 'Preferences saved');
    } else {
        pagetop('arc_meta');
    }

    // Define the form fields.
    $fields = array(
        'arc_meta_article_title' => 'Article Page Titles',
        'arc_meta_comment_title' => 'Comment Page Titles',
        'arc_meta_search_title' => 'Search Page Titles',
        'arc_meta_category_title' => 'Category Titles',
        'arc_meta_section_title' => 'Section Titles',
        'arc_meta_section_category_title' => 'Section Category Titles',
        'arc_meta_section_tab' => 'Location of Panel'
    );

    if ($step == 'prefs_save') {

        foreach ($fields as $key => $label) {
            $prefs[$key] = trim(gps($key));
            set_pref($key, $prefs[$key]);
        }

    }

    // Remove the arc_meta_section_tab field as we want to manually add this.
    unset($fields['arc_meta_section_tab']);

    $form = '';

    $form .= hed('Page Title Patterns', 2);

    foreach ($fields as $key => $label) {
        $form .= "<p class='$key'><span class='edit-label'><label for='$key'>$label</label></span>";
        $form .= "<span class='edit-value'>" . fInput('text', $key, $prefs[$key], '', '', '', '', '', $key) . "</span>";
        $form .= '</p>';
    }

    $panels = array(
        'content' => 'Content',
        'extensions' => 'Extensions',
        '' => 'Hidden'
    );

    $panel = $prefs['arc_meta_section_tab'];

    $form .= hed('Sections Meta Data Panel', 2);
    $form .= '<p class="arc_meta_section_tab"><span class="edit-label"><label for="arc_meta_section_tab">Location of Panel</label></span>';
    $form .= '<span class="edit-value">' . selectInput('arc_meta_section_tab', $panels, $panel, '', '', 'arc_meta_section_tab') . "</span>";
    $form .= '</p>';

    $form .= sInput('prefs_save') . n . eInput('plugin_prefs.arc_meta');

    $form .= '<p>' . fInput('submit', 'Submit', gTxt('save_button'), 'publish') . '</p>';

    $html = "<h1 class='txp-heading'>arc_meta</h1>";
    $html .= form("<div class='txp-edit'>" . $form . "</div>", " class='edit-form'");

    echo $html;

    return;
}

function _arc_meta_article_meta($event, $step, $data, $rs)
{
    // Get the article meta data.
    $articleId = !empty($rs['ID']) ? $rs['ID'] : null;
    $meta = _arc_meta('article', $articleId);

    $form = hInput('arc_meta_id', $meta['id']);
    $form .= "<p class='arc_meta_title'>";
    $form .= tag('Meta title', 'label', ' for="arc_meta_title"') . '<br />';
    $form .= fInput('text', 'arc_meta_title', $meta['title'], '', '', '', '32', '', 'arc_meta_title');
    $form .= "</p>";
    $form .= "<p class='arc_meta_description'>";
    $form .= tag('Meta description', 'label', ' for="arc_meta_description"') . '<br />';
    $form .= text_area('arc_meta_description', null, null, $meta['description'], 'arc_meta_description');
    $form .= "</p>";
    $form .= "<p class='edit-category-arc_meta_robots'>";
    $form .= tag('Meta robots', 'label', ' for="arc_meta_description"') . '<br />';
    $form .= selectInput('arc_meta_robots', _arc_meta_robots(), $meta['robots'], 'arc_meta_robots');
    $form .= '</p>';

    return $form.$data;
}

function _arc_meta_section_meta($event, $step, $data, $rs)
{
    // Get the section meta data.
    $sectionName = !empty($rs['name']) ? $rs['name'] : null;
    $meta = _arc_meta('section', $sectionName);

    $form = hInput('arc_meta_id', $meta['id']);
    $form .= "<p class='edit-section-arc_meta_title'>";
    $form .= "<span class='edit-label'> " . tag('Meta title', 'label', ' for="arc_meta_title"') . '</span>';
    $form .= "<span class='edit-value'> " . fInput('text', 'arc_meta_title', $meta['title'], '', '', '', '32', '', 'arc_meta_title') . '</span>';
    $form .= '</p>';
    $form .= "<p class='edit-section-arc_meta_description'>";
    $form .= "<span class='edit-label'> " . tag('Meta description', 'label', ' for="arc_meta_description"') . '</span>';
    $form .= "<span class='edit-value'> " . text_area('arc_meta_description', null, null, $meta['description'], 'arc_meta_description') . '</span>';
    $form .= '</p>';
    $form .= "<p class='edit-section-arc_meta_image'>";
    $form .= "<span class='edit-label'> " . tag('Meta image', 'label', ' for="arc_meta_image"') . '</span>';
    $form .= "<span class='edit-value'> " . fInput('number', 'arc_meta_image', $meta['image'], '', '', '', '32', '', 'arc_meta_image') . '</span>';
    $form .= '</p>';
    $form .= "<p class='edit-category-arc_meta_robots'>";
    $form .= "<span class='edit-label'> " . tag('Meta robots', 'label', ' for="arc_meta_description"') . '</span>';
    $form .= "<span class='edit-value'> " . selectInput('arc_meta_robots', _arc_meta_robots(), $meta['robots'], 'arc_meta_robots') . '</span>';
    $form .= '</p>';

    return $data.$form;
}

function _arc_meta_category_meta($event, $step, $data, $rs)
{
    // Make sure that this is an article category (we don't support other
    // category types).
    if ($rs['type']!='article') {
        return $data;
    }

    // Get the existing meta data for this category.
    $meta = _arc_meta('category', $rs['name']);

    $form = hInput('arc_meta_id', $meta['id']);
    $form .= "<p class='edit-category-arc_meta_title'>";
    $form .= "<span class='edit-label'> " . tag('Meta title', 'label', ' for="arc_meta_title"') . '</span>';
    $form .= "<span class='edit-value'> " . fInput('text', 'arc_meta_title', $meta['title'], '', '', '', '32', '', 'arc_meta_title') . '</span>';
    $form .= '</p>';
    $form .= "<p class='edit-category-arc_meta_description'>";
    $form .= "<span class='edit-label'> " . tag('Meta description', 'label', ' for="arc_meta_description"') . '</span>';
    $form .= "<span class='edit-value'> " . text_area('arc_meta_description', null, null, $meta['description'], 'arc_meta_description') . '</span>';
    $form .= '</p>';
    $form .= "<p class='edit-category-arc_meta_image'>";
    $form .= "<span class='edit-label'> " . tag('Meta image', 'label', ' for="arc_meta_image"') . '</span>';
    $form .= "<span class='edit-value'> " . fInput('number', 'arc_meta_image', $meta['image'], '', '', '', '32', '', 'arc_meta_image') . '</span>';
    $form .= '</p>';
    $form .= "<p class='edit-category-arc_meta_robots'>";
    $form .= "<span class='edit-label'> " . tag('Meta robots', 'label', ' for="arc_meta_description"') . '</span>';
    $form .= "<span class='edit-value'> " . selectInput('arc_meta_robots', _arc_meta_robots(), $meta['robots'], 'arc_meta_robots') . '</span>';
    $form .= '</p>';

    return $data.$form;
}

function _arc_meta_article_meta_save($event, $step)
{
    $articleId = empty($GLOBALS['ID']) ? gps('ID') : $GLOBALS['ID'];

    $metaId = gps('arc_meta_id');
    $metaTitle = gps('arc_meta_title');
    $metaDescription = gps('arc_meta_description');
    $metaRobots = gps('arc_meta_robots');

    $values = array(
        'type' => 'article',
        'type_id' => $articleId,
        'title' => doSlash($metaTitle),
        'description' => substr(doSlash($metaDescription), 0, 250),
        'robots' => doSlash($metaRobots)
    );

    foreach ($values as $key => $value) {
        $sql[] = "$key = '$value'";
    }
    $sql = implode(', ', $sql);

    if ($metaId) {

        // Update existing meta data.
        safe_update('arc_meta', $sql, "id=$metaId");

    } elseif (!empty($metaTitle) || !empty($metaDescription) || !empty($metaRobots)) {

        // Create new meta data only if there is data to be saved.
        safe_insert('arc_meta', $sql);

    }
}

function _arc_meta_section_meta_save($event, $step)
{
    $sectionName = gps('name');

    $metaId = gps('arc_meta_id');
    $metaTitle = gps('arc_meta_title');
    $metaDescription = gps('arc_meta_description');
    $metaImage = gps('arc_meta_image');
    $metaRobots = gps('arc_meta_robots');

    $values = array(
        'type' => 'section',
        'type_id' => $sectionName,
        'title' => doSlash($metaTitle),
        'description' => substr(doSlash($metaDescription), 0, 250),
        'image' => intval($metaImage),
        'robots' => doSlash($metaRobots)
    );

    if (empty($values['image'])) {
        unset($values['image']);
        $sql[] = "image = NULL";
    }

    foreach ($values as $key => $value) {
        $sql[] = "$key = '$value'";
    }
    $sql = implode(', ', $sql);

    if ($metaId) {

        // Update existing meta data.
        safe_update('arc_meta', $sql, "id=$metaId");

    } elseif (!empty($metaTitle) || !empty($metaDescription) || !empty($metaImage) || !empty($metaRobots)) {

        // Create new meta data only if there is data to be saved.
        safe_insert('arc_meta', $sql);

    }
}

function _arc_meta_category_meta_save($event, $step)
{
    $categoryName = gps('name');

    $metaId = gps('arc_meta_id');
    $metaTitle = gps('arc_meta_title');
    $metaDescription = gps('arc_meta_description');
    $metaImage = gps('arc_meta_image');
    $metaRobots = gps('arc_meta_robots');

    $values = array(
        'type' => 'category',
        'type_id' => $categoryName,
        'title' => doSlash($metaTitle),
        'description' => substr(doSlash($metaDescription), 0, 250),
        'image' => intval($metaImage),
        'robots' => doSlash($metaRobots)
    );

    foreach ($values as $key => $value) {
        $sql[] = "$key = '$value'";
    }
    $sql = implode(', ', $sql);

    if ($metaId) {

        // Update existing meta data.
        safe_update('arc_meta', $sql, "id=$metaId");

    } elseif (!empty($metaTitle) || !empty($metaDescription) || !empty($metaImage) || !empty($metaRobots)) {

        // Create new meta data only if there is data to be saved.
        safe_insert('arc_meta', $sql);

    }
}

function _arc_meta_robots()
{
    return array(
        'index, follow' => 'index, follow',
        'index, nofollow' => 'index, nofollow',
        'noindex, follow' => 'noindex, follow',
        'noindex, nofollow' => 'noindex, nofollow'
    );
}

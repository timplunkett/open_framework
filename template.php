<?php

/**
 * @file
 * Functions to support theming in the Open Framework theme.
 */

/**
 * Implements hook_preprocess_HOOK() for html.tpl.php.
 */
function open_framework_preprocess_html(&$variables) {
  // Theme option variables.
  $variables['front_heading_classes'] = theme_get_setting('front_heading_classes');
  $variables['breadcrumb_classes'] = theme_get_setting('breadcrumb_classes');
  $variables['border_classes'] = theme_get_setting('border_classes');
  $variables['corner_classes'] = theme_get_setting('corner_classes');
  $variables['body_bg_type'] = theme_get_setting('body_bg_type');
  $variables['body_bg_classes'] = theme_get_setting('body_bg_classes');
  $variables['body_bg_path'] = theme_get_setting('body_bg_path');
}

/**
 * Implements hook_js_alter().
 */
function open_framework_js_alter(&$javascript) {
  // Update jquery version for non-administration pages
  if (arg(0) != 'admin' && arg(0) != 'panels' && arg(0) != 'ctools') {
    $jquery_file = drupal_get_path('theme', 'open_framework') . '/js/jquery-1.9.1.min.js';
    $jquery_version = '1.9.1';
    $migrate_file = drupal_get_path('theme', 'open_framework') . '/js/jquery-migrate-1.1.1.min.js';
    $migrate_version = '1.1.1';
    $javascript['misc/jquery.js']['data'] = $jquery_file;
    $javascript['misc/jquery.js']['version'] = $jquery_version;
    $javascript['misc/jquery.js']['weight'] = 0;
    $javascript['misc/jquery.js']['group'] = -101;
    drupal_add_js($migrate_file);
    if (isset($javascript["$migrate_file"])) {
      $javascript["$migrate_file"]['version'] = $migrate_version;
      $javascript["$migrate_file"]['weight'] = 1;
      $javascript["$migrate_file"]['group'] = -101;
    }
  }
}

/**
 * Implements hook_preprocess_HOOK() for page.tpl.php.
 */
function open_framework_preprocess_page(&$variables) {
  // Add page template suggestions based on the aliased path. For instance, if
  // the current page has an alias of about/history/early, we'll have templates
  // of:
  // - page-about-history-early.tpl.php
  // - page-about-history.tpl.php
  // - page-about.tpl.php
  // Whichever is found first is the one that will be used.
  if (module_exists('path')) {
    $current_path = current_path();
    $alias = drupal_get_path_alias(str_replace('/edit', '', $current_path));
    if ($alias != $current_path) {
      $template_filename = 'page';
      foreach (explode('/', $alias) as $path_part) {
        $template_filename = $template_filename . '-' . $path_part;
        $variables['template_files'][] = $template_filename;
      }
    }
  }
  // Get the entire main menu tree.
  $main_menu_tree = menu_tree_all_data('main-menu');

  // Add the rendered output to the $main_menu_expanded variables.
  $variables['main_menu_expanded'] = menu_tree_output($main_menu_tree);

  // Primary nav.
  $variables['primary_nav'] = FALSE;
  if ($variables['main_menu']) {
    $variables['primary_nav'] = array(
      '#theme' => 'links__system_main_menu',
      '#links' => $variables['main_menu'],
      '#attributes' => array(
        'id' => 'main-menu-links',
        'class' => array('links', 'clearfix'),
      ),
      '#heading' => array(
        'text' => t('Main menu'),
        'level' => 'h2',
        'class' => array('element-invisible'),
      ),
    );
  }

  // Secondary nav.
  $variables['secondary_nav'] = FALSE;
  if ($variables['secondary_menu']) {
    $variables['secondary_nav'] = array(
      '#theme' => 'links__system_secondary_menu',
      '#links' => $variables['secondary_menu'],
      '#attributes' => array(
        'id' => 'secondary-menu-links',
        'class' => array('links', 'inline', 'clearfix'),
      ),
      '#heading' => array(
        'text' => t('Secondary menu'),
        'level' => 'h2',
        'class' => array('element-invisible'),
      ),
    );
  }

  // Replace tabs with drop down version
  $variables['tabs']['#primary'] = _bootstrap_local_tasks($variables['tabs']['#primary']);

  // Add variable for site title
  $variables['my_site_title'] = variable_get('site_name');
}

/**
 * Implements hook_preprocess_HOOK() for block.tpl.php.
 */
function open_framework_preprocess_block(&$variables) {
  // Count number of blocks in a given theme region
  $variables['block_count'] = count(block_list($variables['block']->region));
}

/**
* Determines if the region has at least one block for this user
*
* @param string $region
*   A string containing the region name
*
* @return bool
*   TRUE if the region has at least one block. FALSE if it doesn't.
*/
function open_framework_region_has_block($region) {
  $number_of_blocks = count(block_list($region));
  if ($number_of_blocks > 0) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

/**
 * Determines the span for a block.
 *
 * @param int $block_count
 *   The number of blocks in the region
 * @param int $block_id
 *   The position of the block (starts at 1).
 * @param bool $count_sidebars
 *   A boolean indicating whether sidebars should be counted.
 *
 * @return int
 *   The span value for the block at this location and region
 */
function open_framework_get_span($block_count, $block_id, $count_sidebars) {
  // @petechen (6.27.12) This method of applying a value to span assumes that there
  // is at least 1 block. If there are no blocks, you end up with a calculation
  // dividing by 0 generating a php error. Suggest the following change:

  // default span if calculations fail
  // Use this default value instead as an "else" condition below:
  // $span = 12;

  // there are 12 columsn in bootstrap
  $available_width = 12;

  if ($count_sidebars) {
    // we assume that the left and right regions have a span of 3
    // if present, we remove that much from the available width
    if (open_framework_region_has_block('sidebar_first')) {
      $available_width = $available_width - 0;
    }

    if (open_framework_region_has_block('sidebar_second')) {
      $available_width = $available_width - 0;
    }
  }

  // @petechen - surroung this condition with another if else to account for $block_count = 0

  if ($block_count != 0) {

    // if the number of blocks divides evenly into the available width, that's our span width
    if (($available_width % $block_count) == 0) {
      $span = $available_width / $block_count;
    }
    // if the number of blocks does not divide evenly, we look up the span widths in an array
    // where then indexes are available width, number of blocks, and block position
    // e.g. [9][2][1] is the span of the first block, out of two when the available width is 9.
    else {
      $exceptions[6][4][1] = 2;
      $exceptions[6][4][2] = 2;
      $exceptions[6][4][3] = 1;
      $exceptions[6][4][4] = 1;

      $exceptions[6][5][1] = 1;
      $exceptions[6][5][2] = 1;
      $exceptions[6][5][3] = 1;
      $exceptions[6][5][4] = 1;
      $exceptions[6][5][5] = 1;

      $exceptions[9][2][1] = 3;
      $exceptions[9][2][2] = 6;

      $exceptions[9][4][1] = 3;
      $exceptions[9][4][2] = 2;
      $exceptions[9][4][3] = 2;
      $exceptions[9][4][4] = 2;

      $exceptions[9][5][1] = 3;
      $exceptions[9][5][2] = 1;
      $exceptions[9][5][3] = 1;
      $exceptions[9][5][4] = 1;
      $exceptions[9][5][5] = 3;

      $exceptions[9][6][1] = 2;
      $exceptions[9][6][2] = 2;
      $exceptions[9][6][3] = 2;
      $exceptions[9][6][4] = 1;
      $exceptions[9][6][5] = 1;
      $exceptions[9][6][6] = 1;

      $exceptions[12][5][1] = 3;
      $exceptions[12][5][2] = 2;
      $exceptions[12][5][3] = 2;
      $exceptions[12][5][4] = 2;
      $exceptions[12][5][5] = 3;

      $span = $exceptions[$available_width][$block_count][$block_id];
    }
    return $span;
  }
  // @petechen: so if $block_count = 0, use this as the default
  else $span = 12;
}

/**
 * Overrides theme_status_messages().
 */
function open_framework_status_messages($variables) {
  $display = $variables['display'];
  $output = '';

  $status_heading = array(
    'status' => t('Status message'),
    'error' => t('Error message'),
    'warning' => t('Warning message'),
  );

  // Map Drupal message types to their corresponding Bootstrap classes.
  // @see http://twitter.github.com/bootstrap/components.html#alerts
  $status_class = array(
    'status' => 'success',
    'error' => 'error',
    'warning' => 'info',
  );

  foreach (drupal_get_messages($display) as $type => $messages) {
    $class = (isset($status_class[$type])) ? ' alert-' . $status_class[$type] : '';
    $output .= "<div class=\"alert alert-block$class\">\n";

    if (arg(0) != 'admin' && arg(0) != 'panels' && arg(0) != 'ctools') {
    $output .= "  <a class=\"close\" data-dismiss=\"alert\" href=\"#\">x</a>\n";
    }

    if (!empty($status_heading[$type])) {
      $output .= '<h2 class="element-invisible">' . $status_heading[$type] . "</h2>\n";
    }

    if (count($messages) > 1) {
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= $messages[0];
    }

    $output .= "</div>\n";
  }
  return $output;
}

/**
 * Impelements hook_form_alter().
 */
function open_framework_form_alter(&$form, &$form_state, $form_id) {
  if ($form_id == 'search_block_form') {
    $form['search_block_form']['#title_display'] = 'invisible';
    $form['search_block_form']['#attributes']['class'][] = 'input-medium search-query';
    $form['search_block_form']['#attributes']['placeholder'] = t('Search this site...');
    $form['actions']['submit']['#attributes']['class'][] = 'btn-search';
    $form['actions']['submit']['#type'] = 'image_button';
    $form['actions']['submit']['#src'] = drupal_get_path('theme', 'open_framework') . '/images/searchbutton.png';
  }
}

/**
 * Overrides theme_menu_local_tasks().
 */
function open_framework_menu_local_tasks($variables) {
  $output = '';

  if ( !empty($variables['primary']) ) {
    $variables['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $variables['primary']['#prefix'] = '<ul class="nav nav-tabs">';
    $variables['primary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['primary']);
  }

  if ( !empty($variables['secondary']) ) {
    $variables['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
    $variables['secondary']['#prefix'] = '<ul class="nav nav-pills">';
    $variables['secondary']['#suffix'] = '</ul>';
    $output .= drupal_render($variables['secondary']);
  }

  return $output;
}

/**
 * Overrides theme_menu_local_task().
 */
function open_framework_menu_local_task($variables) {
  $link = $variables['element']['#link'];
  $link_text = $link['title'];
  $classes = array();

  if (!empty($variables['element']['#active'])) {
    // Add text to indicate active tab for non-visual users.
    $active = '<span class="element-invisible">' . t('(active tab)') . '</span>';

    // If the link does not contain HTML already, check_plain() it now.
    // After we set 'html'=TRUE the link will not be sanitized by l().
    if (empty($link['localized_options']['html'])) {
      $link['title'] = check_plain($link['title']);
    }
    $link['localized_options']['html'] = TRUE;
    $link_text = t('!local-task-title!active', array('!local-task-title' => $link['title'], '!active' => $active));

    $classes[] = 'active';
  }

  return '<li class="' . implode(' ', $classes) . '">' . l($link_text, $link['href'], $link['localized_options']) . "</li>\n";
}

/**
 * Overrides theme_menu_tree().
 */
function open_framework_menu_tree($variables) {
  return '<ul class="menu nav">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_menu_link().
 *
 * Apply bootstrap menu classes to all menu blocks in the
 * navigation region and the main-menu block by default.
 * Note: if a menu is in the navigation and somewhere else as well,
 *       both instances of the menu will have the classes applied,
 *       not just the one in the navigation
 */
function open_framework_menu_link(array $variables) {
  $element = $variables['element'];

  if (open_framework_is_in_nav_menu($element)) {
    $sub_menu = '';

    if ($element['#below']) {
      // Add our own wrapper
      unset($element['#below']['#theme_wrappers']);
      $sub_menu = '<ul class="dropdown-menu">' . drupal_render($element['#below']) . '</ul>';
      $element['#localized_options']['attributes']['class'][] = 'dropdown-toggle';
      $element['#localized_options']['attributes']['data-toggle'] = 'dropdown';

      // Check if this element is nested within another
      if ((!empty($element['#original_link']['depth'])) && ($element['#original_link']['depth'] > 1)) {
      // Generate as dropdown submenu
        $element['#attributes']['class'][] = 'dropdown-submenu';
      }
      else {
        // Generate as standard dropdown
        $element['#attributes']['class'][] = 'dropdown';
        $element['#localized_options']['html'] = TRUE;
        $element['#title'] .= ' <span class="caret"></span>';
      }

      // Set dropdown trigger element to # to prevent inadvertant page loading with submenu click
      $element['#localized_options']['attributes']['data-target'] = '#';
    }

    $output = l($element['#title'], $element['#href'], $element['#localized_options']);
    return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";

  } else {
    $element = $variables['element'];
    $sub_menu = '';

    if ($element['#below']) {
      $sub_menu = drupal_render($element['#below']);
    }
    $output = l($element['#title'], $element['#href'], $element['#localized_options']);
    return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
  }
}

/**
 * Get all primary tasks including subsets
 */
function _bootstrap_local_tasks($tabs = FALSE) {
  if ($tabs == '') {
    return $tabs;
  }

  if (!$tabs) {
    $tabs = menu_primary_local_tasks();
  }

  foreach ($tabs as $key => $element) {
    $result = db_select('menu_router', NULL, array('fetch' => PDO::FETCH_ASSOC))
      ->fields('menu_router')
      ->condition('tab_parent', $element['#link']['path'])
      ->condition('context', MENU_CONTEXT_INLINE, '<>')
      ->condition('type', array(MENU_DEFAULT_LOCAL_TASK, MENU_LOCAL_TASK), 'IN')
      ->orderBy('weight')
      ->orderBy('title')
      ->execute();

    $router_item = menu_get_item($element['#link']['href']);
    $map = $router_item['original_map'];

    $i = 0;
    foreach ($result as $item) {
      _menu_translate($item, $map, TRUE);

      //only add items that we have access to
      if ($item['tab_parent'] && $item['access']) {
        //set path to that of parent for the first item
        if ($i === 0) {
          $item['href'] = $element['#link']['href'];
        }

        if (current_path() == $item['href']) {
          $tabs[$key][] = array(
          '#theme' => 'menu_local_task',
          '#link' => $item,
          '#active' => TRUE,
          );
        }
        else {
          $tabs[$key][] = array(
          '#theme' => 'menu_local_task',
          '#link' => $item,
          );
        }

        //only count items we have access to.
        $i++;
      }
    }
  }

  return $tabs;
}

/**
 * Overrides theme_item_list().
 */
function open_framework_item_list($variables) {
  $items = $variables['items'];
  $title = $variables['title'];
  $type = $variables['type'];
  $attributes = $variables['attributes'];
  $output = '';

  if (isset($title)) {
    $output .= '<h3>' . $title . '</h3>';
  }

  if (!empty($items)) {
    $output .= "<$type" . drupal_attributes($attributes) . '>';
    $num_items = count($items);
    foreach ($items as $i => $item) {
      $attributes = array();
      $children = array();
      $data = '';
      if (is_array($item)) {
        foreach ($item as $key => $value) {
          if ($key == 'data') {
            $data = $value;
          }
          elseif ($key == 'children') {
            $children = $value;
          }
          else {
            $attributes[$key] = $value;
          }
        }
      }
      else {
        $data = $item;
      }
      if (count($children) > 0) {
        // Render nested list.
        $data .= theme_item_list(array('items' => $children, 'title' => NULL, 'type' => $type, 'attributes' => $attributes));
      }
      if ($i == 0) {
        $attributes['class'][] = 'first';
      }
      if ($i == $num_items - 1) {
        $attributes['class'][] = 'last';
      }
      $output .= '<li' . drupal_attributes($attributes) . '>' . $data . "</li>\n";
    }
    $output .= "</$type>";
  }

  return $output;
}

/**
 * Find out if an element (a menu link) is a link displayed in the navigation
 * region for the user. We return true by default if this is a menu link in the
 * main-menu. Open Framework treats the main-menu as being in the navigation by
 * default. We are using the theming functions to figure out the block IDs. The
 * block IDs aren't passed to this function, but theming function names are, and
 * those are baed on the block ID.
 */
function open_framework_is_in_nav_menu($element) {
  // #theme holds one or more suggestions for theming function names for the link
  // simplify things by casting into an array
  $link_theming_functions = (array)$element['#theme'];

  // Avoid calculating this more than once
  $nav_theming_functions = &drupal_static(__FUNCTION__);

  // if not done yet, calculate the names of the theming function for all the blocks
  // in the navigation region

  if (!isset($nav_theming_functions)) {

    // get all blocks in the navigation region
    $blocks = block_list('navigation');

    // Blocks placed using the context module don't show up using Drupal's block_list
    // If context is enabled, see if it has placed any blocks in the navigation area
    // See: http://drupal.org/node/785350
    $context_blocks = array();

    if (module_exists('context')) {
      $reaction_block_plugin = context_get_plugin('reaction', 'block');
      $context_blocks = $reaction_block_plugin->block_list('navigation');
    }

    $blocks = array_merge($blocks, $context_blocks);

    // extract just their IDs (<module>_<delta>)
    $ids = array_keys($blocks);

    // translate the ids into function names for comparison purposes
    $nav_theming_functions = array_map('open_framework_block_id_to_function_name', $ids);

  }

  // if there is nothing in the navigation section, the main menu is added automatically, so
  // we watch for that.
  // 'menu_link__main_menu' is the theming function name for the main-menu
  if ((empty($nav_theming_functions)) && (in_array('menu_link__main_menu', $link_theming_functions))) {
    return TRUE;
  };

  // Find out if any of the theming functions for the blocks are the same
  // as the theming functions for the link.
  $intersect = array_intersect($nav_theming_functions, $link_theming_functions);
  if ((!empty($intersect))) {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

/**
 * Convert a block ID to a theming function name
 */
function open_framework_block_id_to_function_name ($id) {
  // If a system block, remove 'system_'.
  $id = str_replace('system_', '', $id);

  // Recognize menu and block_menu module blocks.
  if (strpos($id, 'menu_block_') === false) {
    // If a menu block but not a menu_block block, remove 'menu_'.
    $id = str_replace('menu_',       '', $id);
  }
  else {
    // If a menu_block block, keep menu_block, but add an
    // underscore. Not sure why this is different from other
    // core modules.
    $id = str_replace('menu_block_', 'menu_block__', $id);
  }

  // Massage the ID to looks like a theming function name.
  // Use the same function used to create the name of theming function.
  $id = strtr($id, '-', '_');
  $name = 'menu_link__' . $id;

  return $name;
}

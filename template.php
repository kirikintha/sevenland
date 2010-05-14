<?php
// $Id: template.php,v 1.16.2.2 2009/08/10 11:32:54 goba Exp $

/**
 * Sets the body-tag class attribute.
 *
 * Adds 'sidebar-left', 'sidebar-right' or 'sidebars' classes as needed.
 */
function phptemplate_body_class($left, $right) {
  if ($left != '' && $right != '') {
    $class = 'sidebars';
  }
  else {
    if ($left != '') {
      $class = 'sidebar-left';
    }
    if ($right != '') {
      $class = 'sidebar-right';
    }
  }

  if (isset($class)) {
    print ' class="'. $class .'"';
  }
}

/**
 * Return a themed breadcrumb trail.
 *
 * @param $breadcrumb
 *   An array containing the breadcrumb links.
 * @return a string containing the breadcrumb output.
 */
function phptemplate_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    $active = end( $breadcrumb );
    array_pop( $breadcrumb );
    array_push( $breadcrumb, '<span class="active-trail">' .$active .'</span>' );
    return '<div class="breadcrumb">'. implode(' › ', $breadcrumb) .'</div>';
  }
}

/**
 * Override or insert PHPTemplate variables into the templates.
 */
function phptemplate_preprocess_page(&$vars) {
  $vars['tabs2'] = menu_secondary_local_tasks();

  // Hook into color.module
  if (module_exists('color')) {
    _color_page_alter($vars);
  }
}

/**
 * Add a "Comments" heading above comments except on forum pages.
 */
function garland_preprocess_comment_wrapper(&$vars) {
  if ($vars['content'] && $vars['node']->type != 'forum') {
    $vars['content'] = '<h2 class="comments">'. t('Comments') .'</h2>'.  $vars['content'];
  }
}

/**
 * Returns the rendered local tasks. The default implementation renders
 * them as tabs. Overridden to split the secondary tasks.
 *
 * @ingroup themeable
 */
function phptemplate_menu_local_tasks() {
  return menu_primary_local_tasks();
}

function phptemplate_comment_submitted($comment) {
  return t('!datetime — !username',
    array(
      '!username' => theme('username', $comment),
      '!datetime' => format_date($comment->timestamp)
    ));
}

function phptemplate_node_submitted($node) {
  return t('!datetime — !username',
    array(
      '!username' => theme('username', $node),
      '!datetime' => format_date($node->created),
    ));
}

/**
 * Generates IE CSS links for LTR and RTL languages.
 */
function phptemplate_get_ie_styles() {
  global $language;

  $iecss = '<link type="text/css" rel="stylesheet" media="all" href="'. base_path() . path_to_theme() .'/fix-ie.css" />';
  if ($language->direction == LANGUAGE_RTL) {
    $iecss .= '<style type="text/css" media="all">@import "'. base_path() . path_to_theme() .'/fix-ie-rtl.css";</style>';
  }

  return $iecss;
}

/**
 * Change links to have a menu id, so we can target those items individually
 */
function phptemplate_menu_item_link( $link ) { //Add an ID to the link, so we can translates it into a, li id - since menu_tree is not interceptable
  
  //print_r( $link );
  
  if ( empty($link['localized_options']) ) {
    
    $link['localized_options'] = array();
    
  }
  
  $id = array( $link['module'], $link['mlid'] );
  $link['localized_options']['attributes']['id'] .= implode( '-', $id );

  return l( $link['title'], $link['href'], $link['localized_options'] );
  
}

/**
 * Allow Menu Items to have their own inidivudal class
 */
function phptemplate_menu_item( $link, $has_children, $menu = '', $in_active_trail = FALSE, $extra_class = NULL ) {
  
  $dom = new domDocument;
  @$dom->loadHTML( $link );
  $dom->preserveWhiteSpace = false;
  $links = $dom->getElementsByTagName('a');
  
  foreach ( $links as $tag ) { $id = $tag->getAttribute('id'); }
  
  $class = ( $menu ? 'expanded' : ( $has_children ? 'collapsed' : 'leaf' ) );
  
  if ( !empty( $extra_class ) ) {
    
    $class .= ' '. $extra_class;
    
  }
  if ( $in_active_trail ) {
    
    $class .= ' active-trail';
    
  }
  
  return '<li id="' .$id .'" class="'. $class .'">' .$link . $menu ."</li>\n";
  
}

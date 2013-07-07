<?php

function theme_menu_link(array $variables) {
  //------------------------
  // This function overrides the default menu behavior to add the menu item's 
  // title slug ('page-name-slugized') and the depth of its menu to the link as classes. 
  // Makes for easier theming.  
  
  $element = $variables['element'];
  
  $sub_menu = '';
  
  if ($element['#below']) {
    $sub_menu = drupal_render($element['#below']);
  }
    
  $cleaned_url = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $element['#title']));
  array_push($element['#attributes']['class'], $cleaned_url); 
  array_push($element['#attributes']['class'], 'level-' . $element['#original_link']['depth']); 

  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}


function theme_links($variables) {
  $links = $variables['links'];
  $attributes = $variables['attributes'];
  $heading = $variables['heading'];
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    $output = '';

    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = array(
          'text' => $heading,
          // Set the default level of the heading. 
          'level' => 'h2',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    $output .= '<ul' . drupal_attributes($attributes) . '>';

    $num_links = count($links);
    $i = 1;


    foreach ($links as $key => $link) {
      $class = array($key);
      $class[] = strtolower(str_replace(" ", "", $link['title']));
      // Add first, last and active classes to the list of links to help out themers.
      if ($i == 1) {
        $class[] = 'first';
      }
      if ($i == $num_links) {
        $class[] = 'last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
           && (empty($link['language']) || $link['language']->language == $language_url->language)) {
        $class[] = 'active';
      }
      
      
      $output .= '<li' . drupal_attributes(array('class' => $class)) . ' >';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        $output .= l($link['title'], $link['href'], $link);
      }
      elseif (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $i++;
      $output .= "</li>\n";
    }

    $output .= '</ul>';
  }

  return $output;
}

function theme_preprocess_page(&$variables, $hook) {

    // When this goes through the theme.inc some where it changes _ to - so the tpl name is actually page--type-name.tpl

  if (isset($variables['node'])) {
     $variables['theme_hook_suggestions'][] = 'page__' . $variables['node']->type;
  }
       
       
}


function theme_preprocess_node(&$variables) {

  // Get a list of all the regions for this theme
  foreach (system_region_list($GLOBALS['theme']) as $region_key => $region_name) {

    // Get the content for each region and add it to the $region variable
    if ($blocks = block_get_blocks_by_region($region_key)) {
      $variables['region'][$region_key] = $blocks;
    }
    else {
      $variables['region'][$region_key] = array();
    }
  }
}


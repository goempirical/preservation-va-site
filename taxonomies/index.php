<?php
  add_action('init', 'historical_site_taxonomy_register');

  function historical_site_taxonomy_register () {
    $labels = array(
      "singular_label" => "Historic Site",
      "all_items" => "All Historic Sites",
      "edit_item" => "Edit Historic Site",
      "add_new" => "Add new Historic Site",
      "add_new_item" => "Add new Historic Site",
      "new_item_name" => "Add new Historic Site name",
      "parent_item" => "Parent Site"
    );

    register_taxonomy(
      "historic_sites",
      array("page", "events"),
      array(
        "hierarchical" => true,
        "label" => "Historic Sites",
        "labels" => $labels,
        "rewrite" => true
      )
    );
  }

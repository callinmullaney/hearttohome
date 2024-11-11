<?php

/**
 * @file
 * Views file for config log views module.
 */

/**
 * Implements hook_views_data().
 */
function config_log_views_views_data() {
  $data = [];
  // Base data.
  $data['config_log']['table']['group'] = t('Config log');
  $data['config_log']['table']['base'] = [
    'title' => t('Config log'),
    'help' => t('Config logs module data registry'),
  ];

  // Fields.
  $data['config_log']['clid'] = [
    'title' => t('clid'),
    'help' => t('The config log ID'),
    'field' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
  ];

  $data['config_log']['uid'] = [
    'title' => t('UID'),
    'help' => t('The {users}.uid that created this log entry'),
    'field' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'relationship' => [
      'group' => t('Users'),
      'title' => t('User table'),
      'help' => t('Display user information'),
      'base' => 'users_field_data',
      'base field' => 'uid',
      'relationship field' => 'uid',
      'id' => 'standard',
    ],
  ];

  $data['config_log']['created'] = [
    'title' => t('Entry date'),
    'help' => t('The unix timestamp of the created log entry'),
    'field' => [
      'id' => 'date',
    ],
    'sort' => [
      'id' => 'date',
    ],
    'filter' => [
      'id' => 'date',
    ],
    'argument' => [
      'id' => 'date',
    ],
  ];

  $data['config_log']['data'] = [
    'title' => t('Data'),
    'help' => t('A serialized configuration object data.'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'standard',
    ],
  ];

  $data['config_log']['originaldata'] = [
    'title' => t('Original Data'),
    'help' => t('A serialized configuration object of original data.'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'standard',
    ],
  ];

  $data['config_log']['name'] = [
    'title' => t('Configuration Name'),
    'help' => t('Config object name.'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'standard',
    ],
  ];

  $data['config_log']['old_name'] = [
    'title' => t('Configuration object old name.'),
    'help' => t('For renames, the old Config object name.'),
    'field' => [
      'id' => 'standard',
    ],
    'sort' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'standard',
    ],
  ];

  $data['config_log']['diff_field'] = [
    'title' => t('Difference'),
    'help' => t('Shows the differences'),
    'field' => [
      'id' => 'config_log_diff',
    ],
  ];

  return $data;
}

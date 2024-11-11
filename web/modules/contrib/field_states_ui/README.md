# Field States UI

Field States UI allows sites builders with minimal PHP/Dev skills to configure
the Field States API. This lets you configure a field to for example hide if
another field has a certain value or hasn't been filled. While doing it via PHP
can be more powerful the UI can be very handy and in much quicker.

See also:

- [Project Page on Drupal.org](https://www.drupal.org/project/field_states_ui).
- [Issue Queue](https://www.drupal.org/project/issues/field_states_ui).

## Table of Contents

 * Introduction
 * Requirements
 * Installation
 * Usage
 * Maintainers

## Requirements

This module requires no modules outside of Drupal core.

## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules)


## Usage

### Configure a field

1. Configuration can be accessed per field instance wherever that field
display is usually configured. For example fields on the Node type Article
would be at `/admin/structure/types/manage/article/form-display`.
1. Use the contextual gear icon to edit the field.
1. There is now a Manage Fields States fieldset, select a new state, configure,
click 'Add' and Save.
1. Save the Form Display

### Field Types/Widgets
Due to various array structures, specific widgets/fields need to handled
specifically for each field at least. Many fields are the same structure
for all widgets and are not documented except in the code. Unrecognized
widgets and field types will attempt to be handled but some will be logged
to the dblog in cases that Field States UI is unsure. Please report those
to the [issue queue](https://www.drupal.org/project/issues/field_states_ui).
Currently more than 50 field widgets covering more than 30 field types from
core and various contrib modules are supported.

- Core fields - 22 fields
- [Address](https://drupal.org/project/address) - 3 field types
- [Chosen](https://drupal.org/project/chosen) - 1 field widget
- [Color Field](https://drupal.org/project/color_field) - 1 field type
- [Entity Reference Revisions](https://drupal.org/project/entity_reference_revisions)
\- 1 field type
- [Geofield](https://drupal.org/project/geofield) - 1 field type
- [Inline Entity Form](https://drupal.org/project/inline_entity_form) - 3 field
widgets
- [Layout Fields](https://drupal.org/project/layout_fields) - 1 field widget
- [Markup](https://drupal.org/project/markup) - 1 field type
- [Paragraphs](https://drupal.org/project/paragraphs) - 2 field widgets
- [Smart Date](https://drupal.org/project/smart_date) - 1 field type
- [Select2](https://drupal.org/project/select2) - 2 field widgets
- [ViewsReference](https://drupal.org/project/viewsreference) - 1 field type
- [Webform](https://drupal.org/project/webform) - 1 field type
- [Youtube](https://drupal.org/project/youtube) - 1 field type


## Maintainers

- [Nick Dickinson-Wilde (NickDickinsonWilde)](https://www.drupal.org/u/nickdickinsonwilde)

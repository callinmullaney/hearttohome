# Sous Content Types Drupal Recipe
A recipe that contains modules and configuration for Sous default content types.

## Configuring Drupal for Recipes

See https://www.drupal.org/files/issues/2023-10-01/Configuring%20Drupal%20to%20Apply%20Recipes.md

## Installing this Recipe

`composer require fourkitchens/sous-content-types`

## Applying this Recipe

If you used the Sous Project as your starterkit:
- `lando install-recipe fourkitchens/sous-content-types` 

Manually applying the recipe to your own project:
From your webroot run: 
- `php core/scripts/drupal recipe recipes/sous-content-types`
- `drush cr`
- `composer unpack fourkitchens/sous-content-types`

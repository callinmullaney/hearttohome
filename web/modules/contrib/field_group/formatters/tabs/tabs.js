/**
 * @file
 * Provides the processing logic for tabs.
 */

(function ($, Drupal, once) {
  Drupal.FieldGroup = Drupal.FieldGroup || {};
  Drupal.FieldGroup.Effects = Drupal.FieldGroup.Effects || {};

  /**
   * Switches to the tab of the first invalid field after HTML 5 validation.
   */
  function handleHtml5Validation(context) {
    // Check if browser supports HTML5 validation.
    if (typeof $('<input>')[0].checkValidity !== 'function') {
      return;
    }

    // Can't use .submit() because HTML validation prevents it from running.
    $(
        once(
            'showTabWithError',
            '.form-submit:not([formnovalidate]):not(.field-add-more-submit)',
        ),
    ).on('click', function () {
      const $this = $(this);
      let $form = $this.closest('form');

      // Support themes where the submit button is separated from the form
      // like the Gin theme.
      if (!$form.length) {
        const $formId = $this.attr('form');
        $form = $(`#${$formId}`);
      }

      // Exit early if we can't find a form or the form has no tabs.
      if ($form.length === 0 || $form.find('.field-group-tab').length === 0) {
        return;
      }

      // Check validity of each form element.
      $($form[0].elements).each(function () {
        if (this.checkValidity && !this.checkValidity()) {
          // Set focus to the first invalid tab.
          const $tab = $(this).closest('.field-group-tab');
          if ($tab.length === 0) {
            return false;
          }

          // Fetching all parents in case of nested tabs and focusing on them.
          const $allParents = $tab.parents('.field-group-tab');
          Object.keys($allParents).forEach(function (key) {
            if (key !== 'length' && key !== 'prevObject') {
              // jQuery .parents() returns parent elements from innermost to
              // outermost matches. We need to focus in reverse order to
              // bring the element in view.
              const element =
                  $allParents[$allParents.length - 1 - parseInt(key, 10)];
              Drupal.FieldGroup.setGroupWithFocus($(element));
              const parentDirection = $(element).hasClass('vertical-tabs__pane')
                  ? 'vertical'
                  : 'horizontal';
              $(element).data(`${parentDirection}Tab`).focus();
            }
          });

          // Finally, putting focus on error field.
          const direction = $tab.hasClass('vertical-tabs__pane')
              ? 'vertical'
              : 'horizontal';
          Drupal.FieldGroup.setGroupWithFocus($tab);
          $tab.data(`${direction}Tab`).focus();
          return false;
        }
      });
    });
  }

  /**
   * Implements Drupal.FieldGroup.processHook().
   */
  Drupal.FieldGroup.Effects.processTabs = {
    execute(context, settings, groupInfo) {
      if (groupInfo.context === 'form') {
        // Add required fields mark to any element containing required fields.
        const { direction } = groupInfo.settings;
        $(context)
            .find(`[data-${direction}-tabs-panes]`)
            .each((indexTabs, tabs) => {
              let errorFocussed = false;
              $(once('fieldgroup-effects', $(tabs).find('> details'))).each(
                  (index, element) => {
                    const $this = $(element);
                    if (typeof $this.data(`${direction}Tab`) !== 'undefined') {
                      if (
                          element.matches('.required-fields') &&
                          ($this.find('[required]').length > 0 ||
                              $this.find('.form-required').length > 0)
                      ) {
                        $this
                            .data(`${direction}Tab`)
                            .link.find('strong:first')
                            .addClass('form-required');
                      }

                      if ($('.error', $this).length) {
                        $this
                            .data(`${direction}Tab`)
                            .link.parent()
                            .addClass('error');

                        // Focus the first tab with error.
                        if (!errorFocussed) {
                          Drupal.FieldGroup.setGroupWithFocus($this);
                          $this.data(`${direction}Tab`).focus();
                          errorFocussed = true;
                        }
                      }
                    }
                  },
              );
            });

        // Handle tab changes on HTML 5 validation.
        handleHtml5Validation(context);
      }
    },
  };
})(jQuery, Drupal, once);

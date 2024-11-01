/**
* Block script for Termin-Kalender Shortcode Block
*/

(function(blocks, blockEditor, components, element)
  {
    var el = element.createElement;
    var __ = wp.i18n.__;

    blocks.registerBlockType('termin-kalender/shortcode-block',
      {
      title: "Termin-Kalender",
      category: 'widgets',
      icon: {
        src: 'calendar',
        foreground: '#FF3C00'
        },
      attributes: {
        shortcode: {
          type: 'string',
          source: 'attribute',
          selector: 'div',
          attribute: 'data-shortcode'
        }
        },
      example: { // Added example property for block preview
        attributes: {
          shortcode: '[my-termin-kalender]'
        }
      },
      description: __('A calendar block to overview and organize my monthly tasks, events, schedules etc.', 'termin-kalender'), // Added description
	      edit: function() {
          return wp.element.createElement(
            'div',
{className: 'termin-kalender-block-editor', style: { display: 'flex', alignItems: 'center', gap: '5px', border: '2px solid #856E53', borderRadius: '8px', padding: '1em' } },

            wp.element.createElement("img", {
              src: js_wp_php_vars.TER_KAL_PLUGIN_URL + "assets/icon-128x128.png", // Use a block preview image
              alt: "Termin-Kalender",
              className: 'termin-kalender-block-preview'
            }),
	            wp.element.createElement("p", {}, __('Please use only the Termin-Kalender block on this page. Set to full width page with all other elements deactivated. ', 'termin-kalender')) // Added descriptive text
          );
        },
	      save: function() {
	          return el('div', {}, '[my-termin-kalender]');
        }

      }
    );
  }
)(
  window.wp.blocks,
  window.wp.blockEditor, // Updated from window.wp.editor
  window.wp.components,
  window.wp.element
);
//------------------------------------------------------------------
//  todo-list
wp.blocks.registerBlockType('termin-kalender/todo-list', {
    title: 'Termin-Kalender To-Do List',
    icon: {
      src: 'clipboard',
      foreground: '#FF3C00'
    },
    category: 'widgets',
    edit: function(props) {
        return wp.element.createElement('div', null,
            wp.element.createElement('span', { className: 'block-icon', style: { color: '#FF3C00' } },
                wp.element.createElement('i', { className: 'dashicons dashicons-clipboard' })
            ),
            ' To-Do List Block (Termin-Kalender)'
        );
    },
    save: function() {
        return null; // Server-side rendering
    }
});
//------------------------------------------------------------------


// Termin-Kalender RESERVATIONS
(function(blocks, blockEditor, components, element) {
  const { createElement } = element;
  const { __ } = wp.i18n;
  const { SelectControl, TextControl, Dashicon } = components;

  blocks.registerBlockType('my-termin-reservation/shortcode-block', {
    title: __("Termin-Kalender Reservation/Appointment Form", 'termin-kalender'),
    category: 'widgets',
    icon: {
      src: 'email',
      foreground: '#FF3C00'
    },
    attributes: {
      shortcode: {
        type: 'string',
        source: 'attribute',
        selector: 'div',
        attribute: 'data-shortcode'
      },
      category: {
        type: 'string',
        default: '',
      },
      email: {
        type: 'string',
        default: '',
      }
    },
    description: __('Termin-Kalender request Form: A block to show a requests form for appointment and reservation requests', 'termin-kalender'),
    edit: function(props) {
      const { useState, useEffect } = wp.element;
      const [categories, setCategories] = useState([]);
      const [email, setEmail] = useState('');

      useEffect(() => {
        wp.apiFetch({ path: '/my-termin-reservation/v1/category/' }).then(function(fetchedCategories) {
           setCategories(fetchedCategories);
           if (!props.attributes.category && fetchedCategories.length > 0) {
             props.setAttributes({ category: fetchedCategories[0].kategorie_id });
           }
        });
        wp.apiFetch({ path: '/my-termin-reservation/v1/email/' }).then(data => setEmail(data.email));
      }, []);

      const updateCategory = val => {
        props.setAttributes({ category: val });
      };

      return createElement(
        'div',
        { className: 'my-termin-reservation-block-editor', style: { display: 'flex', flexDirection: 'column', alignItems: 'start', gap: '5px', border: '2px solid #856E53', borderRadius: '8px', padding: '1em'} },
        createElement("img", {
          src: js_wp_php_vars.TER_KAL_PLUGIN_URL + "assets/icon-128x128.png",
          alt: "Termin-Kalender Reservation",
          className: 'my-termin-reservation-block-preview',
          style: { alignSelf: 'center', marginBottom: '10px' }
        }),
        createElement("strong", {}, __('Termin-Kalender Request Form for Reservation or Appointment.', 'termin-kalender')),
        createElement("p", { style: { textAlign: 'left', width: '100%' } }, __('Add your message about what can be reservated or booked and the information you expect before this block. Choose the category to place the request and find your email where a copy of the confirmation email will be sent', 'termin-kalender')),
        createElement(SelectControl, {
          label: __('Category', 'termin-kalender'),
          value: props.attributes.category,
          options: categories.map(category => ({ label: category.kategorie, value: category.kategorie_id })),
          onChange: updateCategory
        }),
        createElement(TextControl, {
          label: __('Email', 'termin-kalender'),
          value: email,
         // style: { display: 'none' } // Make it hidden    // disabled: true
        })
        //createElement('div', {}, __('Email: ', 'termin-kalender'), email)
      );
    },
    save: function(props) {
      return createElement('div', {
      }, `[my-termin-reservation category="${props.attributes.category}" email="${props.attributes.email}"]`);
    }
  });
})(
  window.wp.blocks,
  window.wp.blockEditor,
  window.wp.components,
  window.wp.element
);






//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

// Termin-Kalender EVENT LIST
(function(blocks, blockEditor, components, element) {
  var el = element.createElement;
  var __ = wp.i18n.__;
  var TextControl = components.TextControl;

  blocks.registerBlockType('my-termin-eventlist/shortcode-block', {
    title: __("Termin-Kalender Event List", 'termin-kalender'),
    category: 'widgets',
    icon: {
      src: 'list-view',
      foreground: '#FF3C00'
    },
    attributes: {
      shortcode: {
        type: 'string',
        source: 'attribute',
        selector: 'div',
        attribute: 'data-shortcode'
      },
      // Category attribute
      category: {
        type: 'string',
        default: '',
      }
    },
    example: {
      attributes: {
        shortcode: '[my-termin-eventlist]'
      }
    },
    description: __('Termin-Kalender Event List: A block to display a list of events', 'termin-kalender'),


edit: function(props) {
  var SelectControl = wp.components.SelectControl;
  var withSelect = wp.data.withSelect;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var [categories, setCategories] = useState([]);

  // Fetch categories from the REST API
  useEffect(function() {
    wp.apiFetch({ path: '/my-termin-eventlist/v1/categories/' }).then(function(fetchedCategories) {
      setCategories(fetchedCategories);
      if (!props.attributes.category && fetchedCategories.length > 0) {
        props.setAttributes({ category: fetchedCategories[0].kategorie_id });
      }
    });
  }, []);

  function updateCategory(val) {
    props.setAttributes({ category: val });
  }

  return el(
    'div',
    { className: 'my-termin-eventlist-block-editor', style: { display: 'flex', flexDirection: 'column', gap: '5px', border: '2px solid #856E53', borderRadius: '8px', padding: '1em' } },
    el("img", {
      src: js_wp_php_vars.TER_KAL_PLUGIN_URL + "assets/icon-128x128.png",
      alt: "Termin-Kalender Event List",
      className: 'my-termin-eventlist-block-preview',
      style: { alignSelf: 'center' }
    }),
    el("strong", {}, __('Termin-Kalender Event List:', 'termin-kalender')),
    el("p", { style: { textAlign: 'center', width: '100%' } }, __('Add this block to display a list of upcoming events. Choose a Category to be listed:', 'termin-kalender')),
    // Category select input
    el(SelectControl, {
      label: __('Category', 'termin-kalender'),
      value: props.attributes.category,
      options: categories.map(function(category) {
        return { label: category.kategorie, value: category.kategorie_id };
      }),
      onChange: updateCategory
    })
  );
},

save: function(props) {
  return el('div', {}, '[my-termin-eventlist category="' + props.attributes.category + '"]');
}
  });
})(
  window.wp.blocks,
  window.wp.blockEditor,
  window.wp.components,
  window.wp.element
);

//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

// Termin-Kalender FREE LIST
wp.blocks.registerBlockType('termin-kalender/my-termin-list', {
    title: 'Termin-Kalender Simple List',
    icon: {
      src: 'list-view',
      foreground: '#FF3C00'
    },
    category: 'widgets',
    edit: function(props) {
        return wp.element.createElement('div', null,
            wp.element.createElement('span', { className: 'block-icon', style: { color: '#FF3C00' } },
                wp.element.createElement('i', { className: 'dashicons dashicons-list-view' })
            ),
            ' Termin-Kalender Simple List Block (Get PRO for recurring events and more).'
        );
    },
    save: function() {
        return null; // Server-side rendering
    }
});

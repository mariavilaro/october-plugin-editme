+function ($) { "use strict";
  $(document).render(function() {
    if ($.FroalaEditor) {
      $.FroalaEditor.DEFAULTS = $.extend($.FroalaEditor.DEFAULTS, {
        //Uncomment to disable advanced list types:
        //listAdvancedTypes: false,

        //Uncomment to remove H1 and add H6 to the list of available paragraph formats:
        //paragraphFormat:{N:"Normal",H2:"Heading 2",H3:"Heading 3",H4:"Heading 4",H5:"Heading 5",H6:"Heading 6",PRE:"Code"},
        
        //or add more options below...
      });
    }        
  })
}(window.jQuery);
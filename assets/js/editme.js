/*
 * EditMe plugin
 *
 */

+function ($) { "use strict";

    // EDITME CLASS DEFINITION
    // ============================

    var EditMe = function(element, options) {
        var self       = this
        this.options   = options
        this.$el       = $(element)

        this.originalHtml = null;
        this.requestHandler = this.$el.data('handler')
        this.editMessage = this.$el.data('message')

        this.$controlPanel = $('<div />').addClass('control-editme')
        this.$edit = $('<button />').addClass('editme-edit-button').text('Edit').appendTo(this.$controlPanel)
        this.$save = $('<button />').addClass('editme-save-button').text('Save').hide().appendTo(this.$controlPanel)
        this.$cancel = $('<button />').addClass('editme-cancel-button').text('Cancel').hide().appendTo(this.$controlPanel)

        $(document.body).append(this.$controlPanel)

        this.$el.on('mousemove', function(){
            self.refreshControlPanel()
        })

        this.$controlPanel.on('mouseenter', function(){ self.refreshControlPanel() })

        self.showControlPanel()

        this.$edit.on('click', function(){ self.clickEdit() })
        this.$save.on('click', function(){ self.clickSave() })
        this.$cancel.on('click', function(){ self.clickCancel() })
    }

    EditMe.DEFAULTS = {
        option: 'default'
    }

    EditMe.prototype.clickCancel = function() {
        this.$el.redactor('code.set', this.originalHtml)
        this.$el.redactor('core.destroy')
        this.refreshControlPanel()
        this.$controlPanel.removeClass('active')
        this.$edit.show()
        this.$save.hide()
        this.$cancel.hide()
    }

    EditMe.prototype.clickSave = function() {
        var html = this.$el.redactor('code.get')
        this.$el.redactor('core.destroy')
        this.refreshControlPanel()
        this.$controlPanel.removeClass('active')
        this.$edit.show()
        this.$save.hide()
        this.$cancel.hide()
        $.request(this.requestHandler, {
            data: {
                message: this.editMessage,
                content: html
            }
        })
    }

    EditMe.prototype.clickEdit = function() {
        this.$el.redactor({
            focus: true,
            toolbar: false,
            paragraphize: false,
            linebreaks: true
        })

        this.refreshControlPanel()
        this.$controlPanel.addClass('active')
        this.$save.show()
        this.$cancel.show()
        this.$edit.hide()
        this.originalHtml = this.$el.redactor('code.get')
    }

    EditMe.prototype.hideControlPanel = function() {
        this.$controlPanel.removeClass('visible')
    }

    EditMe.prototype.refreshControlPanel = function() {
        if (!this.$controlPanel.hasClass('visible'))
            this.showControlPanel()

        this.$controlPanel
            .width(this.$el.outerWidth())
            .height(this.$el.outerHeight())
            .css({
                top: this.$el.offset().top,
                left: this.$el.offset().left + this.$el.outerWidth() - this.$controlPanel.outerWidth()
            })
    }

    EditMe.prototype.showControlPanel = function() {
        this.$controlPanel.addClass('visible')
        if (!this.$controlPanel.hasClass('active'))
            this.refreshControlPanel()
    }

    // EDITME PLUGIN DEFINITION
    // ============================

    var old = $.fn.editme

    $.fn.editme = function (option) {
        var args = Array.prototype.slice.call(arguments, 1)
        return this.each(function () {
            var $this   = $(this)
            var data    = $this.data('oc.example')
            var options = $.extend({}, EditMe.DEFAULTS, $this.data(), typeof option == 'object' && option)
            if (!data) $this.data('oc.example', (data = new EditMe(this, options)))
            else if (typeof option == 'string') data[option].apply(data, args)
        })
    }

    $.fn.editme.Constructor = EditMe

    // EDITME NO CONFLICT
    // =================

    $.fn.editme.noConflict = function () {
        $.fn.editme = old
        return this
    }

    // EDITME DATA-API
    // ===============

    $(document).on('mouseenter', '[data-control="editme"]', function() {
        $(this).editme()
    });

    $(window).scroll(function() {
        $(document).find('[data-control="editme"]').each(function(){
            if ($(this).data('oc.example') != undefined)
                $(this).data('oc.example').hideControlPanel()
        })
    });

    $(document).on('click','.redactor-editor',function(e){
        e.preventDefault();
        e.stopPropagation();
        return false;
    });

}(window.jQuery);

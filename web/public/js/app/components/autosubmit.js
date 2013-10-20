define(['$'], function($) {

    var AutoSubmit = function($el, $option) {
        var event;
        $el.submit(function(e){
            e.preventDefault();

            event = new $.Event('autosubmit.validate');
            $el.trigger(event);
            if (event.isDefaultPrevented()) {
                return ;
            }

            event = new $.Event('autosubmit.initdata');
            $el.trigger(event);
            $('button, input[type="submit"]', $el).prop('disabled', true);

            $.ajax({
                type: $el.attr('method').toUpperCase(),
                url: $el.attr('action'),
                data: $el.serialize(),
                cache: false,
                dataType: 'json',
                beforeSend: function(jqXHR, settings) {
                    $(this).find('button[type="submit"]').attr('disabled', true);
                    event = new $.Event('autosubmit.beforesend');
                    $el.trigger(event);
                    if (event.isDefaultPrevented()) {
                        jqXHR.abort();
                    }
                },
                success: function(data, textStatus, jqXHR) {
                    event = new $.Event('autosubmit.success');
                    $el.trigger(event, [data]);
                    if (event.isDefaultPrevented()) {
                        return ;
                    }

                    var reload = data.reload || false;
                    var redirect = data.redirect || false;
                    var status = data.status || false;

                    if(status) {
                        if (redirect) {
                            window.location.href = redirect;
                        } else if (reload) {
                            window.location.reload();
                        }
                    } else {
                        event = new $.Event('autosubmit.failure');
                        $el.trigger(event, [data, textStatus]);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    event = new $.Event('autosubmit.failure');
                    $el.trigger(event, [$.parseJSON(jqXHR.responseText), textStatus]);
                },
                complete: function(jqXHR, textStatus) {
                    event = new $.Event('autosubmit.complete');
                    $el.trigger(event, [$.parseJSON(jqXHR.responseText), textStatus]);
                    $(this).find('button[type="submit"]').attr('disabled', false);
                }
            })
                .always(function() {
                    $('button, input[type="submit"]', $el).prop('disabled', false);
                });

        });
    };

    // Data API
    $.fn.extend({
        autosubmit: function(option) {
            return this.each(function() {
                var $this = $(this),
                    data = $this.data('autosubmit'),
                    options = typeof option == 'object' && option;

                // Setup
                if (!data) {
                    $this.data('autosubmit', (data = new AutoSubmit($this, options)));
                }

                // Method invokation
                if ($.type(option) === 'string') {
                    data[option]();
                }

                return $this;
            });
        }
    });

    $(document).on('focus.autosubmit.data-api', 'mousedown.autosubmit.data-api', 'touchstart.autosubmit.data-api', '[data-provide="autosubmit"]', function(e) {
        var $this = $(this);
        if ($this.data('autosubmit')) { return; }
        $this.autosubmit($this.data());
    });

    return AutoSubmit;
});
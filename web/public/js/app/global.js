define([ '$' ], function($){

    $(document).on('click', '[data-event]', _raiseEvent);

    function _raiseEvent(e) {
        var el = $(this);
        if (!el.prop('disabled')) {

            var params = {},
                attribs = $.grep($(el[0].attributes), (function(el, index) {
                    return /^data-param/.test(el.name);
                }));

            for (var i = 0; i < attribs.length; i++) {
                params[attribs[i].name.replace('data-param-', '')] = attribs[i].value;
            }

            el.trigger(el.data('event'), [e, params]);
        }

        e.preventDefault();
    }

    return {};
});
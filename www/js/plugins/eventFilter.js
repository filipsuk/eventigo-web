(function ($, allTags) {

    const showTime = 300;


    /**
     * Plugin initialization
     * @returns {jQuery}
     */
    $.fn.eventFilter = function () {
        filter();
        this.change(filter);
        return this;
    };


    /**
     * Filter events by checked tags
     */
    function filter() {
        var tags = getCheckedTags(),
            tagsClasses = tags.prefix('.'),
            events = $('#events-list');

        if (tagsClasses.length) {
            // Hide unchecked tags
            events
                .find('.event-box:not(' + tagsClasses.join(',') + ')')
                .fadeOut(showTime);

            // Show up hidden
            var filteredEvents = events.find('.event-box' + tagsClasses.join(','));
            filteredEvents.fadeIn(showTime);

        } else {
            // Show all up
            events
                .children()
                .fadeIn(showTime);

            filteredEvents = events.children();
            tags = allTags;
        }

        // Order events by rate
        orderByRate(filteredEvents, tags);
    }


    /**
     * Return array with checked tags
     * @returns {Array}
     */
    function getCheckedTags() {
        var checked = $('#tags').find('input:checked');

        var tags = [];
        checked.each(function (index, el) {
            tags.push($(el).parent().data('tag'));
        });

        return tags;
    }


    /**
     * Order events by calculated rate
     * @param events
     * @param tags
     */
    function orderByRate(events, tags) {
        var rates = calculateRates(events, tags);
        events.sort(function (a, b) {
            return rates[a.id] > rates[b.id] ? -1 : 1;
        }).appendTo('#events-list');
    }


    /**
     * Order events by calculated rate
     * @param events
     * @param tags
     */
    function calculateRates(events, tags) {
        var eventsRates = [];

        events.each(function (i, el) {
            eventsRates[el.id] = 0;

            var rates = $(el).data('rates');

            // Event rate
            var eventRate = rates.event;

            // Tag rates
            tags.forEach(function (tag) {
                var tagRate = rates[tag] || 0;
                eventsRates[el.id] += eventRate * tagRate;
            });
        });

        return eventsRates;
    }

}(jQuery, allTags));
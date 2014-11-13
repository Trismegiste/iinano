/*
 * all initialization for main content
 */
var social = {
    initLeafletShow: function () {
        $('div[data-social-status-show]').each(function () {

            var item = $(this);
            var origin = {lng: item.data('socialStatusLng'), lat: item.data('socialStatusLat')};

            var map = L.map(item.attr('id'), {
                scrollWheelZoom: false,
                center: origin,
                zoom: item.data('socialStatusZoom')
            });

            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker(origin)
                    .addTo(map)
                    .bindPopup(item.data('socialStatusMessage'))
                    .openPopup();
            // just to be sure this attribute will not be initialized two times
            item.removeAttr('data-social-status-show');
        });
    },
    transformLikeToAjax: function () {
        $(document).on('click', 'a[data-like-ajaxed]', function (event) {
            event.stopPropagation();
            event.preventDefault();
            $.ajax({
                url: this.href,
                type: 'POST'
            }).done(function (response) {
                $(event.currentTarget).replaceWith(response);
                alertify.success("Like updated");
            });
        });
    },
    initConfirmDelete: function () {
        $(document).on('click', 'a[data-social-delete]', function (event) {
            event.stopPropagation();
            event.preventDefault();
            var button = this;
            alertify.confirm("Are you sure you want to delete this entry ?", function (e) {
                if (e) {
                    window.location.href = button.href;
                }
            });
        });
    },
    initReportPopup: function () {
        $(document).on('click', 'a[data-social-report]', function (event) {
            event.stopPropagation();
            event.preventDefault();
            var button = this;
            alertify.prompt("Please give a reason for reporting this content", function (e, str) {
                if ((e) && (str.length > 0)) {
                    window.location.href = button.href;
                }
            });
        });
    }
};
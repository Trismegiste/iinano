/*
 * all initialization for main content
 */
var social = {
    initLeafletShow: function () {
        $('div[data-social-status-show]').each(function () {

            var item = $(this);
            var origin = {lng: item.data('socialStatusLng'), lat: item.data('socialStatusLat')};

            var map = L.map(this, {
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
            alertify.confirm("Are you sure you want to report this content as abusive or spam", function (e) {
                if (e) {
                    window.location.href = button.href;
                }
            });
        });
    },
    initGetCommentaryAjax: function () {
        $(document).on('click', 'a[data-social-commentary-ajax]', function (event) {
            var button = this;
            var pkContent = $(button).data('socialCommentaryAjax');
            event.stopPropagation();
            event.preventDefault();
            $.ajax({
                url: button.href,
                type: 'GET'
            }).done(function (response) {
                $('div[data-social-commentary-lst=' + pkContent + ']').html(response);
            });
        });
    },
    initLightBox: function () {
        $('a[data-lightbox]').lightbox();
        $('a[data-lightbox]').removeAttr('data-lightbox');
        // @todo not very satisfying but it works...
    },
    initFormFocus: function ()
    {
        $('form *[data-form-focus]').first().focus();
    },
    initFluidVideo: function () {
        fluidvids.init({
            selector: ['iframe[data-fluid-video]'],
            players: ['www.youtube.com', 'player.vimeo.com']
        });
        $('iframe[data-fluid-video]').removeAttr('data-fluid-video'); // to prevent double init with more
    },
    initPrivateMessageWarning: function (url) {
        if (window.localStorage != undefined) {  // if no storage, forget about this feature
            $.ajax({
                url: url,
                type: 'GET'
            }).done(function (response) {
                if (response.lastUpdate !== null) {
                    var lastClientValue = localStorage.getItem('privateMessage');
                    if ((lastClientValue == undefined) || (lastClientValue < response.lastUpdate.date)) {
                        $('i[class=icon-mail]').addClass("blink");
                    }
                }
            });
        }
    }
};
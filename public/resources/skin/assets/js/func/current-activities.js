
var searchedParams = new URLSearchParams(window.location.search);

let record_id = '0';

if (searchParams.has('attr-ref')) {
    record_id = searchParams.get('attr-ref');
} else {
    console.log('trainer id not set');
    //window.location.href = "/current-activities";
}
try {
    record_id = parseInt(record_id);
    console.log(record_id);

    if (isNaN(record_id) || record_id < 1) {
        console.log('not a good number');
        //window.location.href = "/current-activities";
    }

} catch (Exception) {
    console.log(Exception);
    //window.location.href = "/current-activities";
}

const global_record_id = record_id;

(function ($, window, document, undefined) {
    "use strict";
    var pluginName = "paginga",
        defaults = {
            itemsPerPage: 3,
            itemsContainer: ".items",
            item: "> div",
            page: 1,
            nextPage: ".nextPage",
            previousPage: ".previousPage",
            firstPage: ".firstPage",
            lastPage: ".lastPage",
            pageNumbers: ".pageNumbers",
            maxPageNumbers: false,
            currentPageClass: "active",
            pager: ".pager",
            autoHidePager: true,
            scrollToTop: {
                offset: 15,
                speed: 100,
            },
            history: false,
            historyHashPrefix: "page-"
        };
    // The actual plugin constructor
    function paginga(element, options) {
        this.element = element;
        this.settings = $.extend(true, {}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this._ready = false;
        this.currentPage = this.settings.page;
        this.items = $(this.element).find(this.settings.itemsContainer + " " + this.settings.item);
        this.totalPages = Math.ceil(this.items.length / this.settings.itemsPerPage);
        if (this.totalPages <= 1) {
            $(this.element).find(this.settings.pager).hide();
        } else {
            this.init();
        }
    }
    $.extend(paginga.prototype, {
        init: function () {
            this.bindEvents();
            this.showPage();
            if (this.settings.history) {
                var plugin = this;
                if (window.location.hash) {
                    var hash = parseInt(window.location.hash.substring(plugin.settings.historyHashPrefix.length + 1), 10);
                    if (hash <= plugin.totalPages && hash > 0) {
                        plugin.currentPage = hash;
                        plugin.showPage.call(plugin);
                    }
                }
                window.addEventListener("popstate", function (event) {
                    plugin.currentPage = event && event.state && event.state.page ? event.state.page : plugin.settings.page;
                    plugin.showPage.call(plugin);
                });
            }
            this._ready = true;
        },
        bindEvents: function () {
            var plugin = this,
                element = $(plugin.element),
                previousElement = element.find(plugin.settings.previousPage),
                nextElement = element.find(plugin.settings.nextPage),
                firstElement = element.find(plugin.settings.firstPage),
                lastElement = element.find(plugin.settings.lastPage);
            previousElement.on("click", function () {
                plugin.showPreviousPage.call(plugin);
            });
            nextElement.on("click", function () {
                plugin.showNextPage.call(plugin);
            });
            firstElement.on("click", function () {
                plugin.showFirstPage.call(plugin);
            });
            lastElement.on("click", function () {
                plugin.showLastPage.call(plugin);
            });
        },
        showPreviousPage: function () {
            this.currentPage--;
            if (this.currentPage <= 1) {
                this.currentPage = 1;
            }
            this.setHistoryUrl();
            this.showPage();
        },
        showNextPage: function () {
            this.currentPage++;
            if (this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages;
            }
            this.setHistoryUrl();
            this.showPage();
        },
        showFirstPage: function () {
            this.currentPage = 1;
            this.setHistoryUrl();
            this.showPage();
        },
        showLastPage: function () {
            this.currentPage = this.totalPages;
            this.setHistoryUrl();
            this.showPage();
        },
        showPage: function () {
            var firstItem = (this.currentPage * this.settings.itemsPerPage) - this.settings.itemsPerPage,
                lastItem = firstItem + this.settings.itemsPerPage;
            $.each(this.items, function (index, item) {
                if (index >= firstItem && index < lastItem) {
                    $(item).show();
                    return true;
                }
                $(item).hide();
            });
            var plugin = this,
                element = $(plugin.element),
                previousElement = element.find(plugin.settings.previousPage),
                nextElement = element.find(plugin.settings.nextPage),
                firstElement = element.find(plugin.settings.firstPage),
                lastElement = element.find(plugin.settings.lastPage);
            if (plugin._ready && plugin.settings.scrollToTop && (element.offset().top - plugin.settings.scrollToTop.offset) < $(window).scrollTop()) {
                $("html, body").animate({
                    scrollTop: (element.offset().top - plugin.settings.scrollToTop.offset)
                }, plugin.settings.scrollToTop.speed);
            }
            if (this.currentPage <= 1) {
                previousElement.addClass("disabled");
                firstElement.addClass("disabled");
            } else {
                previousElement.removeClass("disabled");
                firstElement.removeClass("disabled");
            }
            if (this.currentPage >= this.totalPages) {
                nextElement.addClass("disabled");
                lastElement.addClass("disabled");
            } else {
                nextElement.removeClass("disabled");
                lastElement.removeClass("disabled");
            }
            var pager = element.find(this.settings.pager),
                pageNumbers = pager.find(this.settings.pageNumbers);
            if (pageNumbers) {
                pageNumbers.html("");
                var firstPage = 1;
                var lastPage = this.totalPages;
                if (this.settings.maxPageNumbers) {
                    var offset = Math.ceil((this.settings.maxPageNumbers - 1) / 2);
                    firstPage = Math.max(1, this.currentPage - offset);
                    lastPage = Math.min(this.totalPages, this.currentPage + offset);
                    if (lastPage - firstPage < this.settings.maxPageNumbers - 1) {
                        if (firstPage <= offset) {
                            lastPage = Math.min(this.totalPages, firstPage + this.settings.maxPageNumbers - 1);
                        } else if (lastPage > this.totalPages - offset) {
                            firstPage = Math.max(1, lastPage - this.settings.maxPageNumbers + 1);
                        }
                    }
                }
                for (var pageNumber = firstPage; pageNumber <= lastPage; pageNumber++) {
                    var className = pageNumber == this.currentPage ? this.settings.currentPageClass : "";
                    pageNumbers.append("<a href='javascript:void(0);' data-page='" + pageNumber + "' class='" + className + " buttonify'>" + pageNumber + "</a>");
                    //pageNumbers.append('<span class="page-numbers current" aria-current="page" data-page=' + pageNumber + ' class=' + className + '>'+ pageNumber +'</span>');
                }
                pageNumbers.find("a").on("click", function () {
                    plugin.currentPage = $(this).data("page");
                    plugin.setHistoryUrl.call(plugin);
                    plugin.showPage.call(plugin);
                });
            }
        },
        setHistoryUrl: function () {
            var plugin = this;
            if (plugin._ready && plugin.settings.history && "pushState" in history) {
                history.pushState({
                    page: this.currentPage
                }, null, '#' + plugin.settings.historyHashPrefix + this.currentPage);
            }
        }
    });
    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new paginga(this, options));
            }
        });
    };
})(jQuery, window, document);
get_services();

function truncate(input, strlength = 5) {
    if (input.length > strlength) {
        return input.substring(0, strlength) + '...';
    }
    return input;
};


function get_services() {
    var item_divs = '';
    var b = {
        'filter_record_id': global_record_id,
        'token': localStorage.token,
    };
    $.ajax({
        type: "post",
        url: "/get-event-list-fe-api",
        data: b,
        dataType: "json",
        beforeSend: function () {
            $('.loading-div').removeClass('hide-me');
            $('.results-div').addClass('hide-me');
            $(".ajaxDataloader").html('<p class="modal-title"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Please wait...</p>');
            $(".ajaxDataloader").css("visibility", "visible");
            $(".removeDataMessages").html("");
            $(".removeDataMessages").css("visibility", "hidden");
            $(".clientData-btn").prop('disabled', true).html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
        },
        success: function (e) {
            if (e.status == 'ok') {
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-success alert-dismissible" role="alert"><a  class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-check"></span> </strong>' + e.messages + "</div>");
                var data_export = e.data;
                let event_type_desc = e.event_type_desc;
                //console.log(event_type_desc);
                //console.log(Object.keys(event_type_desc).length);
                if (event_type_desc != undefined) {
                    if (Object.keys(event_type_desc).length > 1) {
                        $('#event_type_name').html(event_type_desc.service_name);
                        $('#event_type_desc').html(event_type_desc.service_description);
                        $('#side-img').attr('src', event_type_desc.desc_picture);
                        $('#desc-div').removeClass('hide-me');
                        $('.event-title-major').html(event_type_desc.service_name);
                    }
                }
                var item_divs = '';
                console.log('item_divs', item_divs);

                $.each(data_export.data, function (key, val) {

                    var services = '| ';
                    var session_title = '';
                    var trainer_data = val.type_of_trainings;
                    
                    let event_dates = val.start_date + ' => ' + val.end_date;
                    $.each(trainer_data, function (key, val) {
                        //console.log(val);
                        services += val.description + ' | ';

                        // var i;
                        // for (i in val) {
                        //     if (val.hasOwnProperty(i)) {
                        //         services+=val.description+' | ';
                        //     }
                        // }
                    })
                    var tag_line = val.tag_line;
                    services = truncate(services, 25);
                    tag_line = truncate(tag_line, 80);
                    session_title = truncate(val.session_title, 40);
                    var service_pill = 'Training activities: <span class="badge badge-light">' + services + '</span>';
                    //item_divs += '<div class="classess-block col-lg-4 col-md-6 col-sm-12">' +
                    //     '<div class="inner-box elem-paro">' +
                    //     '<div class="image">' +
                    //     '<a href="/current-activities-view?ref=' + val.record_id + '"><img src="' + val.thumbnail + '" alt="" /></a>' +
                    //     '<div class="icon-box hide-me">' +
                    //     '<span class="icon flaticon-tools-and-utensils-1"></span>' +
                    //     '</div>' +
                    //     '</div>' +
                    //     '<div class="lower-content">' +
                    //     '<div class="title-x hide-me">' + service_pill + '</div>' +
                    //     '<h4><a href="/current-activities-view?ref=' + val.record_id + '">' + session_title + '</a></h4>' +
                    //     '<div class="text"><span class="boldify">Max Attendees:</span>' + val.max_attendee + '<br><span class="boldify">Chaperone Allowed:</span>' + val.chaperone_allowed + '</div>' +
                    //     '<a class="read-more" href="/current-activities-view?ref=' + val.record_id + '">Book Now</a>' +
                    //     '</div>' +
                    //     '</div>' +
                    //     '</div>';
                    //console.log(val.record_id);
                    item_divs +='<div class="col-lg-4 col-md-6">' +
                        '		 <div class="single-services-item bg-1 style-three d-flex align-items-end" style="background-image:url(' + val.thumbnail + ')">' +
                        '			 <div class="services-contents">' +
                        '				<h3>' +
                        '					 <a href="/current-activities-view?ref=' + val.record_id + '">' + session_title + '</a>' +
                        '				</h3>' +
                        '				 <ul>' +
                        '					 <li> Date: ' + event_dates + '</li>' +
                        '				</ul>' +
                        '			 </div>' +
                        '		 </div>' +
                        '</div>';
                    console.log('item_divs', item_divs);
                });

                if (item_divs == '') {
                    item_divs = '<h4>No events are available for this category.</h4>';
                }
                $('#results-div').html(item_divs);
                $(".paginate").paginga({
                    // use default options

                    itemsPerPage: 6,
                });
                $('.results-div').css('width', '100%');
            } else {
                item_divs = '<h4>No events are available for this category.</h4>';
                $('#results-div').html(item_divs);
                $('.pager').addClass('hide-me');
                $(".ajaxDataloader").html("");
                $(".ajaxDataloader").css("visibility", "hidden");
                $(".removeDataMessages").html('<div class="alert alert-warning alert-dismissible" role="alert"><a class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></a><strong> <span class="fa-fa-cancel"></span> </strong>' + e.messages + "</div>")
            }
        },
        complete: function () {
            $('.loading-div').addClass('hide-me');
            $('.results-div').removeClass('hide-me');
            $(".ajaxDataloader").html("");
            $(".ajaxDataloader").css("visibility", "hidden");
            $(".removeDataMessages").css("visibility", "visible");
            $(".clientData-btn").prop('disabled', false).html('Log In');
            $(".clientData-btn-ico").prop('disabled', false).html('<i class="lni lni-arrow-right"></i>');
        }
    });
}
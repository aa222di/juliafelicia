/*

 This script is used to hide the header widget and add a small clickable icon to the menu which toggles the display
 for the searchwidget

 */

( function() {
    var searchWidget, widgetContainer, mainMenu, searchIcon;
    searchWidget = jQuery('#woocommerce_product_search-2');

    if(searchWidget.length) {
        widgetContainer = searchWidget.closest('.header-widget-region');
        jQuery(widgetContainer).hide();
        mainMenu = jQuery('#site-navigation').find('.nav-menu');

        searchIcon = jQuery('<li class="search-icon menu-item"></li>').appendTo(mainMenu);
           jQuery(searchIcon).click(function () {
               jQuery(widgetContainer).slideToggle();
           });
    }
} )();
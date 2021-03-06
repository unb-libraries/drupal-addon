(function ($, Drupal, drupalSettings) {

    Drupal.behaviors.multiselect = {

        config: {
            enableClickableOptGroups: true,
            enableCollapsibleOptGroups: true,
            collapseOptGroupsByDefault: false,
            disableIfEmpty: true,
            buttonWidth: "100%",
            buttonClass: "",
            nonSelectedText: "None",
            allSelectedText: "All",
            numberDisplayed: 1,
            selectedClass: "",
            includeSelectAllOption: true,
            selectAllText: "All",
            selectAllValue: "All",
            selectAllNumber: false,
            enableFiltering: false,
            enableCaseInsensitiveFiltering: true,
            enableFullValueFiltering: false,
            filterBehavior: "text",
            filterPlaceholder: "Search",
            includeResetOption: false,
            resetText: "Reset",
        },

        selectElement: undefined,

        attach: function attach(context, settings) {
            for (let property in settings.bootstrapMultiselect) {
                ms.config[property] = settings.bootstrapMultiselect[property];
            }

            $('select.multiselect').each(function(index, element) {
                ms.initialize($(element));
            });
        },

        initialize: function initialize(selectElement) {
            ms.selectElement = selectElement;
            ms.selectElement.multiselect(ms.config);
            ms.setButtonSize();
            if (ms.areNoOptionsSelected()) {
                ms.selectAll();
            }
        },

        setButtonSize: function setButtonSize() {
            let width = ms.getWidth();
            let buttonContainer = ms.selectElement[0].parentNode.getElementsByClassName('btn-group')[0];
            buttonContainer.style.width = width + 'px';
        },

        addListener: function addListener(type, listener) {
            ms.config[type] = listener;
            ms.selectElement.multiselect('setOptions', ms.config);
        },

        areNoOptionsSelected: function areNoneSelected() {
            return ms.selectedOptions().length === 0;
        },

        selectedOptions: function selectedOptions() {
            return ms.selectElement[0].selectedOptions;
        },

        selectAll: function () {
            ms.selectElement.multiselect('selectAll', false);
            ms.selectElement.multiselect('updateButtonText');
        },

        getWidth: function getWidth() {
            return ms.getOptionsList().width();
        },

        getOptionsList: function getOptionsList() {
            return $(ms.selectElement[0].parentNode.getElementsByTagName('ul')[0]);
        }
    };

    let ms = Drupal.behaviors.multiselect;

})(jQuery, Drupal, drupalSettings);
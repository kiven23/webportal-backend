$.fn.select2.amd.define("CustomSelectionAdapter", [
  "select2/utils",
  "select2/selection/multiple",
  "select2/selection/placeholder",
  "select2/selection/eventRelay",
  "select2/selection/single",
],
function(Utils, MultipleSelection, Placeholder, EventRelay, SingleSelection) {

  // Decorates MultipleSelection with Placeholder
  let adapter = Utils.Decorate(MultipleSelection, Placeholder);
  // Decorates adapter with EventRelay - ensures events will continue to fire
  // e.g. selected, changed
  adapter = Utils.Decorate(adapter, EventRelay);

  adapter.prototype.render = function() {
    // Use selection-box from SingleSelection adapter
    // This implementation overrides the default implementation
    let $selection = SingleSelection.prototype.render.call(this);
    return $selection;
  };

  adapter.prototype.update = function(data) {
    // copy and modify SingleSelection adapter
    this.clear();

    let $rendered = this.$selection.find('.select2-selection__rendered');
    let noItemsSelected = data.length === 0;
    let formatted = "";

    if (noItemsSelected) {
      formatted = this.options.get("placeholder") || "";
    } else {
      let itemsData = {
        selected: data || [],
        all: this.$element.find("option") || []
      };
      // Pass selected and all items to display method
      // which calls templateSelection
      formatted = this.display(itemsData, $rendered);
    }

    $rendered.empty().append(formatted);
    $rendered.prop('title', formatted);
  };

  return adapter;
});

$.fn.select2.amd.define("CustomDropdownAdapter", [
  "select2/utils",
  "select2/dropdown",
  "select2/dropdown/attachBody",
  "select2/dropdown/attachContainer",
  "select2/dropdown/search",
  "select2/dropdown/minimumResultsForSearch",
  "select2/dropdown/closeOnSelect",
],
function(Utils, Dropdown, AttachBody, AttachContainer, Search, MinimumResultsForSearch, CloseOnSelect) {

  // Decorate Dropdown with Search functionalities
  let dropdownWithSearch = Utils.Decorate(Dropdown, Search);
  dropdownWithSearch.prototype.render = function() {
    // Copy and modify default search render method
    var $rendered = Dropdown.prototype.render.call(this);
    // Add ability for a placeholder in the search box
    let placeholder = this.options.get("placeholderForSearch") || "";
    var $search = $(
      '<span class="select2-search select2-search--dropdown">' +
      '<input class="select2-search__field" placeholder="' + placeholder + '" type="search"' +
      ' tabindex="-1" autocomplete="off" autocorrect="off" autocapitalize="off"' +
      ' spellcheck="false" role="textbox" />' +
      '</span>'
    );

    this.$searchContainer = $search;
    this.$search = $search.find('input');

    $rendered.prepend($search);
    return $rendered;
  };

  // Decorate the dropdown+search with necessary containers
  let adapter = Utils.Decorate(dropdownWithSearch, AttachContainer);
  adapter = Utils.Decorate(adapter, AttachBody);

  return adapter;
});
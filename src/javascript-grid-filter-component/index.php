<?php
$key = "Filter Component";
$pageTitle = "JavaScript Grid Filtering";
$pageDescription = "Describes how to implement customer filters for ag-Grid";
$pageKeyboards = "JavaScript Grid Filtering";
$pageGroup = "components";
include '../documentation-main/documentation_header.php';
?>

<h2 id="filter-component">Filter Component</h2>


<p>
    Filter components allow you to add your own filter types to ag-Grid. Use this when the provided
    filters do not meet your requirements.
</p>

<p>
    To provide a custom filter, instead of providing a string for the filter in
    the column definition, provide a Filter Component in the form of a function. ag-Grid will call 'new'
    on this function and treat the generated class instance as a filter component. A filter component class
    can be any function / class that implements the following interface:
</p>

<pre>interface IFilterComp {

    <span class="codeComment">// mandatory methods</span>

    <span class="codeComment">// The init(params) method is called on the filter once. See below for details on the parameters.</span>
    init(params: IFilterParams): void;

    <span class="codeComment">// Returns the GUI for this filter. The GUI can be a) a string of html or b) a DOM element or node.</span>
    getGui(): any;

    <span class="codeComment">// The grid calls this to know if the filter icon in the header should be shown. Return true to show.</span>
    isFilterActive(): boolean;

    <span class="codeComment">// The grid will ask each active filter, in turn, whether each row in the grid passes. If any
    // filter fails, then the row will be excluded from the final set. The method is provided a
    // params object with attributes node (the rodNode the grid creates that wraps the data) and data
    // (the data object that you provided to the grid for that row).</span>
    doesFilterPass(params: IDoesFilterPassParams): boolean;

    <span class="codeComment">// Gets the filter state for storing</span>
    getModel(): any;

    <span class="codeComment">// Restores the filter state. Called either as a result of user calling
    // <i>gridApi.setSortModel</i> OR the floating filter changed (only if using floating filters).</span>
    setModel(model: any): void;

    <span class="codeComment">// optional methods</span>

    <span class="codeComment">// Gets called every time the popup is shown, after the gui returned in
    // getGui is attached to the DOM. If the filter popup is closed and reopened, this method is called
    // each time the filter is shown. This is useful for any logic that requires attachment before executing,
    // such as putting focus on a particular DOM element. The params has one callback method 'hidePopup',
    // which you can call at any later point to hide the popup - good if you have an 'Apply' button and
    // you want to hide the popup after it is pressed.</span>
    afterGuiAttached?(params?: {hidePopup?: Function}): void;

    <span class="codeComment">// Gets called when new rows are inserted into the grid. If the filter needs to change it's state
    // after rows are loaded, it can do it here. For example the set filters uses this to update the list of
    // available values to select from (eg 'Ireland', 'UK' etc for Country filter).</span>
    onNewRowsLoaded?(): void;

    <span class="codeComment">// Gets called when the Column is destroyed. If your custom filter needs to do
    // any resource cleaning up, do it here. A filter is NOT destroyed when it is
    // made 'not visible', as the gui is kept to be shown again if the user selects
    // that filter again. The filter is destroyed when the column it is associated with is destroyed,
    // either new columns are set into the grid, or the grid itself is destroyed.</span>
    destroy?(): void;

    <span class="codeComment">// Only used in conjunction with floating filters.
    //
    // If floating filters are turned on for the grid, but you have no floating filter
    // configured for this column, then the grid will check for this method. If this
    // method exists, then the grid will provide a read only floating filter for you
    // and display the results of this method. For example, if your filter is a simple
    // filter with one string input value, you could just return the simple string
    // value here.
    //
    // If you are implementing a floating filter for your filter, then leave this method out.</span>
    getModelAsString?(model:any): string;

    <span class="codeComment">// Only used in conjunction with floating filters.
    //
    // When a floating filter changes and calls the <i>onFloatingFilterChanged(change)</i> callback then:
    //   a) <i>filter.onFloatingFilterChanged(change)</i> gets called if it exists.
    //   ELSE
    //   b) <i>filter.setModel(model)</i> gets called.
    //
    // If <i>setModal(modal)</i> is used, then the change object you pass should be the model
    // object the filter is expecting. The grid will then continue and update the grids rows
    // based on the new filter state.
    //
    // If <i>onFloatingFilterChanged(change)</i> is used, then the change object you pass
    // can be anything you like, as long as it's expected by your filter. The grid will
    // update the grid rows for you, you will need to do this yourself by calling filter
    // <i>filterChangedCallback()</i> if you need. Use this if your need to do more in your floating
    // than <i>setModel()</i> does. For example ag-Grid out of the box filter components use
    // this to also consider logic for the Apply button (as if Apply button is active, then
    // the filter does not call <i>filterChangedCallback()</i>. </span>
    onFloatingFilterChanged?(change:any): void;
}</pre>

<h4 id="ifilter-params">IFilterParams</h4>

<p>
    The method init(params) takes a params object with the items listed below. If the user provides
    params via the <i>colDef.filterParams</i> attribute, these will be additionally added to the
    params object, overriding items of the same name if a name clash exists.
</p>

<pre>interface IFilterParams {

    <span class="codeComment">// The column this filter is for</span>
    column: Column;

    <span class="codeComment">// The column definition for the column</span>
    colDef: ColDef;

    <span class="codeComment">// The row model, helpful for looking up data values if needed.
    // If the filter needs to know which rows are a) in the table b) currently
    // visible (ie not already filtered), c) what groups d) what order - all of this
    // can be read from the rowModel.</span>
    rowModel: IRowModel;

    <span class="codeComment">// A function callback, to be called, when the filter changes,
    // to inform the grid to filter the data. The grid will respond by filtering the data.</span>
    filterChangedCallback: ()=> void;

    <span class="codeComment">// A function callback, to be optionally called, when the filter changes,
    // but before 'Apply' is pressed. The grid does nothing except call
    // gridOptions.filterModified(). This is useful if you are making use of
    // an 'Apply' button and want to inform the user the filters are not
    // longer in sync with the data (until you press 'Apply').</span>
    filterModifiedCallback: ()=> void;

    <span class="codeComment">// A function callback, call with a node to be given the value for that
    // filters column for that node. The callback takes care of selecting the right colDef
    // and deciding whether to use valueGetter or field etc. This is useful in, for example,
    // creating an Excel style filer, where the filter needs to lookup available values to
    // allow the user to select from.</span>
    valueGetter: (rowNode: RowNode) => any;

    <span class="codeComment">// A function callback, call with a node to be told whether
    // the node passes all filters except the current filter. This is useful if you want
    // to only present to the user values that this filter can filter given the status
    // of the other filters. The set filter uses this to remove from the list, items that
    // are no longer available due to the state of other filters (like Excel type filtering). </span>
    doesRowPassOtherFilter: (rowNode: RowNode) => boolean;

    <span class="codeComment">// The context for this grid. See section on <a href="../javascript-grid-context/">Context</a></span>
    context: any;

    <span class="codeComment">// If the grid options angularCompileFilters is set to true, then a new child
    // scope is created for each column filter and provided here. Just ignore this if
    // you are not using Angular 1</span>
    $scope: any;

     <span class="codeComment">// The grid API</span>
    api: any;
}</pre>

<h4 id="i-does-filter-pass-params">IDoesFilterPassParams</h4>

<p>
    The method doesFilterPass(params) takes the following as a parameter:
</p>

<pre>interface IDoesFilterPassParams {

    <span class="codeComment">// The row node in question</span>
    node: RowNode;

    <span class="codeComment">// The data part of the row node in question</span>
    data: any
}</pre>

<h3>Associating Floating Filter</h3>

<p>
    If you create your own filter you have two options to get its floating filters working for that filter:
<ol>
    <li>
        You can <a href="../javascript-grid-floating-filter-component/">create your own floating filter</a>.
    </li>
    <li>
        You can implement the method <i>getModelAsString()</i> in your custom filter. If you implement this method and you don't
        provide a custom floating filter, ag-Grid will automatically provide a read-only version of a floating filter
    </li>
</ol>
If you don't provide any of these two options for your custom filter, the display area for the floating filter
will be empty.
</p>

<h3 id="custom-filter-example">Custom Filter Example</h3>

<p>
    The example below shows two custom filters. The first is on the Athlete column and the
    second is on the Year column.
</p>

<show-example example="exampleCustomFilter"></show-example>

<?php if (isFrameworkAngular2()) { ?>
    <?php include './angular.php';?>
<?php } ?>

<?php if (isFrameworkReact()) { ?>
    <?php include './react.php';?>
<?php } ?>

<?php if (isFrameworkVue()) { ?>
    <?php include './vuejs.php';?>
<?php } ?>

<?php if (isFrameworkAurelia()) { ?>
    <?php include './aurelia.php';?>
<?php } ?>

<?php include '../documentation-main/documentation_footer.php';?>

<?php
$key = "Testing";
$pageTitle = "ag-Grid Testing";
$pageDescription = "ag-Grid End to End (e2e) Testing";
$pageKeyboards = "ag-Grid e2e testing";
$pageGroup = "feature";
include '../documentation-main/documentation_header.php';
?>

<div>

    <h2 id="e2e-testing">End to End (e2e) Testing</h2>

    <p>
        We will walk through how you can use <code>Protractor</code> and <code>Jasmine</code> to do End to End (e2e) testing
        with ag-Grid in this section.
    </p>

    <p>These recipes below are suggestions - there are many ways to do End to End testing, what we document below is
        what we use here at ag-Grid.</p>

    <p>We do not document how to use either <code>Protractor</code> and <code>Jasmine</code> in depth here - please see either the
        <a href="http://www.protractortest.org/#/" target="_blank">Protractor</a> or
        <a href="https://jasmine.github.io/" target="_blank">Jasmine</a> for information around either of these tools.
        We only describe how these tools can be used to test ag-Grid below.</p>

    <note>End to End testing can be fragile. If you change something trivial upstream it can have a big impact on an End to End test,
    so we recommend using End to End tests in conjuction with unit tests. It's often easier to find and fix a problem at the unit
    testing stage than it is in the end to end stage.</note>

    <h3>Testing Dependencies</h3>

<pre>
npm install protractor webdriver-manager --save-dev

<span class="codeComment">// optional dependencies - if you're using TypeScript </span>
npm install @types/jasmine @types/selenium-webdriver --save-dev
</pre>

    <p>Note you can install <code>protractor</code> and <code>webdriver-manager</code> globally if you'd prefer,
        which would allow for shorter commands when executing either of these tools.</p>

    <p>We now need to update the webdriver:</p>

    <pre>./node_modules/.bin/webdriver-manager update</pre>

    <p>This can be added to your package.json for easier packaging and repeatability:</p>

<pre>
"scripts": {
    "postinstall": "webdriver-manager update"
}
</pre>

    <h4>Selenium Server</h4>

    <p>You can either start & stop your tests in a script, or start the Selenium server seperately, running your tests against it.</p>

    <p>Remember that the interaction between your tests and the browser is as follows:</p>

    <pre>[Test Scripts] &lt; ------------ &gt; [Selenium Server] &lt; ------------ &gt; [Browser Drivers]</pre>

    <p>We'll run the server separately to begin with here:</p>

    <pre>./node_modules/.bin/webdriver-manager start</pre>

    <h3>Sample Configuration</h3>

    <pre>
<span class="codeComment">// conf.js</span>
exports.config = {
    framework: 'jasmine',
    specs: ['spec.js']
}
</pre>

    <pre>Here we specify the <code>Jasmine</code> testing framework as well as our test to run.</pre>

    <h3>Sample Test</h3>

    <note>If you're testing against a non-Angular application then you need to tell <code>Protractor</code>
        not to wait for Angular by adding this to either your configuration or your tests: <code>browser.ignoreSynchronization = true;</code></note>

    <p>For this test we'll testing a simple JavaScript based grid which can be found at the <a
                href="../best-javascript-data-grid/example-js.html" target="_blank">Getting Started -> JavaScript</a> Section:</p>

    <img src="../images/example-js.png" style="width: 100%;padding-bottom: 10px">

    <h4>Checking Headers</h4>

    <p>Let's start off by checking the headers are the ones we're expecting. We can do this by retrieving all <code>div</code>'s that
        have the <code>ag-header-cell-text</code> class:
    </p>

<pre>
<span class="codeComment">// spec.js</span>
describe('ag-Grid Protractor Test', function () {
    // not an angular application
    browser.ignoreSynchronization = true;

    beforeEach(() => {
        browser.get("https://www.ag-grid.com/best-javascript-data-grid/example-js.html");
    });

    it('should have expected column headers', () => {
        element.all(by.css(".ag-header-cell-text"))
            .map(function (header) {
                return header.getText()
            }).then(function (headers) {
                expect(headers).toEqual(['Make', 'Model', 'Price']);
            });
    });
});
</pre>

    <p>We can now run our test by executing the following command:</p>

<pre>
./node_modules/.bin/protractor conf.js

<span class="codeComment">// or if protractor is installed globally</span>
protractor conf.js
</pre>

    <h4>Checking Grid Data</h4>

    <p>We can match grid data by looking for rows by matching <code>div[row="&lt;row id&gt;"]</code> and then column
        values within these rows by looking for <code>div</code>'s with a class of <code>.ag-cell-value</code>:</p>

<pre>
it('first row should have expected grid data', () => {
    element.all(by.css('div[row="0"] div.ag-cell-value'))
        .map(function (cell) {
            return cell.getText();
        })
        .then(function (cellValues) {
            expect(cellValues).toEqual(["Toyota", "Celica", "35000"]);
        });
});
</pre>

    <p>We can add this to <code>spec.js</code> and run the tests as before.</p>


    <h3>ag-Grid Testing Utilities</h3>
    
    <note>These utilities scripts should still be considered beta and are subject to change. Please provide feedback to 
    the <a href="https://github.com/seanlandsman/ag-grid-testing" target="_blank">GitHub</a> repository.</note>

    <p>Here at ag-Grid we use a number of utility functions that make it easier for us to test ag-Grid functionality.</p>

    <p>The utilities can be installed & imported as follows:</p>

    <p>Installing:</p>
<pre>
npm install ag-grid-testing --save-dev
</pre>

    <p>Importing:</p>

    <pre>let ag_grid_utils = require("ag-grid-testing");
    </pre>

    <h4>verifyRowDataMatchesGridData</h4>

    <p>Compares Grid data to provided data. The order of the data provided should correspond to the order within the grid.
    The property names should correspond to the <code>colId</code>'s of the columns.</p>

    <pre>
ag_grid_utils.verifyRowDataMatchesGridData(
    [
        <span class="codeComment">// first row</span>
        {
            "name": "Amelia Braxton",
            "proficiency": "42%",
            "country": "Germany",
            "mobile": "+960 018 686 075",
            "landline": "+743 1027 698 318"
        },
        <span class="codeComment">// more rows...</span>
    ]
);
    </pre>

    <h4>verifyCellContentAttributesContains</h4>
    <p>Userful when there is an array of data within a cell, each of which is witing an attribute (for example an image).</p>

    <pre>ag_grid_utils.verifyCellContentAttributesContains(1, "3", "src", ['android', 'mac', 'css'], "img");</pre>

    <h4>allElementsTextMatch</h4>

    <p>Verifies that all elements text (ie the cell value) matches the provided data. Usf</p>

    <pre>
ag_grid_utils.allElementsTextMatch(by.css(".ag-header-cell-text"),
    ['#', 'Name', 'Country', 'Skills', 'Proficiency', 'Mobile', 'Land-line']
);
    </pre>

    <h4>clickOnHeader</h4>

    <p>Clicks on a header with the provided <code>headerName</code>.</p>
    <pre>ag_grid_utils.clickOnHeader("Name");</pre>

    <h4>getLocatorForCell</h4>

    <p>Provides a CSS <code>Locator</code> for a grid cell, by row & id and optionally a further CSS selector.</p>

    <pre>
ag_grid_utils.getLocatorForCell(0, "make")
ag_grid_utils.getLocatorForCell(0, "make", "div.myClass)
</pre>

    <h4>getCellContentsAsText</h4>

    <p>Returns the cell value (as text) for by row & id and optionally a further CSS selector.</p>

<pre>
ag_grid_utils.getCellContentsAsText(0, "make")
             .then(function(cellValue) {
                // do something with cellValue
             });

ag_grid_utils.getCellContentsAsText(0, "make", "div.myClass)
             .then(function(cellValue) {
                // do something with cellValue
             });
</pre>
</div>

<?php include '../documentation-main/documentation_footer.php';?>

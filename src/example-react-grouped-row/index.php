<?php
$key = "React Group Row";
$pageTitle = "ag-Grid React Grouped Row Renderer";
$pageDescription = "ag-Grid React Grouped Row Renderer Example";
$pageKeyboards = "ag-Grid react grouped row component";
$pageGroup = "examples";
include '../documentation-main/documentation_header.php';
?>

<div>

    <h2>Group Row Inner Renderer</h2>
    <p>A Group Row Inner Renderer Example</p>

    <show-complex-example example="../react-examples/examples/?fromDocs&example=group-row"
                          sources="{
                            [
                                { root: '/react-examples/examples/src/groupedRowInnerRendererExample/', files: 'GroupedRowInnerRendererComponentExample.jsx,MedalRenderer.jsx' }
                            ]
                          }"
                          exampleHeight="525px">
    </show-complex-example>
</div>

<?php include '../documentation-main/documentation_footer.php';?>

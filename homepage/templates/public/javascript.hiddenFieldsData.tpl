<script>

    if(typeof window.hiddenFieldsData == 'undefined') {
        window.hiddenFieldsData = new Array();
    }
    window.hiddenFieldsData.push({$element->getHiddenFieldsData()|json_encode});

</script>
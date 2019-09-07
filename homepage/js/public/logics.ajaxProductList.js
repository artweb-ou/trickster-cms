// window.ajaxProductsListLogics = new function() {
//     var self = this;
//     var initComponents = function() {
//         var elementsList;
//         var elements;
//         var productId;
//         var productsListSets;
//         var productsListSetsArray = [];
//         elementsList = document.querySelector('.products_list');
//         //  data-products-list="quickview, basket, pager"
//         if (elementsList && elementsList.dataset.productsList && trimSpaces(elementsList.dataset.productsList) !=='') {
//             // todo: now only 'product_detailed' test for, need any sign for each product which could be call by Ajax
//             productsListSets = trimSpaces(elementsList.dataset.productsList);
//             productsListSetsArray = productsListSets.split(',');
//             if (productsListSetsArray.indexOf('quickview') || productsListSetsArray.indexOf('basket')) {
//
//                 elements = elementsList.querySelectorAll("[class*='productid_']");
//
//                 for (var i = 0; i < elements.length; i++) {
//                     productId = parseInt(elements[i].className.split('productid_')[1], 10);
//                     new AjaxProductsListComponent(elements[i], productId, productsListSetsArray);
//                 }
//             }
//
//             if (productsListSetsArray.indexOf('pager')) {listElementId, selectedPageId, productsListSetsArray, data
//                 new AjaxProductShowListComponent(elementsList, productsListSetsArray);
//             }
//         }
//     };
//
//     var trimSpaces = function(string) {
//         return string.replace(/^\s+/, '').replace(/\s+$/, ''); // trim
//     };
//
// /*    var receiveData = function(
//         responseStatus, requestName, responseData, callBack) {
//         if (responseStatus === 'success' && responseData) {
//             callBack(responseData);
//         } else {
//             controller.fireEvent('ajaxSearchResultsFailure', responseData);
//         }
//     };
//     this.sendQuery = function(
//         callBack, query, types, apiMode, resultsLimit, language, filters) {
//         var url = '/ajaxProductsList/mode:' + apiMode + '/types:' + types + '/totals:' +
//             1 + '/query:' + query;
//         if (typeof resultsLimit !== 'undefined') {
//             url += '/resultsLimit:' + parseInt(resultsLimit, 10);
//         }
//         if (typeof language !== 'undefined') {
//             url += '/language:' + language;
//         }
//         if (typeof filters !== 'undefined') {
//             url += '/filters:' + filters;
//         }
//         url += '/';
//         var request = new JsonRequest(url,
//             function(responseStatus, requestName, responseData) {
//                 return receiveData(responseStatus, requestName, responseData,
//                     callBack);
//             }, 'ajaxProductsList');
//         request.send();
//     };
//     */
//
//     controller.addListener('initDom', initComponents);
//
// };
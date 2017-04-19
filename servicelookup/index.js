var fs = require("fs");
var argvLength = process.argv.length > 2 ? process.argv.length - 2 : 0;

// function readLines(input, func) {
//     var remaining = '';

//     input.on('data', function(data) {
//         func(data);
//     });

//     input.on('end', function() {
//         if (remaining.length > 0) {
//             func(remaining);
//         }
//     });
// }

for (var i=0; i<argvLength; i++) {
    var file2read = process.argv[2+i];

    var array = fs.readFileSync(file2read).toString().split("\n");
    array.forEach(function(element) {
        // console.log(element);

        var indexOfStr = element.indexOf("ServiceChannel.Return<");
        var indexOfStrEnd = element.indexOf("(", indexOfStr);
        var indexOfEndEnd = element.indexOf(";", indexOfStr);

        if (indexOfStr > 0 && indexOfStrEnd > 0) {
            // console.log(indexOfStr, indexOfStrEnd, element.substr(indexOfStr, indexOfStrEnd-indexOfStr));
            var text1 = element.substr(indexOfStr, indexOfStrEnd-indexOfStr),
                text2 = element.substr(indexOfStr, indexOfEndEnd-indexOfStr);
            console.log(text1 + '|' + text2);
        }
        
    }, this);

    // var input = fs.createReadStream(file2read);
    // readLines(input, function(text) {
    //     console.log(text);
    // });
}

console.log("done");


//   C:\Users\nhussein\Source\Repos\FxOnline\Web\BillPayment\Billpayment.Bll\BP\BillOrderManager.cs(24):			List<BillInfo> searchedList = ServiceChannel.Return<IBillPaymentService, List<BillInfo>>(service => service.SearchOrders(searchOptions.ReceivingAgentIds, searchOptions.TransNo, searchOptions.DateFrom, searchOptions.DateTo, searchOptions.Status, transType, searchOptions.PagingOptions.RowNumber, searchOptions.PagingOptions.PageSize, searchOptions.PhoneNumber, Convert.ToInt32(searchOptions.EnteredByIds), searchOptions.Branch, searchOptions.BillerLocID));
//   C:\Users\nhussein\Source\Repos\FxOnline\Web\BillPayment\Billpayment.Bll\BP\BillOrderManager.cs(43):			BillCommissionInfo billCommissionList = ServiceChannel.Return<IBillPaymentService, BillCommissionInfo>(service => service.GetAgentCommissionReport(agentId, dateStart, dateEnd, billerLocId, country, countryId, agentLocId, agentLocationList));
//   C:\Users\nhussein\Source\Repos\FxOnline\Web\BillPayment\Billpayment.Bll\BP\BillPaymentManager.cs(26):			List<BillPaymentInfo> info = ServiceChannel.Return<IBillPaymentService, List<BillPaymentInfo>>(service => service.TransactionTypeList());

using System;
using System.Collections.Generic;
using System.Linq;
using System.ServiceModel;
using System.Text;

namespace CES.SL.ISO8583Service.Walmart.Contract
{
    /// <summary>
    /// 
    /// </summary>
    [ServiceContract]
    public interface IISO8583WalmartService
    {
        #region sending

        /// <summary>
        /// from RIa to Walmart to ask for ticket number to included as a barcode in the sending receipt
        /// </summary>
        /// <param name="orderID"></param> 
        /// <param name="calledFrom"></param>
        /// <returns></returns>
        [OperationContract]
        WalmartRequestResult OrderRequest(int orderID, int modifyUserId, int loginId, string calledFrom);

        /// <summary>
        /// from Walmart to Ria to confirm whether they can collect the money from the customer
        /// </summary>
        /// <param name="orderID"></param>
        /// <param name="calledFrom"></param>
        [OperationContract]
        WalmartMessageInfo OrderAuthorization(WalmartMessageInfo SendAuthorizationMessage, string messageRaw);

        /// <summary>
        /// from Walmart to Ria to reverse the SendAuthorization(). 
        /// </summary>
        /// <param name="orderID"></param>
        /// <param name="calledFrom"></param>
        /// <seealso cref="SendAuthorization"/>
        [OperationContract]
        WalmartMessageInfo OrderAuthorizationReverse(WalmartMessageInfo SendAuthorizationReverseMessage, string messageRaw);

        /// <summary>
        /// from Walmart to Ria to reverse the SendAuthorization() by id
        /// </summary>
        /// <param name="orderID">trans order id</param>
        [OperationContract]
        WalmartRequestResult OrderAuthorizationReverseByOrderId(int orderID, int modifiedUserId);

        /// <summary>
        /// from Walmart to Ria to reverse the PaymentAuthorization() by id
        /// </summary>
        /// <param name="orderID">trans order id</param>

        [OperationContract]
        WalmartRequestResult PaymentAuthorizationReverseByOrderId(int orderid, int modifiedUserId);

        /// <summary>
        /// from Walmart to Ria to reverse the RefundAuthorization() by id
        /// </summary>
        /// <param name="orderID">trans order id</param>
        [OperationContract]
        WalmartRequestResult RefundAuthorizationReverseByOrderId(int orderid, int modifiedUserId);

        #endregion sending
       
        #region paying

        /// <summary>
        /// from Ria to Walmart to ask for ticket number to included as a barcode in the payment receipt
        /// </summary>
        /// <param name="orderID"></param>
        /// <param name="calledFrom"></param>
        [OperationContract]
        WalmartRequestResult PaymentRequest(int orderID, int modifyUserId, int loginId, string calledFrom);

        /// <summary>
        /// from Walmart to Ria to confirm whether they can give the money to customer
        /// </summary>
        /// <param name="orderID"></param>
        /// <param name="calledFrom"></param>
        [OperationContract]
        WalmartMessageInfo PaymentAuthorization(WalmartMessageInfo PaymentAuthorizationMessage, string messageRaw);

        /// <summary>
        /// from Walmart to Ria to reverse the PaymentAuthorization
        /// </summary>
        /// <param name="orderID"></param>
        /// <param name="calledFrom"></param>
        /// <seealso cref="PaymentAuthorization"/>
        [OperationContract]
        WalmartMessageInfo PaymentAuthorizationReverse(WalmartMessageInfo PaymentAuthorizationReverseMessage, string messageRaw);

        #endregion paying

        #region refund

        /// <summary>
        /// from Ria to Walmart to ask for ticket number to included as a barcode in the payment receipt
        /// </summary>
        /// <param name="orderID"></param>
        /// <param name="calledFrom"></param>
        [OperationContract]
        WalmartRequestResult RefundRequest(int orderID, int modifyUserId, int loginId, string calledFrom);

        /// <summary>
        /// from Walmart to Ria to confirm whether they can give the money to customer
        /// </summary>
        /// <param name="orderID"></param>
        /// <param name="calledFrom"></param>
        [OperationContract]
        WalmartMessageInfo RefundAuthorization(WalmartMessageInfo RefundAuthorizationMessage, string messageRaw);

        /// <summary>
        /// from Walmart to Ria to reverse the PaymentAuthorization
        /// </summary>
        /// <param name="orderID"></param>
        /// <param name="calledFrom"></param>
        /// <seealso cref="RefundRequest"/>
        [OperationContract]
        WalmartMessageInfo RefundAuthorizationReverse(WalmartMessageInfo RefundAuthorizationReversemessage, string messageRaw);

        #endregion refund

        #region Logs
       
        /// <summary>
        /// get log from a order id
        /// </summary>
        /// <param name="orderId">order id</param>
        /// <returns></returns>
        [OperationContract]
        List<GatewayWalmartChileLogInfo> GetLogByOrderId(int orderId);

         /// <summary>
        /// get log from a order no
        /// </summary>
        /// <param name="orderId">order no</param>
        /// <returns></returns>
        [OperationContract]
        List<GatewayWalmartChileLogInfo> GetLogByOrderNo(string orderNo);

        [OperationContract]
        GatewayWalmartChileLogSearch SearchLog(DateTime dateFrom, DateTime dateTo,
            int messageType, int transCode, int traceNumber, string acquirerCode, int authorizationCode,
            int responseCode, string terminalNo, string transferType, int sequenceNo, string transNo,
            string extTicketNo, int extTicketStatus, int agentID, int agentLocID, int enteredBy, decimal transferAmountMin, decimal transferAmountMax, int rowNumber, int pageSize);

        #endregion Logs

        #region others

        [OperationContract]
        List<WalmartMessageType> GetAllMessageTypes();

        [OperationContract]
        int UpdateLogTableFields(int logId, string messageRaw);

        #endregion others

        #region search methods

        [OperationContract]

        [FaultContract(typeof(GetTransactionFault))]
        WalmartChileBriefTransactionInfoResponse GetTransactionOrderInfo(int orderID);

        [OperationContract]
        [FaultContract(typeof(GetTransactionFault))]
        WalmartChileBriefTransactionInfoResponse GetTransactionPaidInfo(int orderID);

        [OperationContract]
        [FaultContract(typeof(GetTransactionFault))]
        WalmartChileBriefTransactionInfoResponse GetTransactionRefundedInfo(int orderID);

        #endregion search methods

        [OperationContract]
        int testorderid(int number);
    }
}
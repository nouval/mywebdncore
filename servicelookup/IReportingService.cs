#region Namespace
using System.Collections.Generic;
using System.IO;
using System.ServiceModel;
using CES.Common.Bll.Types;
using CES.SL.Reporting.Contract.BillPayment.Receiving.Receipts;
using CES.SL.Reporting.Contract.CheckCashing.Receiving.Receipts;
using CES.SL.Reporting.Contract.CheckCashing.Receving;
using CES.SL.Reporting.Contract.Compliance.Europe;
using CES.SL.Reporting.Contract.DebitCard;
using CES.SL.Reporting.Contract.FXCorporate;
using CES.SL.Reporting.Contract.MoneyTransfer.AgentDeposits;
using CES.SL.Reporting.Contract.MoneyTransfer.Receiving;
using CES.SL.Reporting.Contract.MoneyTransfer.Receiving.Disclaimers;
using CES.SL.Reporting.Contract.MoneyTransfer.Receiving.Receipts;
using CES.SL.Reporting.Contract;
using CES.SL.Reporting.Contract.StoreOperation.Receiving.Receipts;
#endregion Namespace

namespace CES.SL.Reporting.Contract
{
	[ServiceContract]
	public interface IReportingService
	{
		[OperationContract]
		bool IsAlive();

		[OperationContract]
		Stream GetAgentReceipt(ReceiptRequest request);

		[OperationContract]
		CompanyInfo GetThermalReceiptCompanyInfo(int companyId, string companyCountry);

		[OperationContract]
        Stream GetReceivingAgentManualReceipt(OrderManualReceiptInfo orderManualReceiptInfo, ReportOptions reportOptions, string[] reqFields, string[] reqDMFields);

        [OperationContract]
		Stream GetReceivingAgentCustomerReceipt(OrderReceiptInfo orderInfo, ReportOptions reportOptions, ReceiptPrintType printingType, int applicationFEDBId, int userId, int agentId, int agentLocId);

		[OperationContract(Name = "DigitalReceiptsGetReceivingAgentCustomerReceipt")]
		Stream GetReceivingAgentCustomerReceipt(OrderReceiptInfo orderInfo, ReportOptions reportOptions, ReceiptPrintType printingType, int applicationFEDBId, int userId, int agentId, int agentLocId, int appId, int appObjId, int loginId, bool enableSave, ReceiptFormat receiptFormat);

		[OperationContract(Name = "RequestResponse")]
		GetReceivingAgentCustomerReceiptResponse GetReceivingAgentCustomerReceipt(GetReceivingAgentCustomerReceiptRequest request);

		[OperationContract(Name = "RequestResponse_v2")]
		GetReceivingAgentCustomerReceiptResponse GetReceivingAgentCustomerReceipt(GetReceivingAgentCustomerReceiptRequest request, bool useSSRSStream);

		 [OperationContract]
		Stream GetPreOrderDisclosureReceipt(ReceiptRequest request);

		 [OperationContract]
		List<DisclaimerInfo> GetThermalReceiptDisclaimer(DisclaimerRequest request);

        [OperationContract]
        List<ReceiptLabelsInfo> GetReceiptLabels(int serviceId, int productId, int productItemId, int languageId);

        [OperationContract]
        int GetReceiptLanguageID(string language1, string language2);

        [OperationContract]
		Stream GetConsumerReceipt(OrderReceiptInfo orderInfo, ReportOptions reportOptions, ReceiptPrintType printingType, int applicationFEDBId);

		[OperationContract]
		Stream GetDigitalConsumerReceipt(GetReceivingAgentCustomerReceiptRequest request);

        [OperationContract]
        Stream GetPEPReceipt(OrderReceiptInfo orderReceiptInfo, ReportOptions reportOptions);

        [OperationContract]
		Stream GetOrderOtherReceipt(OrderReceiptInfo orderReceiptInfo, ReportOptions reportOptions, string otherType);
        
        [OperationContract]
        Stream GetAmountLimitComplianceForm(AmountLimitComplianceFormInfo amountLimitComplianceFormInfo);

        [OperationContract]
        Stream GetOrderManualReceiptInfo(OrderManualReceiptInfo info);

        [OperationContract]
		Stream GetAgentDepositReport(AgentDepositReceiptInfo depositInfo, ReportOptions reportOptions, ReceiptPrintType printingType);

        [OperationContract]
        Stream GetBillInfoForPrinting(BillReceiptInfo billReceiptInfo, ReportOptions reportOptions, ReceiptPrintType printingType, int applicationFEDBId);

        [OperationContract]
        Stream GetManualBillInfoForPrinting(ReportOptions reportOptions);

        [OperationContract]
        Stream GetCheckCashingCustomerReceipt(CheckCashingReceiptInfo checkReceiptInfo, ReportOptions reportOptions, ReceiptPrintType printingType, int applicationFEDBId);
        
        [OperationContract]
        Stream GetCardTransactionCustomerReceipt(CardTransactionReceiptInfo cardInfo, ReportOptions reportOptions);

		[OperationContract]
		Stream GetPayingAgentReceipt(int orderId, string culture, ReceiptPrintType printingType, int applicationFEDBId, ReportRenderTypes renderType, int userId, int agentId, int agentLocId);

		[OperationContract(Name = "DigitalReceiptsGetPayingAgentReceipt")]
		Stream GetPayingAgentReceipt(int orderId, string culture, ReceiptPrintType printingType, int applicationFEDBId, ReportRenderTypes renderType, int userId, int agentId, int agentLocId, int appId, int appObjId, int loginId, bool enableSave, ReceiptFormat receiptFormat);

        [OperationContract]
        Stream GetCheckCashingReceiptInfo(CheckCashReceiptInfo checkcashReceiptInfo, ReportOptions reportOptions, string printingType);
        
        [OperationContract]
		Stream GetRefundAgentReceipt(int orderId, string culture, ReceiptPrintType printingType, int applicationFEDBId, ReportRenderTypes renderType, int userId, int agentId, int agentLocId);

		[OperationContract(Name = "DigitalReceiptsGetRefundAgentReceipt")]
		Stream GetRefundAgentReceipt(int orderId, string culture, ReceiptPrintType printingType, int applicationFEDBId, ReportRenderTypes renderType, int userId, int agentId, int agentLocId, int appId, int appObjId, int loginId, bool enableSave, ReceiptFormat receiptFormat);

        [OperationContract]
		bool SSRSTemplateExists(int orderId, string culture, ReceiptPrintType printingType, ReceiptType receiptType, int applicationFEDBId);

		[OperationContract]
		ReceiptTemplateInfo GetTemplateInfo(ReceiptRequest request);

        [OperationContract]
        Stream GetCorporateFxReceipts(FXCorporateReceiptRequest request);

		[OperationContract]
		List<ComplianceFormInfo> SSRSComplianceTemplateList(int orderId, string culture, ReceiptPrintType printingType, ReceiptType receiptType, int applicationFEDBId);

		[OperationContract]
		Stream GetSSRSComplianceForm(OrderReceiptInfo orderReceiptInfo, ReportOptions reportOptions, int applicationFEDBId, ReceiptTemplateInfo TemplateInfo, ReceiptPrintType PrintingType, int userId, int agentId, int agentLocId);

		[OperationContract(Name = "DigitalReceiptsGetSSRSComplianceForm")]
		Stream GetSSRSComplianceForm(OrderReceiptInfo orderReceiptInfo, ReportOptions reportOptions, int applicationFEDBId, ReceiptTemplateInfo TemplateInfo, ReceiptPrintType PrintingType, int userId, int agentId, int agentLocId, int appId, int appObjId, int loginId, bool enableSave, ReceiptFormat receiptFormat);

		[OperationContract]
		OrderRequireWatermarkInfo MTOrderRequiresReprintWatermark(int orderId, bool isNew);

		[OperationContract]
		SSRSOrderReceiptResponse GetRecevingAgentReceiptResponse(ReceiptRequest request);

		[OperationContract]
		Stream GetStoreOperationInfoForPrinting(StoreOperationReceiptInfo storeReceiptInfo, ReportOptions reportOptions, ReceiptPrintType printingType, int applicationFEDBId, string callby, bool StoreOpsShowSystemBalance);

    }
}
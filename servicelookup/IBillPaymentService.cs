#region Namespace
using System;
using System.Collections.Generic;
using System.IO;
using System.ServiceModel;
#endregion

namespace CES.SL.BillPayment.Contract
{
	[ServiceContract]
	public interface IBillPaymentService
	{
		[OperationContract]
		bool IsAlive();

		#region CustomerBillPaymentAccount

		[OperationContract]
		CustomerBillPaymentAccountInfo CreateNewCustomerBillPaymentAccount();
		[OperationContract]
		CustomerBillPaymentAccountInfo GetCustomerBillPaymentAccount(int customerAccountID);
		[OperationContract]
		void AddCustomerBillPaymentAccount(CustomerBillPaymentAccountInfo info, int modifyingUserId);
		[OperationContract]
		void RemoveCustomerBillPaymentAccount(int customerAccountID, int modifyingUserId);
		[OperationContract]
		void UpdateCustomerBillPaymentAccount(CustomerBillPaymentAccountInfo info, int modifyingUserId);

		#endregion CustomerBillPaymentAccount

		#region BillerLocation

		[OperationContract]
		BillerLocationInfo CreateNewBillerLocation();
		[OperationContract]
		BillerLocationInfo GetBillerLocation(int billerLocationID, int billerID);
		[OperationContract]
		void AddBillerLocation(BillerLocationInfo info, int modifyingUserId);
		[OperationContract]
		void RemoveBillerLocation(int billerLocationID, int billerID, int modifyingUserId);
		[OperationContract]
		void UpdateBillerLocation(BillerLocationInfo info, int modifyingUserId);
		[OperationContract]
		List<BillBeneficiaryInfo> GetBillerDetails(int customerID);
		[OperationContract]
		BillerLocationInfo GetBillerTerminalDetails(int customerAccountID);
		[OperationContract]
		int UpdateBillerTerminalDetails(BillerLocationInfo info, int customerId);

		[OperationContract]
		BillBeneficiaryInfo InsertBiller(BillBeneficiaryInfo info, int modifyingUserId);

		[OperationContract]
		void UpdateBiller(BillBeneficiaryInfo info, int modifyingUserId);

		[OperationContract]
		List<BillBeneficiaryInfo> GetAllBillersByCust(List<int> customerIDList, bool? disabled);
		[OperationContract]
		List<BillBeneficiaryInfo> GetBillersForCust(List<int> customerIDList);
		[OperationContract]
		BillBeneficiaryInfo GetBiller(int customerAccId);
		#endregion BillerLocation

		#region BillPayment
		[OperationContract]
		CommissionChargeInfo GetCommissionCharges(BillPaymentInfo info, decimal transAmt, SpecialOfferBP offerVar);

		[OperationContract]
		List<BillPaymentInfo> TransactionTypeList();

		[OperationContract]
		void ReleaseSpecialOffer(int transIdLocked);

		[OperationContract]
		List<BillStatusInfo> StatusTypeList(int fkey1);

		[OperationContract]
		List<BillerLocationInfo> GetBillerLookUpList(string biller, string country, string Currency, string AgentCountry, int agentid);

		[OperationContract]
		List<BillerLocationInfo> GetServiceTypeList(int billerlocID);

		[OperationContract]
		int GetDecimalByCurrency(string currency);

		[OperationContract]
		List<string> GetBillPaymentAccountMaskList(int billerlocID);

		[OperationContract]
		string CheckBPAccountMask(int billerlocID, string accountNo);

		[OperationContract]
		bool GetBillerLocationStatus(int billerID, int billerlocID);

		[OperationContract]
		bool CheckServiceOffered(BillPaymentInfo info);

		[OperationContract]
		bool CheckCustomerValid(int custID);

		[OperationContract]
		int GetBPPaymentProcessorID(int billerlocID, int serviceTypeID);

		[OperationContract]
		string GetBPProcessorPrompts(int billerlocID, int processorID);

		[OperationContract]
		string GetDistinctPromptList(int billerlocID);

		[OperationContract]
		int GetBPPromptValue_ExistforCustomer(int custAccID, int paymentProcessorID);

		[OperationContract]
		string GetBillPayment_AllProcessorData(int promptID);

		[OperationContract]
		int GetBP_InternalIDUsingPromptValue(int promptID);

		[OperationContract]
		string GetBP_ProcessorData(string extNo, int processorId, int promptValId, int serviceId);

		[OperationContract]
		string SaveCustomerAccountInfo(BillPaymentSaveInfo info, string promptValues);

		[OperationContract]
		List<BillPaymentSaveInfo> GetBP_AccountHolderInfo(int custAccountId);

		[OperationContract]
		List<BPStatesInfo> GetStatesList(string countryAbbrev);

		[OperationContract]
		List<BPPromptInfo> GetBillPaymentPromptDailog(int billerLocId);

		[OperationContract]
		string SaveBillPaymentTransactions(BillTransactionInfo info, string promptValues, int tellerDrawerInstanceID);

		[OperationContract]
		string SaveWalkInBillPaymentTransactions(BillTransactionInfo info, string promptValues, int tellerDrawerInstanceID);

		[OperationContract]
		int GetLastServiceTypeID(int billerLocId, int customerAccountId);

		[OperationContract]
		string SetCancellationMessage(string TextMessage, string CountryAbbr, int BillerID, int CarrierLocationID);

		[OperationContract]
		RateCommissionInfo GetBPRateCommission(RatesInfo info);

		[OperationContract]
		string BillPaymentCutOffMessages(int BillerLocID, int PmtProcessorID, int ServiceTypeID, bool IsReceipt);

		//[OperationContract]
		//string BillPaymentCutOffTimeMessage(string cutoffTime, int sameDayType, int serviceTypeId);

		[OperationContract]
		string BillPaymentCutOffTimeMessage(int billerLocId, int pmtProcessorID, int serviceTypeId);

		[OperationContract]
		List<BpCountryList> GetCountryBillerListBP(int languageId);
		[OperationContract]
		List<CarrierBiller> GetBillerCarrierLookUp(string billerName, int transtype, string AgentCountry, string AgentState, int AgentID, int AgentLocID, string AgentCurrency);
		[OperationContract]
		List<BPPaymentMethod> GetPaymentMethod(int AgentId);

		#endregion

		#region WirelessTopUp

		[OperationContract]
		WireTopUpAmountsInfo GetTopUpAmount(WirelessTopUpInfo info, int companyID, int agentID, string agentState, string agentCountry, decimal transAmt);

		[OperationContract]
		List<WirelessTopUpInfo> CountryBillerList();

		[OperationContract]
		WirelessTopUpAmountRangeInfo GetValidTopUpAmountRange(WirelessTopUpInfo info);

		[OperationContract]
		WirelessTopUpRateInfo GetTransRate(WirelessTopUpInfo info, int agentID, string agentCurrency, string agentCountry);

		[OperationContract]
		WirelessRechargeInfo ValidateRechargeNumber(WirelessTopUpInfo info, int serviceId, int serviceKey);

		[OperationContract]
		WirelessTopUpInfo SaveWirelessTransaction(WirelessTransactionInfo info, string AppVersion, string AppName, int EpayRequestTimeOut, int ArrayPosition, string FieldNames);

		//[OperationContract]
		//List<WirelessTopUpInfo> WirelessCountryList();

		[OperationContract]
		List<WirelessTopUpInfo> WirelessCountryList(DateTime TransDate, int AgentID, int AgentLocId, int AgentCompanyID, int AgentTypeID,
			int ServiceID, int ProductID, int ProductItemID, string AgentCountry, string AgentState, string ProductCategory, string Processor_Code, string AgentCurrency);


		[OperationContract]
		List<TopUpFilteredCountryListInfo> TopUpFilteredCountryList(DateTime TransDate, int AgentID, int AgentLocId, int AgentCompanyID, int AgentTypeID,
			int ServiceID, int ProductID, int ProductItemID, string AgentCountry, string AgentState, string ProductCategory, string Processor_Code, string AgentCurrency, int languageId);
		[OperationContract]
		List<TopUpBillernameListInfo> GetTopUpBillerList(List<BillerSearchInfo> searchInfoList, DateTime TransDate, int AgentID, int AgentLocId, int AgentCompanyID, int AgentTypeID,
			int ServiceID, int ProductID, int ProductItemID, string AgentCountry, string AgentState, int ProductCategoryID, string AgentCurrency);

		[OperationContract]
		List<WirelessTopUpInfo> GetWirelessCarrierList(DateTime TransDate, int AgentID, int AgentLocId, int AgentCompanyID, int AgentTypeID,
			int ServiceID, int ProductID, int ProductItemID, string AgentCountry, string AgentState, int ProductCategoryID, string CountryAbbrev, string ProductCategory, string Processor_Code, string AgentCurrency);

		[OperationContract]
		List<TransInfo> CustomersTransLookUp(int customerID, string customerNo, string ProductType);
		#endregion

		#region Customer

		[OperationContract]
		List<CustomerInfo> GetCustomers(CustomerInfo info, int rowNumber, int pageSize);

		[OperationContract]
		int CheckCustomerNo(int lTempNo, string sCustomerNo);

		[OperationContract]
		List<BeneficiaryEnrollCardInfo> GetBeneficiaryDetails(int customerID);

		[OperationContract]
		BeneficiaryEnrollCardInfo GetBeneficiaryTerminalDetails(int beneficiaryId);

		[OperationContract]
		int UpdateBeneficiaryTerminalDetails(BeneficiaryEnrollCardInfo info, int customerId);

		#endregion

		#region Search Billpayment Orders

		[OperationContract]
		List<BillInfo> SearchOrders(String agentID, string transNo, DateTime dateStart, DateTime dateEnd, String status, String transtype, int rowNumber, int pageSize, string CustomerTelNo, int EnteredByID, string Branch, string BillerLocID);

		#endregion

		#region Validate Service Offered

		[OperationContract]
		bool ValidateServicesOffered(int ServiceId, int ProductId, int AgentID);

		[OperationContract]
		bool VerifyServicesOffered(int ServiceId, int ProductId, int ProductItemId, int EntryMethod, string CountryFrom, string StateFrom, int CityIDFrom, string CountryTo,
			string StateTo, string CurrencyFrom, string CurrencyTo, int DeliveryMethod, DateTime TransDate, Decimal TransAmountFrom, Decimal TransAmountTo, int AgentID, int AgentLocID, int CorrespID, int CorrespLocID);

		#endregion

		#region Voiding and Cancellation of Transaction

		[OperationContract]
		string VoidBillPaymentTransaction(string tranid, string userid, string entryType, int tellerDrawerInstanceID);

		[OperationContract]
		CancelTransactionResponse CancelTransaction(int serviceId, int serviceKey, string tranid, string userid);

		#endregion

		#region Checks Zero Amount Biller
		[OperationContract]
		int GetZeroAmountBillerExist(int billerlocID);

		#endregion

		#region  Psreceipt value
		[OperationContract]
		string GetPSReceiptVlaue(int transId);
		#endregion

		#region GetBillReceipt

		[OperationContract]
		Stream GetBillReceipt(int transId, string locale, bool billReceipt, bool isTopUp, string pin, string userId, int RefNum);

		#endregion

		#region GetThermalReceipt_BP_And_TP

		[OperationContract]
		BPThermalReceiptInfo GetBPThermalReceiptInfo(int transId, string locale, bool billReceipt);

		[OperationContract]
		BPThermalReceiptInfo GetTPThermalReceiptInfo(int transId, string pin, string locale, bool billReceipt, string userId);

		#endregion

		#region GetBPReceivingAgentManualReceipt
		[OperationContract]
		Stream GetBPReceivingAgentManualReceipt();
		#endregion

		#region ValidateBillerAccount
		[OperationContract]
		ValidateBillerResponse ValidateBillerAccount(BillerAccountInfo info, int serviceId, int serviceKey);
		#endregion

		#region Validate Transactions
		[OperationContract]
		BPValidateResponse ValidateTransaction(BPAccountInfo bpacctinfo, int serviceId, int serviceKey, int paymentProcessorID, int CustomerID, int CustomerAccID, int UserID, string AppVersion, string App);

		#endregion

		#region Spain TopUp Receipt
		[OperationContract]
		OnlineBillInfo GetSpainTopUpReceipt(int transId, string locale, bool billReceipt, int RefNum);
		#endregion

		#region Getting Computer ID from the Mac Address
		[OperationContract]
		string GetCoumputerID(string macadress, string agentid);

		[OperationContract]
		int GetReceiptType(int computerid, string setting);

		[OperationContract]
		bool SaveReceiptType(int computerid, int settingid, string setting, int modifiedid);

		#endregion

		#region Getting Confirmation Number

		[OperationContract]
		string GetConfirmationNumber(string TransID);

		#endregion

		#region Getting Agent City ID

		[OperationContract]
		int GetAgentCityID(string AgentCountry, string AgentPostalCode);

		#endregion

		#region Getting Agent Commission Report

		[OperationContract]
		BillCommissionInfo GetAgentCommissionReport(String agentID, DateTime dateStart, DateTime dateEnd, string BillerLocID, string Country, int CountryID, int AgentLocID, string Agentloclist);

		#endregion

		#region Saving Top Up Transactions to Realtime Log

		[OperationContract]
		int SaveTopUpRealtimeLogTransactions(decimal BillAmount, decimal TransRate, decimal TransAmount, int AgentID, int ArrayPosition, string FieldNames, int BilerLocationID);

		#endregion

		#region Retriving thr Trans info from Activation Code
		[OperationContract]
		BillPaymentTransactionInfo GetOTHTransByActivationCode(string ActNo);
		#endregion

		#region Retriving thr Trans info from Activation Code
		[OperationContract]
		SpecialOfferInfo GetPromoOfferBP(SpecialOfferWithPromoRequest topUpInfo, decimal originalCustomerFee, int agentID, string agentCurrency, string agentCountry, SpecialOfferBP PromoCode);
		#endregion



		[OperationContract]
		SearchBillerLookupinformation GetBillerLookupinformation(int BillerLocationIDHiddenField, int CustomerAccountNumberHiddenField, int AgentId, string AgentBaseCurrency, string RecvAgentCountry, int RecvAgentID, string BillerCurrencyHiddenField, int BillerIDHiddenField, int GetlastServiceTypeID, BillPaymentInfo info, SpecialOfferBP SpecOfferBP, WirelessTopUpInfo wirlessTPinfo, decimal BillerAmount);


	}
}

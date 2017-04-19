using CES.SL.MT.Pricing.Contract.Fees;
using CES.SL.MT.Pricing.Contract.Programs;
using CES.SL.MT.Pricing.Contract.Rates;
using CES.SL.MT.Pricing.Contract.Recalculation;
using CES.SL.MT.Pricing.Contract.SpecialOffer;
using System;
using System.Collections.Generic;
using System.ServiceModel;

namespace CES.SL.MT.Pricing.Contract
{
	[ServiceContract]
	public interface IPricingService
	{
		[OperationContract]
		bool IsAlive();

		#region Fees

		[OperationContract]
		FeeInfo Get(FeeInfoRequest feeInfoRequest);

		[OperationContract]
		FeeInfo GetLowestByCountry(FeeInfoRequest feeInfoRequest);

		[OperationContract]
		List<FeeInfo> GetLowestFeeListByCountry(List<FeeInfoRequest> feeInfoRequestList);

		#endregion Fees

		#region Rates

		[OperationContract]
		RateInfo GetRateInfo(RateInfoRequest request);

		[OperationContract]
		List<PayAgentRateInfo> GetRateInfoByAgentList(List<RateInfoRequest> rateInfoRequestList);

		[OperationContract]
		AgentRateLevelInfo GetRateLevelInfo(int recAgentId, int recAgentLocationId, string countryTo, string currencyTo);

		#endregion Rates

		#region Currencies

		[OperationContract]
		List<CurrenciesInfo> GetAllCurrencies();
		[OperationContract]
		List<string> GetPayoutCurrencyListByCountry(string countryTo);

		#endregion Currencies

		#region ProgramExtra

		[OperationContract]
		List<ProgramExtraInfo> SearchProgramExtraByExtraTypeList(ProgramExtraSearchRequest request);

		#endregion ProgramExtra

		#region ProgramsQuestion

		[OperationContract]
		List<ProgramQuestionInfo> GetProgramQuestionByCSV(ProgramExtraType extraID, string subTypeIDCSV);

		#endregion ProgramsQuestion

		#region Programs

		[OperationContract]
		ProgramsInfo CreateNewPrograms();

		[OperationContract]
		List<AgentProgramInfo> GetAllProgramsForAgent(int nameID);

		[OperationContract]
		List<AgentProgramInfo> GetAllProgramsByAgentCurrency(int nameID, string currency);

		#endregion Programs

		#region ProgramsCommGroup

		[OperationContract]
		ProgramCommGroupInfo CreateNewProgramsCommGroup();

		#endregion ProgramsCommGroup

		#region Payout Exchange Rate

		[OperationContract]
		CorrespPayoutRateInfo GetCorrespPayoutRateInfo(int itemId);

		[OperationContract]
		int AddCorrespPayoutRateInfo(CorrespPayoutRateInfo info, int modifyingUserId);

		[OperationContract]
		void UpdateCorrespPayoutRateInfo(CorrespPayoutRateInfo info, int modifyingUserId);

		[OperationContract]
		void RemoveCorrespPayoutRateInfo(int itemId, int modifyingUserId);

		[OperationContract]
		List<CorrespPayoutRateInfo> GetCorrespPayoutRateInfos(GetCorrespPayoutRateInfosRequest request, out int totalCount);

		[OperationContract]
		List<PayoutExchangeRateInfo> GetLatestExchangeRates(GetLatestExchangeRatesRequest request);

		#endregion Payout Exchange Rate

		#region Payout Correspondent Base Rates

		[OperationContract]
		CorrespBaseRatesInfo GetCorrespAndCurTo(int correspId, string currencyTo);

		#endregion

		#region Payout Correspondent Base Rates Saved

		[OperationContract]
		CorrespBaseRatesSavedInfo GetItemForGivenDate(int item_struct_Id, DateTime transDate);

		#endregion

		#region Recalculate Amounts & Rates

		[OperationContract]
		AmountRecalculationInfo RecalculateOrderAmounts(RecalculationInfoRequest request);

		#endregion

		[OperationContract]
		[FaultContract(typeof(NoRatesFoundFault))]
		[FaultContract(typeof(PricingFault))]
		[FaultContract(typeof(CommissionNoSetupFault))]
		RateAndFeeInfo GetRatesAndFees(RateAndFeeRequest request);

		[OperationContract]
		void FinalizeSpecialOffer(int sessionId, int transIdLocked, int serviceId, int productId, int orderId, decimal originalCustomerCharge, int ruleid, decimal addOnAmount);

		[OperationContract]
		decimal GetCustomerCharge(SpecialOfferInfo offersInfo, FeeInfo feeInfo, decimal localAmmount);
		[OperationContract]
		string GetOfferType(int itemId);

		/// <summary>
		///     Get the next available promotion.
		/// </summary>
		/// <param name="offersInfoRequest"></param>
		/// <returns></returns>
		[OperationContract]
		SpecialOfferInfo GetNextAvailablePromotion(RateAndFeeRequest rateAndFeeRequest, decimal OriginalCustCharge);

		/// <summary>
		///		Update not used promotion to reuse
		/// </summary>
		/// <param name="customerID"></param>
		[OperationContract]
		void ReleaseSpecialOffer(int transIdLocked);

		[OperationContract]
		int GetFeeIdForOrder(FeeIdRequest feeIdRequest);

		[OperationContract]
		string GetCorrespondentCommissionCurrency(FeeCurrencyRequest feeCurrencyRequest);

		[OperationContract]
		ProgramCommInfo GetCommissionInfo(int commissionId);

		[OperationContract]
		decimal GetCustomerFeeInSpecifiedCurrency(int orderId, int payAgentId, int payAgentLocationId, string currency);

		[OperationContract]
		ProgramCommGroupInfo GetDefaultFeeGroupInfo();

		[OperationContract]
		BeneficiaryTaxResponse GetBeneficiaryTaxAmount(BeneficiaryTaxRequest request);
	}
}

using CES.Common.Bll.System;
using CES.Common.Bll.Types;
using CES.SL.Agents.Contract.Addresses;
using CES.SL.Agents.Contract.Finance;
using CES.SL.Agents.Contract.Paying;
using CES.SL.Agents.Contract.Receiving;
using CES.SL.Agents.Contract.Sequences;
using System;
using System.Collections.Generic;
using System.ServiceModel;

namespace CES.SL.Agents.Contract
{
	[ServiceContract]
	public interface IAgentService : IAgentExpressService
	{
		#region Branding

		//[OperationContract]
		//void AddBrandingInfo(AgentBrandingInfo info, int modifyingUserId);
		[OperationContract]
		AgentBrandingInfo GetBrandingInfoByLocationID(int agentID, int agentLocID);
		[OperationContract]
		AgentBrandingInfo GetBrandingInfo(int agentID);

		#endregion Branding

		[OperationContract]
		int GetAgentIDFromAgentNo(string agentNo);

		#region AgentLocation

		[OperationContract]
		AgentLocationInfo CreateNewAgentLocation();
		[OperationContract]
		AgentLocationInfo GetAgentLocation(int agentId, int locationId);
		[OperationContract]
		AgentLocationInfo GetAgentLocationByState(int agentId, string state);
		[OperationContract]
		List<AgentLocationInfo> GetAgentLocationsByAgent(int agentId);
		[OperationContract]
		List<AgentLocationInfo> GetAgentLocations(List<int> locationIdList);
		[OperationContract]
		List<AgentLocationInfo> GetAgentLocationsWithService(int agentId, string cityPrefix, string receivingCountryAbbrev, int deliveryMethod, string receivingCurrency);
		[OperationContract]
		List<AgentLocationInfo> GetCorrespondentLocationList(int agentId, string cityPrefix, string state, string receivingCountryAbbrev, int deliveryMethod, string receivingCurrency);
		[OperationContract]
		bool CorrespondentHasOpenPayment(int payagentId, string country);
		[OperationContract]
		bool CountryHaveOpenPayment(string country);

		#endregion AgentLocation

		#region PayingAgent

		[OperationContract]
		PayingAgentInfo GetPayingAgent(int payingAgentId);
		[OperationContract]
		List<PayingAgentInfo> GetPayingAgents(List<int> payingAgentIdList);
		[OperationContract]
		PayingAgentInfo CreateNewPayingAgent();
		[OperationContract]
		List<PayingAgentWithLocationCountInfo> GetAgentsWithService(string cityPrefix, string receivingCountryAbbrev, int deliveryMethod, string receivingCurrency);

		#endregion PayingAgent

		#region PayingAgentLocationSearchInfo
		[OperationContract]
		List<PayingAgentLocationSearchInfo> GetAllPayingAgentAndLocation();
		[OperationContract]
		List<PayingAgentLocationSearchInfo> SearchPayingAgentLocationsByCountries(List<string> countries);
		[OperationContract]
		List<PayingAgentLocationExhaustiveSearchResponse> SearchPayAgentAndLocationExhaustive(PayingAgentLocationExhaustiveSearchRequest request);
		[OperationContract]
		List<CityStateCountryInfo> GetCitiesStatesWithService(string cityPrefix, string receivingCountryAbbrev, int deliveryMethod, string receivingCurrency);
		[OperationContract]
		List<CityStateCountryInfo> GetCitiesStatesWithServiceForRiaMT(string cityPrefix, string receivingCountryAbbrev, int deliveryMethod, string receivingCurrency);
		[OperationContract]
		List<string> GetPayOutCities(string country, string city);
		[OperationContract]
		List<PayAgentCitiesAndStatesInfo> GetPayOutCitiesAndStates(string country, string city, int deliveryMethodId);
		[OperationContract]
		List<PayingAgentSearchInfo> GetCorrespondentList(int agentId, string countryTo, string stateTo, string cityTo, int deliveryMethod, int programId, string currencyFrom, DateTime date, bool cityMatchType);

		#endregion PayingAgentLocationSearchInfo

		#region PreferredPayingAgentLocation

		[OperationContract]
		PreferredPayingAgentLocationResponse GetPreferredPayingAgentLocations(int payingAgentId, int payingAgentLocationId, int receivingAgentId, string countryFrom, string stateFrom, string cityFrom, string currencyFrom, string currencyTo, DateTime orderDate, DeliveryMethodType deliveryMethod, int programId, DateTime localTime);

		[OperationContract]
		PreferredPayingAgentScheduleInfo GetPreferredPayingAgentSchedule(int itemID);

		#endregion PreferredPayingAgentLocation

		#region PayingAgentDeliveryMethod

		[OperationContract]
		PayingAgentDeliveryMethodInfo CreateNewDeliveryMethod();
		[OperationContract]
		List<PayingAgentDeliveryMethodInfo> GetAllDeliveryMethods(int payAgentID);
		[OperationContract]
		List<PayingAgentDeliveryMethodInfo> GetAllDeliveryMethodsByLocation(int payAgentID, int locationID);
		[OperationContract]
		List<PayingAgentDeliveryMethodInfo> GetDeliveryMethodByLocation(int payAgentId, int locationId);
		[OperationContract]
		List<DeliveryMethodType> GetAvailableDeliveryMethodByCountry(string countryAbbrev, ServiceType serviceType);

		#endregion PayingAgentDeliveryMethod

		#region PayingAgentCurrency

		[OperationContract]
		PayingAgentCurrencyInfo CreateNewPayingAgentCurrency();
		[OperationContract]
		PayingAgentCurrencyInfo GetPayingAgentCurrency(int nameID, string symbol);
		[OperationContract]
		List<PayingAgentCurrencyInfo> GetAllPayingAgentCurrenciesByAgent(int nameID);
		[OperationContract]
		List<string> GetCurrencyListByCountry(string countryTo, ServiceType serviceType);
		[OperationContract]
		List<AgentServicesOfferedInfo> GetCurrencyByCountryAndRecAgent(string countryTo, ServiceType serviceType, int recAgentId);
		[OperationContract]
		List<string> GetAvailableCurrencyListByCountryAndDeliveryMethod(string countryTo, ServiceType serviceType, int[] deliveryMethodTypeIdArray);

		#endregion PayingAgentCurrency

		#region PayingAgentLocationCurrency

		[OperationContract]
		PayingAgentCurrencyInfo CreateNewPayingAgentLocationCurrency();
		[OperationContract]
		PayingAgentCurrencyInfo GetPayingAgentLocationCurrency(int locationId, string symbol);
		[OperationContract]
		List<PayingAgentCurrencyInfo> GetAllPayingAgentLocationCurrenciesByLocation(int locationId);

		#endregion PayingAgentLocationCurrency

		#region PayingAgentRating

		[OperationContract]
		PayingAgentRatingInfo CreateNewPayingAgentRating();
		[OperationContract]
		List<PayingAgentRatingInfo> GetPayingAgentRating(int correspID, string countryFrom, string countryTo, byte delivMethod);

		#endregion PayingAgentRating

		#region ReceivingAgent

		[OperationContract]
		ReceivingAgentInfo GetReceivingAgent(int receivingAgentId);
		[OperationContract]
		ReceivingAgentInfo CreateNewReceivingAgent();

		#endregion ReceivingAgent

		#region ReceivingAgentCurrency

		[OperationContract]
		ReceivingAgentCurrencyInfo CreateNewReceivingAgentCurrency();
		[OperationContract]
		ReceivingAgentCurrencyInfo GetReceivingAgentCurrency(int nameID, int branchID, string symbol);
		[OperationContract]
		List<ReceivingAgentCurrencyInfo> GetAllReceivingAgentCurrenciesByAgent(int agentId);

		#endregion ReceivingAgentCurrency

		#region ReceivingAgentLoginSettings

		#endregion ReceivingAgentCurrency

		#region AgentLoginSettingsInfo

		[OperationContract]
		AgentLoginSettingsInfo GetAgentLoginSettingsInfo(int agentId, bool defaultSettingsIfEmpty);
		[OperationContract]
		AgentLoginSettingsInfo CreateNewAgentLoginSettings();

		#endregion AgentLoginSettingsInfo

		#region AgentServicesOffered

		[OperationContract]
		List<AgentServicesOfferedInfo> GetAgentServicesOfferedByPayingAgentLocation(ServiceType serviceType, int agentId, int agentLocationId, DateTime serviceDate);
		[OperationContract]
		AgentServicesOfferedInfo CreateNewAgentServicesOffered();

		#endregion AgentServicesOffered


		#region Finance

		[OperationContract]
		AgentReceivingLimitCheckResultInfo CheckAgentsReceivingLimit(int agentId, decimal amount, string currency, int userId);
		[OperationContract]
		AgentCanPayResult CheckAgentsPayingLimit(int agentId, int agentLocationId, string currency, decimal amount);

		#endregion Finance

		#region AgentLocationDetail

		[OperationContract]
		AgentLocationDetailInfo CreateNewAgentLocationDetail();
		[OperationContract]
		List<AgentLocationDetailInfo> GetAgentLocationDetailByAgentAndLocation(int agentID, int locationID);

		#endregion AgentLocationDetail

		#region Sequences

		[OperationContract]
		int GetNextReceivingAgentSequenceId(int recAgentId, int nextIncrement);
		[OperationContract]
		int GetNextReceivingAgentLocationSequenceId(int recAgentLocationId, int nextIncrement);
		[OperationContract]
		IdNoPinInfo GetIdNoPin(int loginLocationId, int payingAgentId, int payingAgentLocationId, int recAgentId, int recAgentCompanyId, string recAgentCountry);
		[OperationContract]
		SequenceInfo GetReceivingAgentSequenceNo(int recAgentId, int recAgentLocationId);
		[OperationContract]
		DateTime GetLastRecordedTimeForAgent(int recAgentId);

		#endregion Sequences

		#region PayAgentsOpenPaymentGroups

		[OperationContract]
		PayAgentsOpenPaymentGroupsInfo CreateNewPayAgentsOpenPaymentGroups();
		[OperationContract]
		List<PayAgentsOpenPaymentGroupsInfo> GetPayAgentsOpenPaymentGroupsByPaidFor(int nameIDPayFor);
		[OperationContract]
		void AddPayAgentsOpenPaymentGroups(PayAgentsOpenPaymentGroupsInfo info);
		[OperationContract]
		void RemovePayAgentsOpenPaymentGroups(int nameIDPaidBy, int nameIDPayFor);
		[OperationContract]
		void UpdatePayAgentsOpenPaymentGroups(PayAgentsOpenPaymentGroupsInfo info);

		#endregion PayAgentsOpenPaymentGroups

		#region RecevingAgent MoneyTransfer Settings

		[OperationContract]
		RecevingAgentMoneyTransferSettingInfo GetRecevingAgentMoneyTransferSetting(int nameID, int settingID);


		#endregion RecevingAgent MoneyTransfer Settings

		[OperationContract]
		AgentAddressInfo GetAddress(int agentId, int agentLocationId);

		[OperationContract]
		[FaultContract(typeof(LoginFault))]
		AgentLoginResponse Login(AgentLoginRequest request);
	}
}
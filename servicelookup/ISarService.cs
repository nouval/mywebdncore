using System;
using System.Collections.Generic;
using System.Data;
using System.ServiceModel;

namespace CES.SL.Compliance.Sar.Contract
{
	[ServiceContract]
	public interface ISarService
	{
		[OperationContract]
		bool IsAlive();
		[OperationContract]
		SarValidationResponse Validate(SarValidationRequest sarValidationRequest);
		[OperationContract]
		SarValidationResponse Validate2(SarRequest sarRequest);
		[OperationContract]
		SarValidationResponse Validate_CEX(int AppID, int AppObjectID,SarValidationRequest sarValidationRequest);

		[OperationContract]
		List<SARFilterInfo> GetSARFilterListByCountry(string countryFrom, string countryTo, List<int> agentIdList, bool showDisabled);
		[OperationContract]
		List<SARFieldInfo> GetAllFieldsByFilterID(int filterID);

		#region Compliance Log Entry

		[OperationContract]
		SarLogInfo GetSARLogDetails(int action, int orderID, string timeZoneOffSet);

		[OperationContract]
		List<SarLogInfo> GetBlackListLogs(int action, DateTime beginDate, DateTime endDate, String ticketNumber, string agentIds, string timeZoneOffSet);

		[OperationContract]
		List<SarLogInfo> GetRejectedOrders(int action, DateTime beginDate, DateTime endDate, string agentIds, string timeZoneOffSet);

		[OperationContract]
		List<SarLogInfo> GetBlackListAttempts(int action, DateTime beginDate, DateTime endDate, string agentIds, string timeZoneOffSet);

		[OperationContract]
		DataSet GetSARLogsXML(DateTime beginDate, DateTime endDate, String ticketNumber, bool getAll, String sarCriteria, string agentIds, string timeZoneOffSet);

		[OperationContract]
		int GetSarLogsPaginationSize(DateTime beginDate, DateTime endDate, String ticketNumber, bool getAll,
																							String sarCriteria, string agentIds, string timeZoneOffSet,
																							bool onlyLegalHold);

		#endregion Compliance Log Entry

		#region ComplianceLogEntry

		[OperationContract]
		ComplianceLogEntryInfo CreateNewComplianceLogEntry();
		[OperationContract]
		List<ComplianceLogEntryInfo> GetComplianceLogEntriesByOrderId(int orderId);
		[OperationContract]
		List<ComplianceLogEntryInfo> GetComplianceLogEntries(string types, string issueIds, DateTime startDate, DateTime endDate);
		[OperationContract(Name = "Add duplicate issue")]
		int AddComplianceIssue(int orderID, int userNameID, string message, string note, string onHoldReason, int onHold, int serviceTypeID, int issueTypeID, int issueItemID, int loginLocationID, bool allowToCreateDuplicateIssue);
		[OperationContract]
		int AddComplianceIssue(int orderID, int userNameID, string message, string note, string onHoldReason, int onHold, int serviceTypeID, int issueTypeID, int issueItemID, int loginLocationID);
		[OperationContract]
		int CheckIssueStatus(int orderID, int issueTypeID, int issueItemID);

		#endregion ComplianceLogEntry

		#region SARFilter

		[OperationContract]
		SARFilterInfo CreateNewSARFilter();
		[OperationContract]
		SARFilterInfo GetSARFilter(int Id);
		[OperationContract]
		List<SARFilterInfo> GetSARFilters(List<int> Ids);

		#endregion SARFilter

		#region RejectedOrder

		[OperationContract]
		RejectedOrderInfo CreateNewRejectedOrder();
		[OperationContract]
		List<RejectedOrderInfo> GetSARRejectedOrdersByDateAndRecAgents(List<int> recAgentIds, DateTime fromDate, DateTime toDate);
		[OperationContract]
		List<RejectedOrderInfo> GetOFACRejectedOrdersByDateAndRecAgents(List<int> recAgentIds, DateTime fromDate, DateTime toDate);
		[OperationContract]
		void AddRejectedOrder(RejectedOrderInfo info, int modifyingUserId);

		#endregion RejectedOrder

		#region SARField

		[OperationContract]
		SARFieldInfo CreateNewSARField();

		#endregion SARField

		[OperationContract]
		ComplianceInfo GetComplianceMatchById(int id);

	}
}

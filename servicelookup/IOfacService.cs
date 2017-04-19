using System;
using System.Collections.Generic;
using System.ServiceModel;

namespace CES.SL.Compliance.Ofac.Contract
{
	[ServiceContract]
	public interface IOfacService
	{
		[OperationContract]
		bool IsAlive();
		[OperationContract]
		Dictionary<int, List<OFACMatch>> Match(OfacUserInfo ofacUser);
		[OperationContract]
		ValidationResponse Match2(OfacUserInfo ofacUser);
		[OperationContract]
		List<OFACAddressInfo> GetAddressList(int ofacListID, string nameToSearch);
		[OperationContract]
		List<OFACAddressInfo> GetAllAddressesByCSV(int ofacListID, string entryIDCSV);
		[OperationContract]
		List<OFACAliasInfo> GetAliasList(int ofacListID, string nameToSearch);
		[OperationContract]
		List<OFACNameCombinedInfo> GetNameCombinedList(int ofacListID, string nameToSearch);
		[OperationContract]
		List<OFACNameInfo> GetNameList(int ofacListID, string nameToSearch);
		[OperationContract]
		List<OFACNameInfo> GetAllNamesByCsv(int ofacListID, string ent_numCSV);
		[OperationContract]
		List<OFACListInfo> GetAllBlackLists(int agentID);
		[OperationContract]
		OFACListInfo GetBlackList(int listID, int agentID);
		[OperationContract]
		List<OFACListHistoryInfo> GetListHistory(int listID);
		[OperationContract]
		List<OFACEntryInfo> GetAllListEntries(int listID);
		[OperationContract]
		OFACEntryInfo GetEntryInfo(int ofacListID, int entryID);
		[OperationContract]
		List<OFACAliasInfo> GetAllAliases(int ofacListID, int entryID);
		[OperationContract]
		List<OFACAliasInfo> GetAllAliasesByCSV(int ofacListID, string entryIDCSV);
		[OperationContract]
		List<OFACLogInfo> GetOFACLogEntriesByOrderIds(string orderIDCsv);
		[OperationContract]
		List<int> GetOFACLogOrderIdsByDate(DateTime fromDate, DateTime toDate);
		[OperationContract]
		int AddOFACLogEntry(OFACLogEntryInfo info);
		[OperationContract]
		WatchlistResponse WatchlistMatch(WatchListUserInfo WatchListUser, int appID, int appObjectID, int userID);
	}
}

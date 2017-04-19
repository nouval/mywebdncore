using System;
using System.Collections.Generic;
using System.ServiceModel;

namespace CES.SL.ForeignExchange.Contract
{
	[ServiceContract]
	public interface IForeignExchange
	{
		[OperationContract]
		bool IsAlive();

		#region Reseller Info

		[OperationContract]
		ResellerInfo GetReseller(int agentId);

		[OperationContract]
		ResellerInfo AddResellerInfo(ResellerInfo resellerInfo, int modifyingUserId);

		[OperationContract]
		void UpdateResellerInfo(ResellerInfo resellerInfo, int modifyingUserId);

		[OperationContract]
		void RemoveResellerInfo(int accountID, int modifyingUserId);

		#endregion Reseller Info

		#region ResellerRateSheets1

		[OperationContract]
		ResellerRateSheets1Info CreateNewResellerRateSheets1();
		[OperationContract]
		ResellerRateSheets1Info GetResellerRateSheets1(int rateSheetID);
		[OperationContract]
		void AddResellerRateSheets1(ResellerRateSheets1Info info, int modifyingUserId);
		[OperationContract]
		void RemoveResellerRateSheets1(int rateSheetID, int modifyingUserId);
		[OperationContract]
		void UpdateResellerRateSheets1(ResellerRateSheets1Info info, int modifyingUserId);

		#endregion ResellerRateSheets1

		#region ResellerRateSheets2

		[OperationContract]
		ResellerRateSheets2Info CreateNewResellerRateSheets2();
		[OperationContract]
		ResellerRateSheets2Info GetResellerRateSheets2(int rateSheetID, string currency);
		[OperationContract]
		List<ResellerRateSheets2Info> GetAllResellerRateSheets2(int rateSheetID);
		[OperationContract]
		void AddResellerRateSheets2(ResellerRateSheets2Info info);
		[OperationContract]
		void RemoveResellerRateSheets2(int rateSheetID, string currency);
		[OperationContract]
		void UpdateResellerRateSheets2(ResellerRateSheets2Info info);

		#endregion ResellerRateSheets2

		#region Reseller Agent/User Permission

		[OperationContract]
		ResellerUserPermissionsInfo GetResellerUserPermissions(int agentID, int userNameID, int permissionID);

		[OperationContract]
		void AddResellerUserPermissions(ResellerUserPermissionsInfo info);

		[OperationContract]
		void UpdateResellerUserPermissions(ResellerUserPermissionsInfo info);

		[OperationContract]
		void RemoveResellerUserPermissions(int agentID, int userNameID, int permissionID);

		#endregion Reseller Agent/User Permission

		[OperationContract]
		List<FXTicketsInfo> GetFXTicketsByDate(int userId, int agentId, DateTime date, bool showAllUsers);

		#region FXTickets1

		[OperationContract]
		FXTickets1Info CreateNewFXTickets1();

		[OperationContract]
		FXTickets1Info GetFXTickets1(int ticketID);

		[OperationContract]
		List<FXTickets1Info> GetFXTicketsForAgent(int agentID, DateTime startDate, DateTime endDate);

		[OperationContract]
		void AddFXTickets1(FXTickets1Info info, int modifyingUserId);

		[OperationContract]
		void RemoveFXTickets1(int ticketID, int modifyingUserId);

		[OperationContract]
		void UpdateFXTickets1(FXTickets1Info info, int modifyingUserId);

		#endregion FXTickets1

		#region FXTickets2

		[OperationContract]
		FXTickets2Info CreateNewFXTickets2();

		[OperationContract]
		FXTickets2Info GetFXTickets2(int ticketID, int tradeID);

		[OperationContract]
		List<FXTickets2Info> GetAllFXTickets2(List<int> ticketIDs);

		[OperationContract]
		void AddFXTickets2(FXTickets2Info info);

		[OperationContract]
		void UpdateFXTickets2(FXTickets2Info info);

		#endregion FXTickets2

		#region FXPendingTickets1

		[OperationContract]
		FXPendingTickets1Info CreateNewFXPendingTickets1();
		[OperationContract]
		FXPendingTickets1Info GetFXPendingTickets1(int iD, int ticketID);
		[OperationContract]
		void AddFXPendingTickets1(FXPendingTickets1Info info, int modifyingUserId);
		[OperationContract]
		void RemoveFXPendingTickets1(int iD, int ticketID, int modifyingUserId);
		[OperationContract]
		void UpdateFXPendingTickets1(FXPendingTickets1Info info, int modifyingUserId);

		#endregion FXPendingTickets1

		#region FXPendingTickets2

		[OperationContract]
		FXPendingTickets2Info CreateNewFXPendingTickets2();
		[OperationContract]
		FXPendingTickets2Info GetFXPendingTickets2(int iD, int ticketID, int tradeID);
		[OperationContract]
		void AddFXPendingTickets2(FXPendingTickets2Info info);
		[OperationContract]
		void UpdateFXPendingTickets2(FXPendingTickets2Info info);

		#endregion FXPendingTickets2

		#region FXInventory

		[OperationContract]
		FXInventoryInfo CreateNewFXInventory();

		[OperationContract]
		FXInventoryInfo AddFXInventory(FXInventoryInfo info, int modifyingUserId);

		[OperationContract]
		void RemoveFXInventory(int inventoryID, int modifyingUserId);

		[OperationContract]
		void UpdateFXInventory(FXInventoryInfo info, int modifyingUserId);

		#endregion FXInventory

		#region FXTicket1 Customer Transaction Information

		[OperationContract]
		FXTickets1CustomerTransactionInfo GetFXTickets1CustomerTransaction(int ticketID);

		[OperationContract]
		void AddFXTickets1CustomerTransaction(FXTickets1CustomerTransactionInfo info);

		[OperationContract]
		void RemoveFXTickets1CustomerTransaction(int ticketID);

		[OperationContract]
		void UpdateFXTickets1CustomerTransaction(FXTickets1CustomerTransactionInfo info);


		#endregion FXTicket1 Customer Transaction Information

		#region FXOfacTicketNotes

		[OperationContract]
		FXOfacTicketNotesInfo GetFXOfacTicketNotes(int ticketID, int noteID);

		[OperationContract]
		FXOfacTicketNotesInfo AddFXOfacTicketNotes(FXOfacTicketNotesInfo info);

		[OperationContract]
		void RemoveFXOfacTicketNotes(int ticketID, int noteID);

		[OperationContract]
		void UpdateFXOfacTicketNotes(FXOfacTicketNotesInfo info);

		#endregion FXOfacTicketNotes

		#region FXOFACTicket1

		[OperationContract]
		FXOfacTickets1Info GetFXOfacTickets1(int oFACID, int ticketID);

		#endregion FXOFACTicket1

		#region FXOfacTicket2

		[OperationContract]
		FXOfacTickets2Info GetFXOfacTickets2(int oFACID, int ticketID, int tradeID);

		[OperationContract]
		void AddFXOfacTickets2(FXOfacTickets2Info info, int modifyingUserId);

		#endregion

	}
}

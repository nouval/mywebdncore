using System;
using System.Collections.Generic;
using System.ServiceModel;

namespace CES.SL.RiaLink.Contract
{
	[ServiceContract]
	public interface IRiaLinkService
	{
		[OperationContract]
		bool IsAlive();

		[OperationContract]
		[FaultContract(typeof(CardInUseFault))]
		[FaultContract(typeof(CardUnavailableFault))]
		int EnrollTermCardFromInventory(TermCardInfo card, int modifiedById);

		[OperationContract]
		void UpdateTermCard(TermCardInfo card, int modifiedById);

		[OperationContract]
		TermCardInfo GetTermCardByCardId(int cardId);

		[OperationContract]
		TermCardInfo GetTermCardByCardNumber(string cardNumber);

		[OperationContract]
		TermCardInfo GetTermCardByCardNumberAndDoB(int appId, int appObjectId, int userId, string cardNumber, DateTime customerDoB, string agentCountry = "");

		[OperationContract]
		List<TermCardInfo> GetTermCardByCustomerIds(List<int> customerIds);

		[OperationContract]
		TermCardInventoryInfo GetTermCardByProcessorCardId(string processorCardId, string processor);
	}
}

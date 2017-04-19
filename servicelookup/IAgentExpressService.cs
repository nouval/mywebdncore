#region NameSpaces

using System.Collections.Generic;
using System.ServiceModel;

#endregion

namespace CES.SL.Agents.Contract
{
	/// <summary>
	/// Created Date: 5-Aug-2010
	/// Created By: Harish GDT-India
	/// Description: This was created to avoid merge conflicts when using shared services
	/// each team can have their own different files to handle
	/// </summary>

	[ServiceContract]
	public interface IAgentExpressService
	{
		#region Agent & default Country display Order
		[OperationContract]
		List<DefaultCountrySetting> GetDefaultCountryListOrder(string agentNumber);
		[OperationContract]
		List<DefaultCountrySetting> GetAddCountryList(string abbrevOrCountryFrom);
		[OperationContract]
		bool IsValidAgent(string agentNumber);
		#endregion
	}
}
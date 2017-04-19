using System.ServiceModel;

namespace CES.SL.CrossSelling.Contract
{
	[ServiceContract]
	public interface ICrossSellingService
	{
		[OperationContract]
		bool IsAlive();

		[OperationContract]
		GetBannersResponse GetBanners(GetBannersRequest request);
	}
}

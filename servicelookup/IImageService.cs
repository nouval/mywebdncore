using System.Collections.Generic;
using System.ServiceModel;

namespace CES.SL.Images.Contract
{
	[ServiceContract]
	public interface IImageService
	{
		[OperationContract]
		bool IsAlive();

		[OperationContract]
		void AddImage(ImageInfo info);
		
		[OperationContract]
		void RemoveImage(int id);
	
		[OperationContract]
		void UpdateImage(ImageInfo info);
		
		[OperationContract]
		ImageInfo GetImage(int id);
		
		[OperationContract]
		List<ImageInfo> GetImages(int imageId, bool includeImage);
		
		[OperationContract]
		int CreateCustomerIdentificationImageKey(CustomerIdentificationImageIndex info);
		
		[OperationContract]
		int CreateCustomerIdentificationImage(CustomerIdentificationImageInfo imageInfo);

		[OperationContract]
		ImageIDInfo CreateCustomerIdentificationImageV2(CustomerIdentificationImageInfo imageInfo);
	}
}

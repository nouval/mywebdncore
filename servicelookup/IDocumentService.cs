using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.ServiceModel;
using CES.FxCommon.Pagination;

namespace CES.SL.Documents.Contract
{
	[ServiceContract]
	public interface IDocumentService
	{
		[OperationContract]
		bool IsAlive();

		#region DocumentCategory

		[OperationContract]
		DocumentCategoryInfo CreateNewDocumentCategory();
		[OperationContract]
		void AddDocumentCategory(DocumentCategoryInfo info);
		[OperationContract]
		List<DocumentCategoryInfo> GetAllDocumentCategories();

		#endregion DocumentCategory

		#region DocumentLanguage

		[OperationContract]
		DocumentLanguageInfo CreateNewDocumentLanguage();
		[OperationContract]
		List<DocumentLanguageInfo> GetAllDocumentLanguages();
		[OperationContract]
		void AddDocumentLanguage(DocumentLanguageInfo info);

		#endregion DocumentLanguage

		#region DocumentFiles

		[OperationContract]
		DocumentFilesInfo CreateNewDocumentFiles();
		[OperationContract]
		DocumentFilesInfo GetDocumentFile(int fileID);
        [OperationContract]
        DocumentFilesInfo GetBinaryDataOfFile(int fileID);
		[OperationContract]
		int AddDocumentFiles(DocumentFilesInfo info);
		[OperationContract]
		List<DocumentFilesInfo> GetLatest5Documents();
		[OperationContract]
		List<DocumentFilesInfo> SearchDocumentsWithCategoryLanguage(string searchFilter, string category, string language);
		[OperationContract]
		List<DocumentFilesInfo> SearchDocumentsByFileNameMatch(string fileNameMatch, bool ommitImageData);

        [OperationContract]
        List<DocumentFilesInfo> Get5LatestAgentDocuments(int agentId, bool isForRecentDocs, string locale);
       
        [OperationContract]
        List<DocumentFilesInfo> SearchAgentDocumentsWithCategoryLanguage(int agentId, string searchFilter, string category,
                                                                         string language, bool isForRecentDocs, PagingOptions options);
        
		
		#endregion DocumentFiles

        #region Quick Order Help And Tips
        [OperationContract]
        List<HelpAndTipsInfo> GetHelpAndTips();
        #endregion
	}
}

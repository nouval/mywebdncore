using System;
using System.Net.Http;

namespace ces.coreapi.sdk
{
    public class RestClientResult<TResponse>
    {
        public RestClientResult(TResponse content, HttpResponseMessage rawResponse)
        {
            this.Content = content;
            this.RawResponse = rawResponse;
        }

        public TResponse Content
        {
            get;
            set;
        }

        public HttpResponseMessage RawResponse
        {
            get;
            set;
        }
    }
}
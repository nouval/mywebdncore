using System;
using System.Threading.Tasks;
using System.Net.Http.Headers;
using System.Net.Http;

namespace ces.coreapi.sdk
{
    public static class RestClient
    {
        public static RestClientResult<TResponse> Get<TResponse>(Uri uri)
        {
            var result = RestClient.GetAsync<TResponse>(uri);

            return result.Result;
        }

        public static async Task<RestClientResult<TResponse>> GetAsync<TResponse>(Uri uri)
        {
            using (var client = new HttpClient())
            {
                client.BaseAddress = uri;
                client.DefaultRequestHeaders.Accept.Clear();
                client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

                HttpResponseMessage response = await client.GetAsync("");
                TResponse result = default(TResponse);

                if (response.IsSuccessStatusCode)
                {
                    // var stream = await response.Content.ReadAsStreamAsync();

                    // var serializer = new DataContractJsonSerializer(typeof(TResponse));
                    // result = serializer.ReadObject(stream) as TResponse;                    
                }

                return new RestClientResult<TResponse>(result, response);
            }
        } 

        public static RestClientResult<string> Get(Uri uri)
        {
            var result = GetAsync(uri);

            return result.Result;
        }

        public static async Task<RestClientResult<string>> GetAsync(Uri uri)
        {
            using (var client = new HttpClient())
            {
                client.BaseAddress = uri;
                client.DefaultRequestHeaders.Accept.Clear();
                client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

                HttpResponseMessage response = await client.GetAsync("");
                string result = null;

                if (response.IsSuccessStatusCode)
                {                    
                    result = await response.Content.ReadAsStringAsync();
                }

                return new RestClientResult<string>(result, response);
            }
        }

        public static RestClientResult<TResponse> Post<TRequest, TResponse>(Uri uri, TRequest content)
        {
            var result = RestClient.PostAsync<TRequest, TResponse>(uri, content);

            return result.Result;            
        }

        public static async Task<RestClientResult<TResponse>> PostAsync<TRequest, TResponse>(Uri uri, TRequest content)
        {
            throw new NotImplementedException();
        }

        public static RestClientResult<int> Post(Uri uri, string content)
        {
            var result = RestClient.PostAsync(uri, content);

            return result.Result;
        }

        public static async Task<RestClientResult<int>> PostAsync(Uri uri, string content)
        {
            using (var client = new HttpClient())
            {
                client.BaseAddress = uri;
                client.DefaultRequestHeaders.Accept.Clear();
                client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

	            var stringContent = new StringContent(content, System.Text.Encoding.UTF8, "application/json");
                HttpResponseMessage response = await client.PostAsync("", stringContent);
                int result = 0;

                if (response.IsSuccessStatusCode)
                {
                    result = 1;
                }

                return new RestClientResult<int>(result, response);
            }            
        }

        public static RestClientResult<TResponse> Put<TRequest, TResponse>(Uri uri, TRequest content)
        {
            var result = RestClient.PutAsync<TRequest, TResponse>(uri, content);

            return result.Result;            
        }

        public static async Task<RestClientResult<TResponse>> PutAsync<TRequest, TResponse>(Uri uri, TRequest content)
        {
            throw new NotImplementedException();
        }

        public static RestClientResult<int> Put(Uri uri, string content)
        {
            var result = RestClient.PutAsync(uri, content);

            return result.Result;
        }

        public static async Task<RestClientResult<int>> PutAsync(Uri uri, string content)
        {
            using (var client = new HttpClient())
            {
                client.BaseAddress = uri;
                client.DefaultRequestHeaders.Accept.Clear();
                client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

	            var stringContent = new StringContent(content, System.Text.Encoding.UTF8, "application/json");
                HttpResponseMessage response = await client.PutAsync("", stringContent);
                int result = 0;

                if (response.IsSuccessStatusCode)
                {
                    result = 1;
                }

                return new RestClientResult<int>(result, response);
            }            
        }

        public static RestClientResult<TResponse> Delete<TResponse>(Uri uri)
        {
            var result = RestClient.DeleteAsync<TResponse>(uri);

            return result.Result;            
        }

        public static async Task<RestClientResult<TResponse>> DeleteAsync<TResponse>(Uri uri)
        {
            throw new NotImplementedException();
        }

        public static RestClientResult<int> Delete(Uri uri)
        {
            var result = RestClient.DeleteAsync(uri);

            return result.Result;
        }

        public static async Task<RestClientResult<int>> DeleteAsync(Uri uri)
        {
            using (var client = new HttpClient())
            {
                client.BaseAddress = uri;
                client.DefaultRequestHeaders.Accept.Clear();
                client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

                HttpResponseMessage response = await client.DeleteAsync("");
                int result = 0;

                if (response.IsSuccessStatusCode)
                {
                    result = 1;
                }

                return new RestClientResult<int>(result, response);
            }            
        }                
    }
}
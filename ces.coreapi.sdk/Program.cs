using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using System.Net.Http;

namespace ces.coreapi.sdk
{
    public class Program
    {
        public static async Task LoadSite() 
        {
            // call www.google.com via HttpClient
            using (var client = new HttpClient()) 
            {
                client.BaseAddress = new Uri(@"http://www.google.com");

                var response = await client.GetAsync("/");
                if (response.IsSuccessStatusCode)
                {
                    Console.WriteLine(response);
                }
            }
        }

        public static async Task AsyncTask()
        {
            // spec: get
            // var restClient = new RestClient(new Uri(@"http://localhost:5000"));
            var result = await RestClient.GetAsync(new Uri(@"http://localhost:5000/api/values/"));
            Console.WriteLine(result.Content);

            var resultSync = RestClient.Get(new Uri(@"http://localhost:5000/api/values/1"));
            Console.WriteLine(resultSync.Content);

            var resultAsValue = await RestClient.GetAsync<ValueData>(new Uri(@"http://localhost:5000//api/values/2"));
            Console.WriteLine(resultAsValue.Content);

            var resultPost =  RestClient.Post(new Uri(@"http://localhost:5000/api/values"), "{\"id\": 3,\"value\": \"tiga\"}");
            Console.WriteLine(resultPost.Content);

            var resultPut =  RestClient.Put(new Uri(@"http://localhost:5000/api/values/3"), "{\"value\": \"3-on-3\"}");
            Console.WriteLine(resultPut.Content);            

            var resultDelete =  RestClient.Delete(new Uri(@"http://localhost:5000/api/values/3"));
            Console.WriteLine(resultDelete.Content);
        }

        public static void Main(string[] args)
        {
            Program.AsyncTask().Wait();

            // Program.LoadSite().Wait();
            Console.WriteLine("boo yah");
        }
    }
}

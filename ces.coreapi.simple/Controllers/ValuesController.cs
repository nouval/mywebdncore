using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;

namespace ces.coreapi.simple.Controllers
{
    public class RequestData
    {
        public int Id;
        public string Value;
    }    

    [Route("api/[controller]")]
    public class ValuesController : Controller
    {
        private static Dictionary<int, object> hashtable = new Dictionary<int, object>();

        // GET api/values
        [HttpGet]
        public IEnumerable<RequestData> Get()
        {
            var results = new List<RequestData>();

            foreach(var item in hashtable) {
                results.Add(new RequestData() {
                    Id = item.Key,
                    Value = item.Value as String
                });
            }

            return results.ToArray();
        }

        // GET api/values/5
        [HttpGet("{id}")]
        public RequestData Get(int id)
        {
            return new RequestData()
            {
                Id = id,
                Value = hashtable[id].ToString()
            };
        }

        // POST api/values
        [HttpPost]
        public void Post([FromBody]RequestData reqData)
        {
            hashtable[reqData.Id] = reqData.Value;
        }

        // PUT api/values/5
        [HttpPut("{id}")]
        public void Put(int id, [FromBody]RequestData reqData)
        {
            hashtable[id] = reqData.Value;
        }

        // DELETE api/values/5
        [HttpDelete("{id}")]
        public void Delete(int id)
        {
            hashtable.Remove(id);
        }
    }
}

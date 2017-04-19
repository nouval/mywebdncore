using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Ria.Logging
{
    public class Log4NetLegacyLogEvent
    {
        public object Message { get; set; }
        public ICollection<string> Categories { get; set; }
        public int Priority { get; set; }
        public int EventId { get; set; }
        public TraceEventType Severity { get; set; }
        public string Title { get; set; }
        public IDictionary<string, object> Properties { get; set; }
        /// <summary>
        /// Formatting logging event to match current MS logging framework.
        /// </summary>
        /// <returns></returns>
        public override string ToString()
        {
            var categories = "";
            if (this.Categories != null)
            {
                this.Categories.ToList().ForEach(cat => categories += "-" + cat);
            }
            return string.Format("Categories={0} Priority={1} EventId={2} Message={3}", categories.Substring(1), this.Priority, this.EventId, this.Message as string);
        }
    }
}
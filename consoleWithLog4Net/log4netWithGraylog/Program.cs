using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

using log4net;
using log4net.Config;

namespace log4netWithGraylog
{
    public class Program
    {
        private static readonly log4net.ILog log = LogManager.GetLogger(typeof(Program));
        
        
        public static void Main(string[] args)
        {
            Console.WriteLine("Hello World");
            Console.Read();
        }
    }
}

using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Ria.Logging
{
    // This project can output the Class library as a NuGet Package.
    // To enable this option, right-click on the project and select the Properties menu item. In the Build tab select "Produce outputs on build".
    public static class Log
	{
        private readonly Logger logger;
        private static readonly Lazy<Log> thisInstance = new Lazy<Log>() => new Log(new Log4NetLogger());

        private Log(Logger logger) {
            this.logger = logger;
        }

        /// <summary>
        /// logs entry at debug level, mostly used in development stage
        /// </summary>
        /// <param name="message"></param>
        public static void Debug(object message) {
            thisInstance.Value.logger.Debug(message);
        }

        /// <summary>
        /// logs error entry, used mostly at handled exception and no impact to system performance
        /// </summary>
        /// <param name="message">log message</param>
        public static void Error(string message) {
            thisInstance.Value.logger.Error(message);
        }

        /// <summary>
        /// logs error entry, used mostly at handled exception and no impact to system performance
        /// </summary>
        /// <param name="exception">exception thrown</param>
        public static void Error(Exception exception) {
            thisInstance.Value.logger.Error(exception);
        }

        /// <summary>
        /// logs error entry, used mostly at handled exception and no impact to system performance
        /// </summary>
        /// <param name="message">human readable message</param>
        /// <param name="exception">exception thrown</param>
        public static void Error(object message, Exception exception) {
            thisInstance.Value.logger.Error(message, exception);
        }

        /// <summary>
        /// logs informational entry, used mostly at information completion of a process, duration taken etc. 
        /// </summary>
        /// <param name="message">human readable message</param>
        public static void Info(object message) {
            thisInstance.Value.logger.Info(message);
        }

        /// <summary>
        /// logs actionable entry, used mostly at informating user that system is experiancing unusual data, process load etc.
        /// requiring operational team to watch and monitoring system closely.
        /// </summary>
        /// <param name="message"></param>
        public static void Warning(object message) {
            thisInstance.Value.logger.Warning(message);
        }

        /// <summary>
        /// logs error entry, used mostly at system of application is crashed
        /// </summary>
        /// <param name="message"></param>
        public static void Fatal(string message) {
            thisInstance.Value.logger.Fatal(message);
        }

        /// <summary>
        /// logs error entry, used mostly at system of application is crashed
        /// </summary>
        /// <param name="exception"></param>
        public static void Fatal(Exception exception) {
            thisInstance.Value.logger.Fatal(exception);
        }

        /// <summary>
        /// logs error entry, used mostly at system of application is crashed
        /// </summary>
        /// <param name="message"></param>
        /// <param name="exception"></param>
        public static void Fatal(object message, Exception exception) {
            thisInstance.Value.logger.Fatal(message, exception);
        }

        /// <summary>
        /// Info level logging to support Microsoft's Logging. Only use this for backward compatibility only, use other Info method
        /// </summary>
        /// <param name="message"></param>
        /// <param name="category"></param>
        /// <param name="priority"></param>
        /// <param name="eventId"></param>
        /// <param name="severity"></param>
        /// <param name="title"></param>
        /// <param name="properties"></param>
        [Obsolete("This method was added to support legacy logging via Microsoft Best Practice Logging, use regular Info", false)]
        public static void Info(object message, string category, int priority, int eventId, TraceEventType severity, string title, IDictionary<string, object> properties) {
            thisInstance.Value.logger.Fatal(message, category, priority, eventId, severity, title, properties);
        }

        /// <summary>
        /// Info level logging to support Microsoft's Logging. Only use this for backward compatibility only, use other Info method
        /// </summary>
        /// <param name="message"></param>
        /// <param name="categories"></param>
        /// <param name="priority"></param>
        /// <param name="eventId"></param>
        /// <param name="severity"></param>
        /// <param name="title"></param>
        /// <param name="properties"></param>
        [Obsolete("This method was added to support legacy logging via Microsoft Best Practice Logging, use regular Info", false)]
        public static void Info(object Message, ICollection<string> categories, int priority, int eventId, TraceEventType severity, string title, IDictionary<string, object> properties) {
            thisInstance.Value.logger.Fatal(message, categories, priority, eventId, severity, title, properties);
        }
	}
}

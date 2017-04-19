using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Ria.Logging
{
    // This project can output the Class library as a NuGet Package.
    // To enable this option, right-click on the project and select the Properties menu item. In the Build tab select "Produce outputs on build".
    public interface Logger
	{
        /// <summary>
        /// Returns true if this logger is enabled
        /// </summary>
        bool Enabled;

        /// <summary>
        /// logs entry at debug level, mostly used in development stage
        /// </summary>
        /// <param name="message"></param>
        void Debug(object message);

        /// <summary>
        /// logs error entry, used mostly at handled exception and no impact to system performance
        /// </summary>
        /// <param name="message">log message</param>
        void Error(string message);

        /// <summary>
        /// logs error entry, used mostly at handled exception and no impact to system performance
        /// </summary>
        /// <param name="exception">exception thrown</param>
        void Error(Exception exception);

        /// <summary>
        /// logs error entry, used mostly at handled exception and no impact to system performance
        /// </summary>
        /// <param name="message">human readable message</param>
        /// <param name="exception">exception thrown</param>
        void Error(object message, Exception exception);

        /// <summary>
        /// logs informational entry, used mostly at information completion of a process, duration taken etc. 
        /// </summary>
        /// <param name="message">human readable message</param>
        void Info(object message);

        /// <summary>
        /// logs actionable entry, used mostly at informating user that system is experiancing unusual data, process load etc.
        /// requiring operational team to watch and monitoring system closely.
        /// </summary>
        /// <param name="message"></param>
        void Warning(object message);

        /// <summary>
        /// logs error entry, used mostly at system of application is crashed
        /// </summary>
        /// <param name="message"></param>
        void Fatal(string message);

        /// <summary>
        /// logs error entry, used mostly at system of application is crashed
        /// </summary>
        /// <param name="exception"></param>
        void Fatal(Exception exception);

        /// <summary>
        /// logs error entry, used mostly at system of application is crashed
        /// </summary>
        /// <param name="message"></param>
        /// <param name="exception"></param>
        void Fatal(object message, Exception exception);

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
        void Info(object message, string category, int priority, int eventId, TraceEventType severity, string title, IDictionary<string, object> properties);

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
        void Info(object message, ICollection<string> categories, int priority, int eventId, TraceEventType severity, string title, IDictionary<string, object> properties);
	}
}

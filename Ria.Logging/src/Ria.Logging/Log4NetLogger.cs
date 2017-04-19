using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;

namespace Ria.Logging
{
    // This project can output the Class library as a NuGet Package.
    // To enable this option, right-click on the project and select the Properties menu item. In the Build tab select "Produce outputs on build".
    public class Log4NetLogger : Logger
    {
        private static readonly ILog logger = LogManager.GetLogger(MethodBase.GetCurrentMethod().DeclaringType);

        /// <summary>
        /// default c'tor, is hidden since this a singleton class.
        /// </summary>
        public Log4NetLogger()
        {
            XmlConfigurator.Configure();
        }

        public bool Enabled
        {
            get { return log4net.LogManager.GetRepository().Configured;  }
        }

        public void Debug(object message)
        {
            this.log(Level.Debug, message, DateTime.Now);
        }

        public void Error(Exception exception)
        {
            this.log(Level.Error, ExceptionFormatHelper.FormatException(exception), DateTime.Now);
        }

        public void Error(string message)
        {
            this.log(Level.Error, message, DateTime.Now);
        }

        public void Error(object message, Exception exception)
        {
            this.log(Level.Error, message, DateTime.Now, exception);
        }

        public void Fatal(Exception exception)
        {
            this.log(Level.Fatal, ExceptionFormatHelper.FormatException(exception), DateTime.Now);
        }

        public void Fatal(string message)
        {
            this.log(Level.Fatal, message, DateTime.Now);
        }

        public void Fatal(object message, Exception exception)
        {
            this.log(Level.Fatal, message, DateTime.Now, exception);
        }

        public void Info(object message)
        {
            this.log(Level.Info, message, DateTime.Now);
        }

        [Obsolete("This method was added to support legacy logging via Microsoft Best Practice Logging, use regular Info", false)]
        public void Info(object message, ICollection<string> categories, int priority, int eventId, TraceEventType severity, string title, IDictionary<string, object> properties)
        {
            this.log(
                Level.Info,
                new Log4NetLegacyLogEvent()
                {
                    Message = Message,
                    Categories = Categories,
                    Priority = Priority,
                    EventId = EventId,
                    Severity = Severity,
                    Title = Title,
                    Properties = Properties
                }, 
                DateTime.Now);
        }

        [Obsolete("This method was added to support legacy logging via Microsoft Best Practice Logging, use regular Info", false)]
        public void Info(object message, string category, int priority, int eventId, TraceEventType severity, string title, IDictionary<string, object> properties)
        {
            this.log(
                Level.Info,
                new Log4NetLegacyLogEvent()
                {
                    Message = Message,
                    Categories = new List<string>() { Category },
                    Priority = Priority,
                    EventId = EventId,
                    Severity = Severity,
                    Title = Title,
                    Properties = Properties
                },
                DateTime.Now);
        }

        public void Warning(object message)
        {
            this.log(Level.Warn, message, DateTime.Now);
        }

        private void log(Level logLevel, object message, DateTime timestamp, Exception exception = null, IDictionary<string, object> Properties = null)
        {
            // only continue log process, if logger is enabled for given logLevel.
            if (!logger.Logger.IsEnabledFor(logLevel))
                return;

            // since this is a log4net wrapper class, need to fetch call stack and prepare calling method, class and line number
            var stackTrace = new StackTrace(true);
            var frame = stackTrace.GetFrame(2);
            var callingMethod = frame.GetMethod();
            var callingType = frame.GetMethod().ReflectedType;

            Task.Run(() =>
            {
                var loggingEventData = new LoggingEventData()
                {
                    Domain = (AppDomain.CurrentDomain != null) ? AppDomain.CurrentDomain.FriendlyName : null,
                    Identity = (Thread.CurrentPrincipal != null && Thread.CurrentPrincipal.Identity != null) ? Thread.CurrentPrincipal.Identity.Name : null,
                    Level = logLevel,
                    LocationInfo = new LocationInfo(callingType.FullName, callingMethod.Name, frame.GetFileName(), frame.GetFileLineNumber().ToString()),
                    LoggerName = callingType.Name,
                    Message = message.ToString(),
                    TimeStamp = timestamp,
                    UserName = (Thread.CurrentPrincipal != null && Thread.CurrentPrincipal.Identity != null) ? Thread.CurrentPrincipal.Identity.Name : null,
                    ExceptionString = (exception != null) ? ExceptionFormatHelper.FormatException(exception) : null,
                    Properties = new log4net.Util.PropertiesDictionary()
                };

                // setup additional properties, if not NULL
                if (Properties != null)
                {
                    foreach (var property in Properties)
                    {
                        loggingEventData.Properties[property.Key] = property.Value;
                    }
                }

                // to support legacy, we need to stuff message into properties bag
                if (message is Log4NetLegacyLogEvent)
                {
                    loggingEventData.Properties["Log4NetLegacyLogEvent"] = message;
                }

                logger.Logger.Log(new LoggingEvent(loggingEventData));
            });
        }
    }
}

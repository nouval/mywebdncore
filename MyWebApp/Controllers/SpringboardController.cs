using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNet.Mvc;

namespace MyWebApp.Controllers
{
    public class SpringboardController : Controller
    {
        public IActionResult Index()
        {
            return View();
        }
    }
}

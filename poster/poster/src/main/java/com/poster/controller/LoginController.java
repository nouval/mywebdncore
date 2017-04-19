package com.poster.controller;

import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.servlet.ModelAndView;

@Controller
public class LoginController {

	@RequestMapping(path = "/login", method = RequestMethod.GET)
	public String index() {
		return "login";
	}
	
	@RequestMapping(path = "/login", method = RequestMethod.POST)
	public ModelAndView index(
			@RequestParam(value = "username", required = true) String username,
			@RequestParam(value = "password", required = true) String password) {
		
		ModelAndView result = null;
		
		if (username == "foo" && password == "bar") {
			result = new ModelAndView("wall");
			
		} else {
			result = new ModelAndView("login");
			result.addObject("errorMessage", "Invalid Login and Password");
		}
		
		return result;
	}
}

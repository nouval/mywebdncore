<%@ page language="java" contentType="text/html; charset=UTF-8"
    pageEncoding="UTF-8"%>
<%@ taglib uri="http://java.sun.com/jsp/jstl/core" prefix="c" %>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<body>
	<form name="f" action="/poster/login" method="post">
		<label>Username:</label><input type="text" name="username"/><br/>
		<label>Password:</label><input type="password" name="password"/><br/>
		<c:if test="${not empty errorMessage}">
		<bold>${errorMessage}</bold>a
		</c:if>
		<input type="submit"/> <a href="/poster/signup">Signup</a>
	</form>
</body>
</html>
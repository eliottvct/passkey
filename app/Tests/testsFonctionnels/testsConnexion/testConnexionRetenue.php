<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head profile="http://selenium-ide.openqa.org/profiles/test-case">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="selenium.base" href="http://localhost/" />
<title>connexionRetenue</title>
</head>
<body>
<table cellpadding="1" cellspacing="1" border="1">
<thead>
<tr><td rowspan="1" colspan="3">connexionRetenue</td></tr>
</thead><tbody>
<tr>
	<td>open</td>
	<td>/passkey/?action=loginPage&amp;url=/passkey/</td>
	<td></td>
</tr>
<tr>
	<td>type</td>
	<td>name=username</td>
	<td>lluke</td>
</tr>
<tr>
	<td>type</td>
	<td>name=password</td>
	<td>lluke</td>
</tr>
<tr>
	<td>click</td>
	<td>name=remember</td>
	<td></td>
</tr>
<tr>
	<td>clickAndWait</td>
	<td>//button[@type='submit']</td>
	<td></td>
</tr>
<tr>
	<td>assertLocation</td>
	<td>*localhost/passkey/</td>
	<td></td>
</tr>

</tbody></table>
</body>
</html>

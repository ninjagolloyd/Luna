<?php

session_start();

if(!isset($_SESSION['luna_install_check']))
	die('Installation access denied.');

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Install Luna</title>
        <link href="../include/css/bootstrap.min.css" rel="stylesheet">
        <link href="install.css" rel="stylesheet">
    </head>
    <body class="default">
        <div class="site-wrapper">
            <div class="site-wrapper-inner">
                <div class="cover-container">
                    <div class="masthead clearfix">
						<h3 class="masthead-brand"><span class="luna-brand">Luna</span>Terms<span class="visible-xs-block"></span><span class="luna-brand">&amp;</span>Lincense</h3>
                    </div>
                    <div class="inner cover">
<textarea class="form-control" rows="20" readonly>LICENSE FOR LUNA MINUS SUNRISE AND LUNA SETUP &amp; UPDATE

Copyright &copy; 2013-2014 Luna Group

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see <http://www.gnu.org/licenses/>.

LICENSE FOR SUNRISE AND LUNA SETUP &amp; UPDATE

Copyright &copy; 2013-2014 Luna Group

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

LICENSE FOR COMPONENTS

Components used in Luna might be released under other licenses. Bootstrap, jQuery and Font Awesome are not part of the Luna Group but developed by third parties. Released, respectively, under the MIT, GPLv2 and MIT license. Please visit the developer's website for more information.</textarea><br />
                        <p class="lead">
                            <a href="setup.php" class="btn btn-lg btn-default">Agree</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <script src="../include/js/jquery.js"></script>
        <script src="../include/js/bootstrap.min.js"></script>
    </body>
</html>
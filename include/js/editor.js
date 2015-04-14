/*
WYSIWYG-BBCODE editor
Copyright (c) 2009, Jitbit Sotware, http://www.jitbit.com/
PROJECT HOME: http://wysiwygbbcode.codeplex.com/
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
	* Redistributions of source code must retain the above copyright
	  notice, this list of conditions and the following disclaimer.
	* Redistributions in binary form must reproduce the above copyright
	  notice, this list of conditions and the following disclaimer in the
	  documentation and/or other materials provided with the distribution.
	* Neither the name of the <organization> nor the
	  names of its contributors may be used to endorse or promote products
	  derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY Jitbit Software ''AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL Jitbit Software BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/
var wswgEditor = new function () {

	this.getEditorDoc = function () { return myeditor; }
	this.getIframe = function () { return ifm; }
	this.IsEditorVisible = function () { return editorVisible; }

	var myeditor, ifm;
	var body_id, textboxelement;
	var content;
	var isIE = /msie|MSIE/.test(navigator.userAgent);
	var isChrome = /Chrome/.test(navigator.userAgent);
	var isSafari = /Safari/.test(navigator.userAgent) && !isChrome;
	var browser = isIE || window.opera;
	var textRange;
	var editorVisible = false;
	var enableWysiwyg = false;

	function rep(re, str) {
		content = content.replace(re, str);
	}

	this.initEditor = function (textarea_id, wysiwyg, css_uri) {
	  if (css_uri) {
	    this.css_uri = css_uri;
	  } else {
	    this.css_uri = "editor.css";
	  }
		if (wysiwyg != undefined)
			enableWysiwyg = wysiwyg;
		else
			enableWysiwyg = true;
		body_id = textarea_id;
		textboxelement = document.getElementById(body_id);
		textboxelement.setAttribute('class', 'form-control editorBBCODE');
		textboxelement.className = "form-control editorBBCODE";
		if (enableWysiwyg) {
			if (!document.getElementById("rte")) { //to prevent recreation
				ifm = document.createElement("iframe");
				ifm.setAttribute("id", "rte");
				ifm.setAttribute("frameBorder", "0");
				ifm.style.width = textboxelement.style.width;
				ifm.style.height = textboxelement.style.height;
				textboxelement.parentNode.insertBefore(ifm, textboxelement);
				textboxelement.style.display = 'none';
			}
			if (ifm) {
				this.ShowEditor();
			} else
				setTimeout('this.ShowEditor()', 100);
		}
	}

	function getStyle(el, styleProp) {
		var x = document.getElementById(el);
		if (x.currentStyle)
			var y = x.currentStyle[styleProp];
		else if (window.getComputedStyle)
			var y = document.defaultView.getComputedStyle(x, null).getPropertyValue(styleProp);
		return y;
	}

	this.ShowEditor = function () {
		if (!enableWysiwyg) return;
		editorVisible = true;
		content = document.getElementById(body_id).value;
		myeditor = ifm.contentWindow.document;
		bbcode2html();
		myeditor.designMode = "on";
		myeditor.open();
		myeditor.write('<html><head><link href="'+this.css_uri+'" rel="Stylesheet" type="text/css" /></head>');
		myeditor.write('<body style="margin:0px 0px 0px 0px" class="editorWYSIWYG">');
		myeditor.write(content);
		myeditor.write('</body></html>');
		myeditor.close();
		myeditor.body.contentEditable = true;
		ifm.contentEditable = true;
		if (myeditor.attachEvent) {
			myeditor.attachEvent("onkeypress", kp);
		}
		else if (myeditor.addEventListener) {
			myeditor.addEventListener("keypress", kp, true);
		}
	}

	this.SwitchEditor = function () {
		if (editorVisible) {
			this.doCheck();
			ifm.style.display = 'none';
			textboxelement.style.display = '';
			editorVisible = false;
			textboxelement.focus();
		}
		else {
			if (enableWysiwyg && ifm) {
				ifm.style.display = '';
				textboxelement.style.display = 'none';
				this.ShowEditor();
				editorVisible = true;
				ifm.contentWindow.focus();
			}
		}
	}

	function html2bbcode() {
		rep(/<img\s[^<>]*?src=\"?([^<>]*?)\"?(\s[^<>]*)?\/?>/gi, "[img]$1[/img]");
		rep(/<\/(strong|b)>/gi, "[/b]");
		rep(/<(strong|b)(\s[^<>]*)?>/gi, "[b]");
		rep(/<\/(em|i)>/gi, "[/i]");
		rep(/<(em|i)(\s[^<>]*)?>/gi, "[i]");
		rep(/<\/u>/gi, "[/u]");
		rep(/\n/gi, " ");
		rep(/\r/gi, " ");
		rep(/<u(\s[^<>]*)?>/gi, "[u]");
		rep(/<div><br(\s[^<>]*)?>/gi, "<div>"); //chrome-safari fix to prevent double linefeeds
		rep(/<br(\s[^<>]*)?>/gi, "\n");
		rep(/<p(\s[^<>]*)?>/gi, "");
		rep(/<\/p>/gi, "\n");
		rep(/<ul>/gi, "[list]");
		rep(/<\/ul>/gi, "[/list]");
		rep(/<ol>/gi, "[ol]");
		rep(/<\/ol>/gi, "[/ol]");
		rep(/<li>/gi, "[\*]");
		rep(/<\/li>/gi, "[/\*]");
		rep(/<\/div>\s*<div([^<>]*)>/gi, "</span>\n<span$1>"); //chrome-safari fix to prevent double linefeeds
		rep(/<div([^<>]*)>/gi, "<span$1>");
		rep(/<\/div>/gi, "</span>");

		rep(/&nbsp;/gi, " ");
		rep(/&quot;/gi, "\"");
		rep(/&amp;/gi, "&");

		//remove style & script tags
		rep(/<script.*?>[\s\S]*?<\/script>/gi, "");
		rep(/<style.*?>[\s\S]*?<\/style>/gi, "");

		//remove [if] blocks (when pasted from outlook etc)
		rep(/<!--\[if[\s\S]*?<!\[endif\]-->/gi, "");

		var sc, sc2;
		do {
			sc = content;
			rep(/<font\s[^<>]*?color=\"?([^<>]*?)\"?(\s[^<>]*)?>([^<>]*?)<\/font>/gi, "[color=$1]$3[/color]");
			if (sc == content)
				rep(/<font[^<>]*>([^<>]*?)<\/font>/gi, "$1");
			rep(/<a\s[^<>]*?href=\"?([^<>]*?)\"?(\s[^<>]*)?>([^<>]*?)<\/a>/gi, "[url=$1]$3[/url]");
			sc2 = content;
			rep(/<(span|blockquote|pre)\s[^<>]*?style=\"?font-weight: ?bold;?\"?\s*([^<]*?)<\/\1>/gi, "[b]<$1 style=$2</$1>[/b]");
			rep(/<(span|blockquote|pre)\s[^<>]*?style=\"?font-weight: ?normal;?\"?\s*([^<]*?)<\/\1>/gi, "<$1 style=$2</$1>");
			rep(/<(span|blockquote|pre)\s[^<>]*?style=\"?font-style: ?italic;?\"?\s*([^<]*?)<\/\1>/gi, "[i]<$1 style=$2</$1>[/i]");
			rep(/<(span|blockquote|pre)\s[^<>]*?style=\"?font-style: ?normal;?\"?\s*([^<]*?)<\/\1>/gi, "<$1 style=$2</$1>");
			rep(/<(span|blockquote|pre)\s[^<>]*?style=\"?text-decoration: ?underline;?\"?\s*([^<]*?)<\/\1>/gi, "[u]<$1 style=$2</$1>[/u]");
			rep(/<(span|blockquote|pre)\s[^<>]*?style=\"?text-decoration: ?line-through;?\"?\s*([^<]*?)<\/\1>/gi, "[u]<$1 style=$2</$1>[/u]");
			rep(/<(span|blockquote|pre)\s[^<>]*?style=\"?text-decoration: ?none;?\"?\s*([^<]*?)<\/\1>/gi, "<$1 style=$2</$1>");
			rep(/<(blockquote|pre)\s[^<>]*?style=\"?\"? (class=|id=)([^<>]*)>([^<>]*?)<\/\1>/gi, "<$1 $2$3>$4</$1>");
			rep(/<pre>([^<>]*?)<\/pre>/gi, "[code]$1[/code]");
			rep(/<span\s[^<>]*?style=\"?\"?>([^<>]*?)<\/span>/gi, "$1");
			if (sc2 == content) {
				rep(/<span[^<>]*>([^<>]*?)<\/span>/gi, "$1");
				sc2 = content;
			}
		} while (sc != content)
		rep(/<[^<>]*>/gi, "");
		rep(/&lt;/gi, "<");
		rep(/&gt;/gi, ">");

		do {
			sc = content;
			rep(/\[(b|i|u|s)\]\[quote([^\]]*)\]([\s\S]*?)\[\/quote\]\[\/\1\]/gi, "[quote$2][$1]$3[/$1][/quote]");
			rep(/\[color=([^\]]*)\]\[quote([^\]]*)\]([\s\S]*?)\[\/quote\]\[\/color\]/gi, "[quote$2][color=$1]$3[/color][/quote]");
			rep(/\[(b|i|u|s)\]\[code\]([\s\S]*?)\[\/code\]\[\/\1\]/gi, "[code][$1]$2[/$1][/code]");
		} while (sc != content)

		//clean up empty tags
		do {
			sc = content;
			rep(/\[b\]\[\/b\]/gi, "");
			rep(/\[i\]\[\/i\]/gi, "");
			rep(/\[u\]\[\/u\]/gi, "");
			rep(/\[s\]\[\/s\]/gi, "");
			rep(/\[quote[^\]]*\]\[\/quote\]/gi, "");
			rep(/\[code\]\[\/code\]/gi, "");
			rep(/\[url=([^\]]+)\]\[\/url\]/gi, "");
			rep(/\[img\]\[\/img\]/gi, "");
		} while (sc != content)
	}

	function bbcode2html() {
		// example: [b] to <strong>
		rep(/\</gi, "&lt;"); //removing html tags
		rep(/\>/gi, "&gt;");

		rep(/\n/gi, "<br />");
		rep(/\[list\]/gi, "<ul>");
		rep(/\[\/list\]/gi, "</ul>");
		rep(/\[ol\]/gi, "<ol>");
		rep(/\[\/ol\]/gi, "</ol>");
		rep(/\[\*\]/gi, "<li>");
		rep(/\[\/\*\]/gi, "</li>");

		if (browser) {
			rep(/\[b\]/gi, "<strong>");
			rep(/\[\/b\]/gi, "</strong>");
			rep(/\[i\]/gi, "<em>");
			rep(/\[\/i\]/gi, "</em>");
			rep(/\[u\]/gi, "<u>");
			rep(/\[\/u\]/gi, "</u>");
			rep(/\[s\]/gi, "<span style=\"text-decoration: line-through;\">");
			rep(/\[\/s\]/gi, "</span>");
		} else {
			rep(/\[b\]/gi, "<span style=\"font-weight: bold;\">");
			rep(/\[i\]/gi, "<span style=\"font-style: italic;\">");
			rep(/\[u\]/gi, "<span style=\"text-decoration: underline;\">");
			rep(/\[s\]/gi, "<span style=\"text-decoration: line-through;\">");
			rep(/\[\/(b|i|u|s)\]/gi, "</span>");
		}
		rep(/\[img\]([^\"]*?)\[\/img\]/gi, "<img src=\"$1\" />");
		var sc;
		do {
			sc = content;
			rep(/\[url=([^\]]+)\]([\s\S]*?)\[\/url\]/gi, "<a href=\"$1\">$2</a>");
			rep(/\[url\]([\s\S]*?)\[\/url\]/gi, "<a href=\"$1\">$1</a>");
			rep(/\[code\]([\s\S]*?)\[\/code\]/gi, "<pre>$1</pre>&nbsp;");
		} while (sc != content);
	}

	this.doCheck = function () {
		if (!enableWysiwyg) return;
		if (!editorVisible) {
			this.ShowEditor();
		}
		content = myeditor.body.innerHTML;
		html2bbcode();
		document.getElementById(body_id).value = content;
	}

	function stopEvent(evt) {
		evt || window.event;
		if (evt.stopPropagation) {
			evt.stopPropagation();
			evt.preventDefault();
		} else if (typeof evt.cancelBubble != "undefined") {
			evt.cancelBubble = true;
			evt.returnValue = false;
		}
		return false;
	}

	this.doQuote = function () {
		if (editorVisible) {
			ifm.contentWindow.focus();
			if (isIE) {
				textRange = ifm.contentWindow.document.selection.createRange();
				var newTxt = "[quote]" + textRange.text + "[/quote]";
				textRange.text = newTxt;
			}
			else {
				var edittext = ifm.contentWindow.getSelection().getRangeAt(0);
				var original = edittext.toString();
				edittext.deleteContents();
				edittext.insertNode(ifm.contentWindow.document.createTextNode("[quote]" + original + "[/quote]"));
			}
		}
		else {
			AddTag('[quote=]', '[/quote]');
		}
	}

	function kp(e) {
		if (isIE) {
			if (e.keyCode == 13) {
				var r = myeditor.selection.createRange();
				if (r.parentElement().tagName.toLowerCase() != "li") {
					r.pasteHTML('<br/>');
					if (r.move('character'))
						r.move('character', -1);
					r.select();
					stopEvent(e);
					return false;
				}
			}
		}
	}
	this.InsertYoutube = function () {
		var video_code = prompt("Introduzca el código de video de youtube:", "_hwQaOpscso&rel=1");
	  this.InsertText(" [youtube]"+video_code+"[/youtube] ");
	}
	
	this.InsertText = function (txt) {
		if (editorVisible)
			insertHtml(txt);
		else
			textboxelement.value += txt;
	}

	this.doClick = function (command,extra) {
		if (editorVisible) {
			ifm.contentWindow.focus();
			myeditor.execCommand(command, false, extra);
		}
		else {
			switch (command) {
				case 'bold':
					AddTag('[b]', '[/b]'); break;
				case 'italic':
					AddTag('[i]', '[/i]'); break;
				case 'underline':
					AddTag('[u]', '[/u]'); break;
				case 'strikethrough':
					AddTag('[s]', '[/s]'); break;
				case 'InsertUnorderedList':
					AddTag('[list][\*]', '[/\*][/list]'); break;
			}
		}
	}

	this.doLink = function () {
		if (editorVisible) {
			ifm.contentWindow.focus();
			var mylink = prompt("Enter a URL:", "http://");
			if ((mylink != null) && (mylink != "")) {
				if (isIE) { //IE
					var range = ifm.contentWindow.document.selection.createRange();
					if (range.text == '') {
						range.pasteHTML("<a href='" + mylink + "'>" + mylink + "</a>");
					}
					else
						myeditor.execCommand("CreateLink", false, mylink);
				}
				else if (window.getSelection) { //FF
					var userSelection = ifm.contentWindow.getSelection().getRangeAt(0);
					if (userSelection.toString().length == 0)
						myeditor.execCommand('inserthtml', false, "<a href='" + mylink + "'>" + mylink + "</a>");
					else
						myeditor.execCommand("CreateLink", false, mylink);
				}
				else
					myeditor.execCommand("CreateLink", false, mylink);
			}
		}
		else {
			AddTag('[url=', ']click here[/url]');
		}
	}
	
	this.doImage = function () {
		if (editorVisible) {
			ifm.contentWindow.focus();
			myimg = prompt('Enter Image URL:', 'http://');
			if ((myimg != null) && (myimg != "")) {
				myeditor.execCommand('InsertImage', false, myimg);
			}
		}
		else {
			AddTag('[img]', '[/img]');
		}
	}

	function insertHtml(html) {
		ifm.contentWindow.focus();
		if (isIE)
			ifm.contentWindow.document.selection.createRange().pasteHTML(html);
		else
			myeditor.execCommand('inserthtml', false, html);
	}

	//textarea-mode functions
	function MozillaInsertText(element, text, pos) {
		element.value = element.value.slice(0, pos) + text + element.value.slice(pos);
	}

	function AddTag(t1, t2) {
		var element = textboxelement;
		if (isIE) {
			if (document.selection) {
				element.focus();

				var txt = element.value;
				var str = document.selection.createRange();

				if (str.text == "") {
					str.text = t1 + t2;
				}
				else if (txt.indexOf(str.text) >= 0) {
					str.text = t1 + str.text + t2;
				}
				else {
					element.value = txt + t1 + t2;
				}
				str.select();
			}
		}
		else if (typeof (element.selectionStart) != 'undefined') {
			var sel_start = element.selectionStart;
			var sel_end = element.selectionEnd;
			MozillaInsertText(element, t1, sel_start);
			MozillaInsertText(element, t2, sel_end + t1.length);
			element.selectionStart = sel_start;
			element.selectionEnd = sel_end + t1.length + t2.length;
			element.focus();
		}
		else {
			element.value = element.value + t1 + t2;
		}
	}
}
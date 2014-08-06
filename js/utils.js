/**
*
*  Javascript sprintf
*  http://www.webtoolkit.info/
*
* my fix: now works lead zero in float/double, like "03.2f"
*
**/

var sprintfWrapper = {

	init: function () {

		if (typeof arguments == "undefined") { return null; }
		if (arguments.length < 1) { return null; }
		if (typeof arguments[0] != "string") { return null; }
		//if (typeof RegExp == "undefined") { return null; }

		var string = arguments[0];
		var exp = new RegExp(/(%([%]|(\-)?(\+|\x20)?(0)?(\d+)?(\.(\d)?)?([bcdfosxX])))/g);
		var matches = new Array();
		var strings = new Array();
		var convCount = 0;
		var stringPosStart = 0;
		var stringPosEnd = 0;
		var matchPosEnd = 0;
		var newString = '';
		var match = null;

		while (match = exp.exec(string)) {
			if (match[9]) { convCount += 1; }

			stringPosStart = matchPosEnd;
			stringPosEnd = exp.lastIndex - match[0].length;
			strings[strings.length] = string.substring(stringPosStart, stringPosEnd);

			matchPosEnd = exp.lastIndex;
			matches[matches.length] = {
				match: match[0],
				left: match[3] ? true : false,
				sign: match[4] || '',
				pad: match[5] || ' ',
				min: match[6] || 0,
				precision: match[8],
				code: match[9] || '%',
				negative: parseInt(arguments[convCount]) < 0 ? true : false,
				argument: String(arguments[convCount])
			};
		}
		strings[strings.length] = string.substring(matchPosEnd);

		if (matches.length == 0) { return string; }
		if ((arguments.length - 1) < convCount) { return null; }

		var code = null;
		var match = null;
		var i = null;

		for (i=0; i<matches.length; i++) {

			var substitution;
			if (matches[i].code == '%') { substitution = '%' }
			else if (matches[i].code == 'b') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(2));
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'c') {
				matches[i].argument = String(String.fromCharCode(parseInt(Math.abs(parseInt(matches[i].argument)))));
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'd') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'f') {
				matches[i].argument = String(Math.abs(parseFloat(matches[i].argument)).toFixed(matches[i].precision ? matches[i].precision : 6));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'o') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(8));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 's') {
				matches[i].argument = matches[i].argument.substring(0, matches[i].precision ? matches[i].precision : matches[i].argument.length)
				substitution = sprintfWrapper.convert(matches[i], true);
			}
			else if (matches[i].code == 'x') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(16));
				substitution = sprintfWrapper.convert(matches[i]);
			}
			else if (matches[i].code == 'X') {
				matches[i].argument = String(Math.abs(parseInt(matches[i].argument)).toString(16));
				substitution = sprintfWrapper.convert(matches[i]).toUpperCase();
			}
			else {
				substitution = matches[i].match;
			}

			newString += strings[i];
			newString += substitution;

		}
		newString += strings[i];

		return newString;

	},

	convert: function(match, nosign){
		if (nosign) {
			match.sign = '';
		} else {
			match.sign = match.negative ? '-' : match.sign;
		}
		var l = 0;		
		
		if((match.code === "f" || match.code === "d")){
			var index_fract = match.argument.indexOf(".");
			l = match.min - index_fract + 1 - match.sign.length;
		}
		else{
			l = match.min - match.argument.length + 1 - match.sign.length;
		}
		
		var pad = new Array(l < 0 ? 0 : l).join(match.pad);
		if (!match.left) {
			if (match.pad == "0" || nosign) {
				return match.sign + pad + match.argument;
			} else {
				return pad + match.sign + match.argument;
			}
		} else {
			if (match.pad == "0" || nosign) {
				return match.sign + match.argument + pad.replace(/0/g, ' ');
			} else {
				return match.sign + match.argument + pad;
			}
		}
	}
}

var sprintf = sprintfWrapper.init;



function url_append_parameters(url_begin, url_part_parameters){
	url_begin = ""+url_begin;
	return url_begin+((url_begin.indexOf("?")===-1)?"?":"&")+url_part_parameters;
}


/***
 * 
 * @param string str string for parseInt()
 * @param integer default_value value when parseInt() returns NaN
 * @returns integer
 */
function parseIntDef(str, default_value){
	if(isNaN(str)) return isNaN(default_value) ? 0 : default_value;
	return parseInt(str);
}


/***
 * 
 * @param string str string for parseInt()
 * @param float default_value value when parseFloat() returns NaN
 * @returns float
 */
function parseFloatDef(str, default_value)
{
	if(isNaN(str)) return isNaN(default_value) ? 0 : default_value;
	return parseFloat(str);
}


function get_target(e)
{
  var targ;
  if (!e) e = window.event;
  if (e.target) targ = e.target;
  else if (e.srcElement) targ = e.srcElement;
  if (targ.nodeType == 3) // defeat Safari bug
    targ = targ.parentNode;
  return targ;
}


/***
 * for dinamicly-loaded elements those want to get onsezie
 */
function onresize_enable_on_element(element, func){
	
	element.setAttribute('has_onresize','1');
	element.onresize = func;
		
	if(window.is_onwidowresize_on_elements_enabled) return;
	window.is_onresize_on_elements_enabled;
	$(window).resize(function(event){
		$('[has_onresize]').each(function(ind,elem){
			if(elem.onresize){
				elem.onresize(event);
			}
		});
	});
}

/**
 * *** event is setted on body element (this is done in case of dynamycal add/remove input elements)
 * 
 * @param string selector  seletor input element 
 * @param function func_on_change handler called on change. params: event
 * @param double max_frequency_ms  minimal time between two calls of `func_on_change`; default is 400
 */
function input_onchange__not_often(selector, func_on_change, max_frequency_ms)
{
	if(max_frequency_ms === undefined) max_frequency_ms = 400;
	
	$('body').on('change keypress paste input',function(event){
		var elem = event.target;
		var val = elem.value;
		
		if(!$(elem).is(selector)) return;

		//console.log("input_onchange__not_often",val);

		if(!("__value_prev" in elem)){
			elem.__value_prev = elem.defaultValue;
		}

		var old_val = elem.__value_prev;

		if(val === elem.__value_prev) return; // input was not changed
		elem.__value_prev = val;

		var check_immediately = function(){
			elem.__last_check_time = new Date();

			//console.log( old_val, "=>", val, elem.value !== val);
			if(elem.value !== val) return;
			
			func_on_change(event);

		};

		var now = new Date();


		if  (elem.__last_check_time === undefined || now.getTime() - elem.__last_check_time.getTime() > max_frequency_ms){
			check_immediately();				
		}
		else{
			var timeout = elem.__last_check_time.getTime() + max_frequency_ms - now.getTime() + 1;
			//console.log("timeout",val, timeout);
			setTimeout( check_immediately, timeout);
		}


	});
		
}

// ==UserScript==
// @name           TA stuff
// @namespace      https://prodgame*.alliances.commandandconquer.com/*/index.aspx*
// @include        https://prodgame*.alliances.commandandconquer.com/*/index.aspx*
// @version        0.1
// @description  Tiberium Alliances stuff by sharpensteel. requires jQuery
// @author       sharpensteel@gmail.com
// @match        https://prodgame17.alliances.commandandconquer.com/259/index.aspx
// @grant        none
// ==/UserScript==

(function(){
	'use strict';


	window.Tool_window = {
		is_css_inited: false,

		$elem: undefined,

		create: function(title, expanded_width, expanded_height, $elem_css_array){

			this.css_inited_check();


			var _ = jQ.extend({}, Tool_window);

			_.$elem = jQ("<div class='ta_stuff_window' style='  position: absolute;  z-index: 10000;  top: 6px;  left: 131px; background: #CDCDCD;border: 1px solid black;border-radius: 5px;width: 200px;height: 25px;"+
						   "font-family:Segoe UI, Tahoma, Liberation Sans, Arial;font-size: 12px;'>"+
						   "  <div class='fl_header' style='background:#999494;text-align:center;height:22px;line-height: 22px;'>"+
						   "    "+title+
						   "    <div class='fl_button fl_minimize'>expand</div>"+
						   "  </div>"+
						   "  <div class='fl_body' style='position:absolute;top:30px;left:10px;right:10px;bottom:10px;padding:5px;background:white;visibility:hidden;'></div>"+
						   "</div>");
			if($elem_css_array){
				_.$elem.css($elem_css_array)
			}
			jQ('body').append(_.$elem);
			_.$elem.find('.fl_header .fl_button.fl_minimize').click(function(){ _.switch_collapse(); });
			_.$elem.attr('expanded_width', expanded_width).attr('expanded_height',expanded_height).attr('collapsed_width','150px').attr('collapsed_height','25px');

			_.$elem.click(_.bring_to_front.bind(_));

			_.bring_to_front.bind();

			return _;
		},

		css_inited_check: function(){
			if(this.is_css_inited) return;
			this.is_css_inited = 1;
			jQ('body').append("<style>.ta_stuff_window .fl_button{ display:inlinine-block;line-height:16px; float:right;border:1px solid #777; cursor:pointer;margin-right: 5px;background: rgb(101, 167, 190);}</style>");
		},

		switch_collapse: function(is_expand){
			var _ = this;
			if(is_expand === undefined) is_expand = (_.$elem.find('.fl_body').css('visibility') === 'hidden') ? 1 : 0;


			_.$elem.find('.fl_body').css('visibility',is_expand ? 'visible' : 'hidden');
			_.$elem.css({
				width: _.$elem.attr(is_expand ? 'expanded_width': 'collapsed_width'),
				height: _.$elem.attr(is_expand ? 'expanded_height': 'collapsed_height')
			});
			_.$elem.find('.fl_button.fl_minimize').html(is_expand ? 'collapse':'expand');
		},

		bring_to_front: function(){
			var max_zindex = 10000;
			jQ('.ta_stuff_window').each(function(ind,elem){ max_zindex = Math.max(max_zindex, jQ(elem).css('z-index')); });
			this.$elem.css('z-index',max_zindex+1);
		}

	};






	var Ta_stuff_function = function(){

		var tool_window_main;


		function load_reload_starting_script(){
			jQ('body').append("<script src='https://tiberium.mooo.com/ta_db/GameClientBackend/Staring_script?rnd="+Math.random()+"'></script>");
		}

		function creator(){
			try{

			tool_window_main = Tool_window.create("TA stuff",'544px','500px', {top:'30px',left:'120px'} );
			tool_window_main.$elem.addClass("ta_stuff");

			var $button_refresh = jQ("<div class='fl_button fl_refresh' style=''>refresh</div>");
			tool_window_main.$elem.find('.fl_header').append($button_refresh);
			$button_refresh.click(load_reload_starting_script);


			tool_window_main.$elem.find('.fl_body').html('loading...');


			load_reload_starting_script();




			/*jQ.ajax({url:'https://ta_local/ta_db/GameClientBackend/Staring_script'}).done(function(data){
				jQ('body').append("<script>"+data+"</script>");
			});*/
			} catch (e) {
				console.debug("Ta stuff: error in creator(): ", e);
			}
		}



		console.log("TA stuff initialization...");



		function dependency_waiter(){


			if(!window.jQ){
				console.log("TA stuff waiting for jQuery ...");
				setTimeout(dependency_waiter,400);
				return;
			}

			if(!window.ClientLib){
				console.log("TA stuff waiting for ClientLib ...");
				setTimeout(dependency_waiter,400);
				return;
			};


			console.log("TA stuff get jquery, continue initialization ...");

			var is_document_ready = false;
			window.jQ(function(){
				if(is_document_ready) return;
				is_document_ready = true;
				creator();
			});
			setTimeout(function(){
				if(is_document_ready) return;
				is_document_ready = true;
				creator();
			});
		};

		/*
		function init_draggable(){
		var $dragging = null;
		$('body').on("mousedown", "draggable_avaliable", function(e) {
			$(this).attr('unselectable', 'on').addClass('draggable');
			var el_w = $('.draggable').outerWidth(),
				el_h = $('.draggable').outerHeight();
			$('body').on("mousemove", function(e) {
				if ($dragging) {
					$dragging.offset({
						top: e.pageY - el_h / 2,
						left: e.pageX - el_w / 2
					});
				}
			});
			$dragging = $(e.target);
		}).on("mouseup", ".draggable", function(e) {
			$dragging = null;
			$(this).removeAttr('unselectable').removeClass('draggable');
		});
	}
	*/

		dependency_waiter();
	}


	try {
		var script = document.createElement("script");
		script.innerHTML = "(" + Ta_stuff_function.toString() + ")();";
		script.type = "text/javascript";
		if (/commandandconquer\.com/i.test(document.domain)) {
			document.getElementsByTagName("head")[0].appendChild(script);
		}
	} catch (e) {
		console.debug("Ta_stuff: init error: ", e);
	}


})();
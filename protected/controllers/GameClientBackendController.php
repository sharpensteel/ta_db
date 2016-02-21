<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GameClientBackend
 *
 * @author Moss
 */
class GameClientBackendController extends Controller {
	function actionStaring_script($_){



		header('Content-Type: application/javascript');
		?>

		try{
		(function(){
			console.log("<?=__METHOD__?> started...");

			var ta_db_base_url = "<?=baseUrl(true)?>";

			var server_url_start = 'https://prodgame17.alliances.commandandconquer.com/259/';

			function get_url_ajax_endpoint_by_api(){ return ClientLib.Net.CommunicationManager.GetInstance().get_ServerUrl(); }

			function get_session_id(){ return ClientLib.Net.CommunicationManager.GetInstance().get_InstanceId(); }

			function get_world_update_link(){
				var url = ta_db_base_url+"GameClientBackend/World_data_update" +
				"?session_id="+html_encode(get_session_id()) +
				"&url_ajax_endpoint_by_api=" + html_encode(get_url_ajax_endpoint_by_api()) +
				"&url_client=" + html_encode(location.href);
				return url;
			}

			var $ta_stuff = jQ(".ta_stuff .fl_body");
			$ta_stuff.html("loaded! "+( (new Date()).toISOString().slice(0, 19)) ).append("<br><br>");
			$ta_stuff.append('<style>.fl_link{ cursor:pointer;text-decoration:underline;color:#0000aa; }</style>');






			function request_generic(method_name, data, callback_on_done){
				if(!data) data = {};
				data['session'] = get_session_id();

				jQ.ajax(
					get_url_ajax_endpoint_by_api()+method_name,
					{
						type:'POST',
						contentType:'application/json',
						headers: JSON.parse('{ "X-Qooxdoo-Response-Type":"application/json" }'),
						data: JSON.stringify(data)
					}
				).done(function(data,textStatus){
					//console.log(method_name+' RESPONSE:',JSON.stringify(data));
					callback_on_done(data);
				});
			}



			function request__AllianceGetMemberData(callback_on_done){
				request_generic("AllianceGetMemberData",null,callback_on_done);
			}


			function request__NotificationGetRange_progress(callback_on_done){
				var data = {category: "9", skip: 0, take: 100, sortOrder: 0, take: 200};
				request_generic("NotificationGetRange",data,callback_on_done);
			}




			var Tw_update_world = {
				tw: undefined,
				create : function(){
					var _ = jQ.extend({},Tw_update_world);

					_.tw = Tool_window.create("Update world data",'544px','500px', {top:'60px',left:'140px'});

					_.tw.switch_collapse(1);
					setTimeout(function(){_.tw.bring_to_front();},1);

					var url = get_world_update_link();

					var $body = _.tw.$elem.find('.fl_body');
					$body.css('overflow','initial');
					$body.html("<iframe src='"+url+"' style='width: 100%;height: 100%;'></iframe>");

					return _;
				}
			};

			var Tw_hacker_attack_report = {
				tw: undefined,

				create : function(){
					var _ = jQ.extend({},Tw_hacker_attack_report);

					_.tw = Tool_window.create("Hacker attack report",'544px','500px', {top:'60px',left:'140px'});
					_.tw.switch_collapse(1);
					setTimeout(function(){_.tw.bring_to_front();},1);

					var $button_refresh = jQ("<div class='fl_button fl_refresh' style=''>refresh</div>");
					_.tw.$elem.find('.fl_header').append($button_refresh);
					$button_refresh.click(_.refresh.bind(_));

					_.refresh();

					return _;
				},

				refresh: function(){
					var _ = this;
					var $body = _.tw.$elem.find('.fl_body');
					$body.html("loading...");
					request__AllianceGetMemberData(function(allance_data){
						request__NotificationGetRange_progress(function(progress_data){
							_.generate_report(allance_data, progress_data);

						});
					});
				},

				generate_report: function(allance_data, progress_data){
					var _ = this;
					var $body = _.tw.$elem.find('.fl_body');
                                        var tt_now = 1*(new Date);
                                        var tt_actual = tt_now - 24*60*60*1000;

					var player_dict = {};
					for(var i=0; i<allance_data.length; i++){
						var p = allance_data[i];
						player_dict[p['n']] = p;
					}

					for(var i=0; i<progress_data.length; i++){
						var pd = progress_data[i];
						if(pd['mdb']!=61) continue;
                                                if(pd['t']<tt_actual)continue;

						var name = pd['p'][0]['v'];
						if(!(name in player_dict)) continue;
						player_dict[name]['tt_virus'] = pd['t'];
					}

					var str_no = "";
					var str_yes = "";
					for(var name in player_dict){
						var p = player_dict[name];
						if(p['tt_virus']){
							var dt_str = (new Date(p['tt_virus'])).toISOString().replace('T',' ');
							str_yes += name+" --- " + dt_str + "<br>";
						}
						else{
							str_no += name+"; ";
						}
					}
					var report = "Not injected virus:<br><br>"+str_no+"<br><br>==============<br>"+"Injected virus:<br><br>"+str_yes;

					$body.html(report);
				}

			};


			var Tw_online_report = {
				tw: undefined,

				create : function(){
					var _ = jQ.extend({},Tw_online_report);

					_.tw = Tool_window.create("Online report",'544px','500px', {top:'60px',left:'140px'});
					_.tw.switch_collapse(1);
					setTimeout(function(){_.tw.bring_to_front();},1);

					var $button_refresh = jQ("<div class='fl_button fl_refresh' style=''>refresh</div>");
					_.tw.$elem.find('.fl_header').append($button_refresh);
					$button_refresh.click(_.refresh.bind(_));

					_.refresh();

					return _;
				},

				refresh: function(){

					var _ = this;
					var $body = _.tw.$elem.find('.fl_body');
					$body.html("loading...");
					request__AllianceGetMemberData(function(allance_data){
						_.generate_report(allance_data);
					});
				},

				generate_report: function(allance_data){

					var _ = this;
					var $body = _.tw.$elem.find('.fl_body');

					var player_dict = {};
					for(var i=0; i<allance_data.length; i++){
						var p = allance_data[i];
						console.log(p.n, p.os);
					}


					var report = "look in console.";

					$body.html(report);
				}

			};


			var $link_world_data_update = jQ("<div style="display:inline-block;"><a class='fl_link' href='"+get_world_update_link()+"' target='_blank'>Update world data</a></div>");
			$ta_stuff.append($link_world_data_update).append("<br>");
			/*$link_world_data_update.click(function(){
				var w = Tw_update_world.create();
			});*/

			var $link_hacker_attack_report = jQ("<div style="display:inline-block;"><span class='fl_link'>Hacker attack report</span></div>");
			$ta_stuff.append($link_hacker_attack_report).append("<br>");
			$link_hacker_attack_report.click(function(){
				var w = Tw_hacker_attack_report.create();

			});

			var $link_online_report = jQ("<div style="display:inline-block;"><span class='fl_link'>Online report</span></div>");
			$ta_stuff.append($link_online_report).append("<br>");
			$link_online_report.click(function(){
				var w = Tw_online_report.create();
			});


		})();
		}
		catch(e){
			console.log('Exception!!!', e);
		}

		function html_encode(value){
			//create a in-memory div, set it's inner text(which jQuery automatically encodes)
			//then grab the encoded contents back out.  The div never exists on the page.
			return jQ('<div/>').text(value).html();
		}

		function html_decode(value){
			return jQ('<div/>').html(value).text();
		}

		<?
	}





	function actionTest1($session_id){
		vd($session_id);
		$url = 'https://prodgame17.alliances.commandandconquer.com/259/Presentation/Service.svc/ajaxEndpoint/AllianceGetMemberData';

		$params_json = json_encode( array('session'=>$session_id) );

		$params = array(
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $params_json,

			CURLOPT_HTTPHEADER => array(
				'X-Qooxdoo-Response-Type: application/json',
				'Content-Type: application/json',
				'Content-Length: '. strlen($params_json),


			),
			//CURLOPT_HTTPHEADER => array('application/x-www-form-urlencoded')
		);

		if(array_default($GLOBALS,'CURL_DONT_VERIFY_SSL',0)){
			$params[CURLOPT_SSL_VERIFYHOST] = 0;
			$params[CURLOPT_SSL_VERIFYPEER] = 0;
		}

		$res = curl_get_contents($url, $params);

		vd($res);

	}

	// sample call: http://146.185.186.182/ta_db/GameClientBackend/World_data_update?session_id=33d54ac8-53aa-4dbc-b4a9-56447019a50a&url_ajax_endpoint_by_api=Presentation/Service.svc/ajaxEndpoint/&url_client=https://prodgame17.alliances.commandandconquer.com/259/index.aspx
	// sample call: https://ta_local/ta_db/GameClientBackend/World_data_update?session_id=bc433fa6-b8e8-41c0-bdfe-8092cdeb3abf&url_ajax_endpoint_by_api=Presentation/Service.svc/ajaxEndpoint/&url_client=https://prodgame17.alliances.commandandconquer.com/259/index.aspx
	function actionWorld_data_update($session_id, $url_client, $url_ajax_endpoint_by_api)
	{
		$updater = new World_data_updater($session_id, $url_client, $url_ajax_endpoint_by_api);
		$updater->make_update();


	}

	// sample call: localhost/ta_db/GameClientBackend/World_data_update_attack_log?session_id=fc45eed5-edeb-47c7-bf81-5c71bf54037a&take=1000&skip=0&url_ajax_endpoint_by_api=Presentation/Service.svc/ajaxEndpoint/&url_client=https://prodgame05.alliances.commandandconquer.com/316/index.aspx
	function actionWorld_data_update_attack_log($session_id, $url_client, $url_ajax_endpoint_by_api,$take=1000, $skip=0)
	{
		$updater = new World_data_updater($session_id, $url_client, $url_ajax_endpoint_by_api);
		$updater->update_attack_log($take, $skip);


	}


}

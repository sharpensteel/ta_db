// ==UserScript==
// @name        Vortek Preload
// @description Load variables
// @namespace      https://prodgame*.alliances.commandandconquer.com/*/index.aspx*
// @include        https://prodgame*.alliances.commandandconquer.com/*/index.aspx*
// @version     1.1
// ==/UserScript==
// a function that loads jQuery and calls a callback function when jQuery has finished loading
function addJQuery(callback) {
    var script = document.createElement("script");
    script.setAttribute("src", "//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js");
    script.addEventListener('load', function() {
        var script = document.createElement("script");
        script.textContent = "window.jQ=jQuery.noConflict(true);(" + callback.toString() + ")();";
        document.body.appendChild(script);
    }, false);
    document.body.appendChild(script);
}
function main(){
    $(document).ready(function()
                      {
                          setTimeout(function(){
                              /*$('.fluid_types').val('Liquid');
                              $('.fluid_types').triggerHandler('change');
                              setTimeout(function(){
                                  $('.fluids').val('Ammonia');
                              },500);*/
                              $('.fluid_types').triggerHandler('change');
                              $('.flow_unit_1').val('lb');
                              $('.flow_unit_2').val('sec');
                              $('.min_flow').val(1);
                              $('.nom_flow').val(2);
                              $('.max_flow').val(8);
                              $('.min_temp').val(280);
                              $('.nom_temp').val(280);
                              $('.max_temp').val(280);
                              //$('.min_press').val(10);
                              //$('.nom_press').val(10);
                              //$('.max_press').val(20);
                              setTimeout(function(){
                                  $('#button_calc').triggerHandler('click');
                                  setTimeout(function(){
                                      $('.icon_size').triggerHandler('click');
                                  },500);
                              },500);
                          },2000);
                      });
}
// load jQuery and execute the main function
addJQuery(main);
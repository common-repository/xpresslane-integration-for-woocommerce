(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 $( document ).ready( function( e ){

	
		
	 	$(document).find('#mwb-xlane-url-option').select2();
	 	siteurldsiplay();
	 	$( '#mwb-xlane-url-option' ).on('change', function( e ){
	 		siteurldsiplay();
	 	});
	 	function siteurldsiplay(){
	 		if( $( '#mwb-xlane-url-option' ).val() == 'mwb_xlane_live_url_option' ){
	 			$( '#mwb-xlane-live-url').closest('tr').show();
	 			$( '#mwb-xlane-stage-url').closest('tr').hide();
	 		}else if(  $( '#mwb-xlane-url-option' ).val() == 'mwb_xlane_stage_url_option' ){
	 			$( '#mwb-xlane-stage-url').closest('tr').show();
	 			$( '#mwb-xlane-live-url').closest('tr').hide();
	 		}
	 	}
	 });

	
	 // Function to section the tabs

		

	 $( document ).ready( function( e ){
	 	$( '#mwb-xlane-text-product').closest('tr').hide();
	 	$( '#mwb-xlane-btn-width').closest('tr').hide();
		
	 	//after settings saved
	 	if ($('input.mwb_xlane_prod_class').prop('checked')) {
		    $( '#mwb-xlane-text-product').closest('tr').show();
	        $( '#mwb-xlane-btn-width').closest('tr').show();
		}
		//After setting save check logo button
		console.log($('.mwb_xlane_logo_button_class:checked').val());
		if ($('.mwb_xlane_logo_button_class:checked').val() == 'logo-in') {
		    $( '.mwb-xlane-img-logo').closest('tr').show();
			$( '.mwb-xlane-logo-below').closest('tr').hide();
		}else{
			console.log($('.mwb_xlane_logo_button_class:checked').val());
			$( '.mwb-xlane-img-logo').closest('tr').hide();//Logo button
			$( '.mwb-xlane-logo-below').closest('tr').show();
		}
		$(".tab_general").on("click",function(){
                                       
			return show_selected_tab($(this),"general");
		});
	
		$(".tab_design").on("click",function(){
			return show_selected_tab($(this),"design");
		});

		$("#xlm-logo").on("click",function(){
			console.log('kakaka');
			return show_selected_sction($(this),"xlm-logo");
		});


		$("#xlm-product").on("click",function(){
                                       
			return show_selected_sction($(this),"xlm-product");
		});
	
		$("#xlm-cart").on("click",function(){
			return show_selected_sction($(this),"xlm-cart");
		});

		$("#xlm-mini-cart").on("click",function(){
			return show_selected_sction($(this),"xlm-mini-cart");
		});

		
		// on change on product page settings
	 	$('[name="mwb-xlane-prod-page"]').change(function()
	      {
	        if ($(this).is(':checked')) {
	        	$( '#mwb-xlane-text-product').closest('tr').show();
	        	$( '#mwb-xlane-btn-width').closest('tr').show();
	        }else{
	        	$( '#mwb-xlane-text-product').closest('tr').hide();
	        	$( '#mwb-xlane-btn-width').closest('tr').hide();
	        }
	      });

		  // on change on product page settings
	 	$('input.mwb_xlane_logo_button_class').change(function()
		 {
			
			if ($(this).val()=='logo-in') {
				console.log($(this).val());
			 $( '.mwb-xlane-img-logo').closest('tr').show();
			 $( '.mwb-xlane-logo-below').closest('tr').hide();
			
			}else{
				console.log("hide");
				$( '.mwb-xlane-img-logo').closest('tr').hide();
			 $( '.mwb-xlane-logo-below').closest('tr').show();
			
			}
		 });


		  // on change on Logo settings
	 	$('#mwb_xlane_logo_button_class').click(function()
		 {
			
		 });

		//Section Tab
		 function show_selected_tab($element,$tab)
		 {
			$(".mwb-xlane-design").closest("span, tr, h2").hide();
			$(".mwb-xlane-design").next("p").hide();
			$(".mwb-xlane-log").closest("tr,h2").hide();
	
			$(".mwb-xlane-general-settings").closest("tr,h2").hide();
			$("#mwb-xlane-section-design").hide();
			
			$(".mwb-xlane-general-settings").next("p").hide();

			 $(".nav-tab").removeClass("nav-tab-active");
			 $element.addClass("nav-tab-active");                   
			
			 $("."+$tab+"_tab_field").closest("tr,h2").show();
			 $("."+$tab+"_tab_field").next("p").show();
			 
			
			if($tab =="general"){
				$(".mwb-xlane-design").closest("span, tr,h2").hide();
				$(".mwb-xlane-general-settings").closest("tr,h2").show();
				$(".mwb-xlane-general-settings").next("p").show();
		
			}
			if($tab =="design"){
				$("#mwb-xlane-section-design").show();
				$(".mwb-xlane-design").closest("tr,h2,ul").show();
				$(".mwb-xlane-design").next("p").show();
				show_selected_sction( $(".tab_general"),"xlm-logo" )
			   }
			 
			 return false;
		 } 
	
	
		show_selected_tab($(".tab_general"),"general");
		show_selected_sction( $(".tab_general"),"" )
		
		 
		$(document).find('#mwb-xlane-url-option').select2();
		siteurldsiplay();
		$( '#mwb-xlane-url-option' ).on('change', function( e ){
			siteurldsiplay();
		});
		function siteurldsiplay(){
			console.log( $( '#mwb-xlane-url-option' ).val() );
			if( $( '#mwb-xlane-url-option' ).val() == 'mwb_xlane_live_url_option' ){
				$( '#mwb-xlane-live-url').closest('tr').show();
				$( '#mwb-xlane-stage-url').closest('tr').hide();
			}else if(  $( '#mwb-xlane-url-option' ).val() == 'mwb_xlane_stage_url_option' ){
				$( '#mwb-xlane-stage-url').closest('tr').show();
				$( '#mwb-xlane-live-url').closest('tr').hide();
			}
		}
		 
		//Design Sub Section Tab
		 function show_selected_sction( $element , $tab ){
			 $(".mwb-xlane-design").closest("tr,h2").hide();
			 $(".mwb-xlane-logo").closest("tr,h2").hide();
			 $('.mwb-xlane-product-button').closest("span, tr, h2").hide();
			 $('.mwb-xlane-cart-button').closest("span, tr, h2").hide();
			 $('.mwb-xlane-mini-button').closest("span, tr, h2").hide();
			 $(".xlm" ).removeClass("current");
			 $element.addClass("xlm current"); 


			 if( $tab == 'xlm-logo'){
				$(".mwb-xlane-logo").closest("tr,h2").show();
				if ($('.mwb_xlane_logo_button_class:checked').val() == 'logo-in') {
					$( '.mwb-xlane-img-logo').closest('tr').show();
					$( '.mwb-xlane-logo-below').closest('tr').hide();
				}else{
					$( '.mwb-xlane-img-logo').closest('tr').hide();//Logo button
					$( '.mwb-xlane-logo-below').closest('tr').show();
				}
				
				
			 }
			 if( $tab == 'xlm-product'){
				$('.mwb-xlane-product-button').closest("span, tr, h2").show();
			}
			if( $tab == 'xlm-cart'){
				$('.mwb-xlane-cart-button').closest("span, tr, h2").show();
			}
			if( $tab == 'xlm-mini-cart'){
				$('.mwb-xlane-mini-button').closest("span, tr, h2").show();
			}
			
			
			
			return false;
		 }
	 	function insertAfter(referenceNode, newNode) {
		  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
		}
	 	// on change for plugin enable
	 	$( '.mwb-xlane-option-enable').closest('tr').css({'opacity':'0.4','pointer-events':'none'});
	 	$( '.mwb-xlane-option-enable').closest('span').css({'opacity':'0.4','pointer-events':'none'});
	 	$('h2:contains(Design)').addClass('xlane-design');
	 	$('h2:contains(Design)').attr('id','xlane-design-id');
	 	$( '.xlane-design').css({'opacity':'0.4','pointer-events':'none'});
  //   	var elem = document.createElement("img");
		// elem.setAttribute("src", pluginDir.pluginDirUrl+"/css/Screenshot.png");
		// elem.setAttribute("height", "768");
		// elem.setAttribute("width", "1024");
		// elem.setAttribute("alt", "Setting Blur image");
		// elem.setAttribute("style", "width:100%;height:auto;padding-top: 12px !important;");
		// elem.setAttribute("class", "mwb-blur-img");
		// insertAfter(document.getElementById("xlane-design-id"),elem);
		//$('.mwb-blur-img').show();
	 	//after settings saved
	 	if ($('input#mwb-xlane-plugin-enable').prop('checked')) {
		    $( '.mwb-xlane-option-enable').closest('tr').css({'opacity':'unset','pointer-events':'unset'});
		    $( '.mwb-xlane-option-enable').closest('span').css({'opacity':'unset','pointer-events':'unset'});
		    $( '.xlane-design').css({'opacity':'unset','pointer-events':'unset'});
		    //$('.mwb-blur-img').css({'opacity:0.4','pointer-events:none'});
		    //insertAfter(document.getElementById("xlane-design-id"),elem);
		}
	 	$('[name="mwb-xlane-plugin-enable"]').change(function()
	      {
	        if ($('#mwb-xlane-plugin-enable').is(':checked')) {
	        	$( '.mwb-xlane-option-enable').closest('tr').css({'opacity':'unset','pointer-events':'unset'});
	        	$( '.mwb-xlane-option-enable').closest('span').css({'opacity':'unset','pointer-events':'unset'});
	        	$( '.xlane-design').css({'opacity':'unset','pointer-events':'unset'});
	        	//$( '.mwb-blur-img').css({'opacity:0.4','pointer-events:none'});
	        	
	        	 
	        }else{
	        	$( '.mwb-xlane-option-enable').closest('tr').css({'opacity':' 0.4','pointer-events':'none'});
	        	$( '.mwb-xlane-option-enable').closest('span').css({'opacity':'0.4','pointer-events':'none'});
	        	$( '.xlane-design').css({'opacity':'0.4','pointer-events':'none'});
	        	//$( '.mwb-blur-img').show();
	        	
	        }
	      });
	 });
	 
	

})( jQuery );

(function( $ ) {
	'use strict';

	$(document).ready(function(e){
		initQouteRenderPayment();	
		initRegistration();
		initProductSelector();
	});

	function initProductSelector(){
		$('.gobloo-product-selector').on('change',function(e){
			var $value = $(this).val();
			$(this).closest('.gobloo-product-selector-outer').next().attr('data-product', $value );
			var $price = $(this).find('option[value="'+$value+'"]').attr('data-price');
			var $label = $(this).find('option[value="'+$value+'"]').text();
			$(this).closest('.gobloo-product-selector-outer').next().attr('data-product-price', $price );
			$(this).closest('.gobloo-product-selector-outer').next().attr('data-product-label', $label );
		});
	}

	function initQouteRenderPayment(){
		// console.log('URL IS SHARED',gobloo.ajax_url);
		$('.add-payment-method').on('click',function(e){
			e.preventDefault;
			$(this).addClass('loading');
			// var dataObject = JSON.parse( atob( $(this).attr('data-object') ));

			var $product = '', $price='', $label='';
			if($(this).attr('data-product')){
				$product 	= $(this).attr('data-product');
				$price		= $(this).attr('data-product-price');
				$label 		= $(this).attr('data-product-label');
			}
			var payload = {
				'action' 					: 'CheckoutProcess',
				'fullancer_id'				: $(this).attr('data-fullancer-id'),
				'fullancer_stripe'			: $(this).attr('data-fullancer-stripe'),
				'product'					: $product,
				'price'						: $price,
				'label'						: $label
			};

			$.post(gobloo.ajax_url,payload)
			.success(function(response){
				if(response.success){
					var stripe;
					if(response.data.pk){
						stripe = Stripe(response.data.pk);
					} else {
						console.error('Cannot proceeed. No PK defined by the current account'); 
						return false;
					}
					stripe.redirectToCheckout({
						// Make the id field from the Checkout Session creation API response
						// available to this file, so you can provide it as parameter here
						// instead of the {{CHECKOUT_SESSION_ID}} placeholder.
						sessionId: response.data.session.id
					  }).then(function (result) {
						// If `redirectToCheckout` fails due to a browser or network
						// error, display the localized error message to your customer
						// using `result.error.message`.
						console.log(result);
						alert('redirecting');
					  });
				}
			});

		});
	}

	/**
	 * Registration
	 */
	function initRegistration(){

		if($('.sc-global-wrapper').length < 1) return false;
		var first_name  = document.querySelector('#sc-first-name'),
		last_name   = document.querySelector('#sc-last-name'),
		email       = document.querySelector('#sc-email'),
		oauth_link  = document.querySelector('#oauth-link');

		var $oAuthExpress = oauth_link.getAttribute('data-url');
		

		oauth_link.addEventListener('click',event=>{

			var classList = event.target.parentElement.classList.value.split(' ');

			if( classList.includes('processing') ) return false;
			event.target.parentElement.classList.add('processing');
			

			var errorNode = document.querySelector('#error-email');
			if(errorNode){
				errorNode.remove();
			}
			
			
			event.preventDefault();


			//check if any of the fields is empty
			if(!email.value){
				renderError('Email is required');
				event.target.parentElement.classList.remove('processing');
				return false;
			}

			var payload = {
				'action' 	: 'InitiateRegistration',
				'first_name': first_name.value,
				'last_name'	: last_name.value,
				'email'		: email.value,
				'url'		: $oAuthExpress
			};

			$.post(gobloo.ajax_url,payload)
			.success(function(response){
				if(response.success){
					processoAuthLink(JSON.parse( response.data.transient ) );	
				} else {
					event.target.parentElement.classList.remove('processing');
				}
				
			})
			.fail(function(response){	
				// var errorNode = document.createElement('span');
				// errorNode.setAttribute('id','error-email');
				// errorNode.textContent = response.responseJSON.data.msg;
				// oauth_link.parentElement.appendChild(errorNode);
				renderError( response.responseJSON.data.msg );
				event.target.parentElement.classList.remove('processing');
			});

			

		});

		/*
			Add Error
		*/
		function renderError(msg){
			var errorNode = document.createElement('span');
			errorNode.setAttribute('id','error-email');
			errorNode.textContent = msg;
			oauth_link.parentElement.appendChild(errorNode);	
		}
		

		function processoAuthLink(response){
			if(response){
				var new_oAuthExpress = $oAuthExpress;

				if(email.value){
					new_oAuthExpress = new_oAuthExpress + '&stripe_user[email]='+email.value;        
				}

				if(first_name.value){
					new_oAuthExpress = new_oAuthExpress + '&stripe_user[first_name]='+first_name.value;        
				}

				if(last_name.value){
					new_oAuthExpress = new_oAuthExpress + '&stripe_user[last_name]='+last_name.value;        
				}
				//console.log('processing...',new_oAuthExpress);
				//oauth_link.setAttribute('href',new_oAuthExpress);
				window.location.href = response.url ;//encodeURI(new_oAuthExpress);
			}

		}
	}

})( jQuery );


document.addEventListener( "DOMContentLoaded", function () {
	"use strict";

	var el;
	window.TomSelect && ( new TomSelect( el = document.getElementById( 'priority' ), {
		copyClassesToDropdown: false,
		dropdownClass: 'dropdown-menu ts-dropdown',
		optionClass: 'dropdown-item',
		controlInput: '<input>',
		render: {
			item: function ( data, escape ) {
				if ( data.customProperties ) {
					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape( data.text ) + '</div>';
				}
				return '<div>' + escape( data.text ) + '</div>';
			},
			option: function ( data, escape ) {
				if ( data.customProperties ) {
					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape( data.text ) + '</div>';
				}
				return '<div>' + escape( data.text ) + '</div>';
			},
		},
	} ) );
	window.TomSelect && ( new TomSelect( et = document.getElementById( 'category' ), {
		copyClassesToDropdown: false,
		dropdownClass: 'dropdown-menu ts-dropdown',
		optionClass: 'dropdown-item',
		controlInput: '<input>',
		render: {
			item: function ( data, escape ) {
				if ( data.customProperties ) {
					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape( data.text ) + '</div>';
				}
				return '<div>' + escape( data.text ) + '</div>';
			},
			option: function ( data, escape ) {
				if ( data.customProperties ) {
					return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape( data.text ) + '</div>';
				}
				return '<div>' + escape( data.text ) + '</div>';
			},
		},
	} ) );
} );

function sendSupportForm() {
	"use strict";

	const submitBtn = document.getElementById( "support_button" );
	submitBtn.disabled = true;
	submitBtn.classList.add( 'submitting' );

	var formData = new FormData();
	formData.append( 'category', $( "#category" ).val() );
	formData.append( 'priority', $( "#priority" ).val() );
	formData.append( 'subject', $( "#subject" ).val() );
	formData.append( 'message', $( "#message" ).val() );

	$.ajax( {
		type: "post",
		url: "/dashboard/support/new-support-request/send",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success('Support Ticket Created Succesfully. Redirecting...')
			setTimeout( function () {
				location.href = '/dashboard/support/my-requests'
			}, 1500 );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			submitBtn.disabled = false;
			submitBtn.classList.remove( 'submitting' );
		}
	} );
	return false;
}

$( document ).on( 'ready', function () {
	"use strict";
} );

// $(document).ready(function() {
// 	console.log('Hello');

// 	$( "#scrollable_content" ).animate( { scrollTop: 1000 }, 200 );

// 	navigator.mediaDevices.getUserMedia({ audio: true })
//     .then(function(stream) {
//       var mediaRecorder = new MediaRecorder(stream);
//       var chunks = [];

//       // Start recording when the button is pressed
//       $('#voice_record_button').click(function() {
// 		console.log('RECORD');
//         mediaRecorder.start();
//       });

//       // Stop recording and save the audio when the button is released
//       $('#voice_record_button').mouseup(function() {
// 		console.log('Stopped');
//         mediaRecorder.stop();
//         mediaRecorder.ondataavailable = function(e) {
//           chunks.push(e.data);
//         }
//         mediaRecorder.onstop = function(e) {
//           var blob = new Blob(chunks, { 'type' : 'audio/ogg; codecs=opus' });
//           var audioURL = URL.createObjectURL(blob);
//           var audio = new Audio(audioURL);
//           audio.controls = true;
//           document.body.appendChild(audio);
//         }
//       });
//     })
//     .catch(function(err) {
//       console.log('The following error occurred: ' + err);
//     });

// })

function sendMessage( ticket_id ) {
	"use strict";

	const submitBtn = document.getElementById( "send_message_button" );
	submitBtn.disabled = true;
	submitBtn.classList.add( 'submitting' );

	var formData = new FormData();
	formData.append( 'message', $( "#message" ).val() );
	formData.append( 'ticket_id', ticket_id );

	$.ajax( {
		type: "post",
		url: "/dashboard/support/requests-action/send-message",
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success('Message sent succesfully. Please Wait')
			setTimeout( function () {
				location.reload();
			}, 1500 );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			submitBtn.disabled = false;
			submitBtn.classList.remove( 'submitting' );
		}
	} );
	return false;
}

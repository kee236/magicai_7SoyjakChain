let selectedPrompt = -1;
let promptsData = [];
let searchString = '';
let filterType = 'all';
let imagePath = [];

function updateFav(id) {
	$.ajax({
		type: 'post',
		url: '/dashboard/user/openai/chat/update-prompt',
		data: {
			id: id,
		},
		success: function (data) {
			favData = data;
			updatePrompts(promptsData);
		},
		error: function () { },
	});
}

function updatePrompts(data) {
	const $prompts = $('#prompts');

	$prompts.empty();

	if (data.length == 0) {
		$('#no_prompt').removeClass('hidden');
	} else {
		$('#no_prompt').addClass('hidden');
	}

	for (let i = 0; i < data.length; i++) {
		let isFav = favData.filter(item => item.item_id == data[i].id).length;

		let title = data[i].title.toLowerCase();
		let prompt = data[i].prompt.toLowerCase();
		let searchStr = searchString.toLowerCase();

		if (searchStr.replace(/\s/g, '') === '') {
		}

		if (data[i].id == selectedPrompt) {
			if (title.includes(searchStr) || prompt.includes(searchStr)) {
				if (
					(filterType == 'fav' && isFav != 0) ||
					filterType != 'fav'
				) {
					let prompt = document
						.querySelector('#selected_prompt')
						.content.cloneNode(true);
					const favbtn = prompt.querySelector('.favbtn');
					prompt.querySelector('.prompt_title').innerHTML =
						data[i].title;
					prompt.querySelector('.prompt_text').innerHTML =
						data[i].prompt;
					favbtn.setAttribute('id', data[i].id);

					if (isFav != 0) {
						favbtn.classList.add('active');
					} else {
						favbtn.classList.remove('active');
					}

					$prompts.append(prompt);
				} else {
					selectedPrompt = -1;
				}
			} else {
				selectedPrompt = -1;
			}
		} else {
			if (title.includes(searchStr) || prompt.includes(searchStr)) {
				if (
					(filterType == 'fav' && isFav != 0) ||
					filterType != 'fav'
				) {
					let prompt = document
						.querySelector('#unselected_prompt')
						.content.cloneNode(true);
					const favbtn = prompt.querySelector('.favbtn');
					prompt.querySelector('.prompt_title').innerHTML =
						data[i].title;
					prompt.querySelector('.prompt_text').innerHTML =
						data[i].prompt;
					favbtn.setAttribute('id', data[i].id);

					if (isFav != 0) {
						favbtn.classList.add('active');
					} else {
						favbtn.classList.remove('active');
					}

					$prompts.append(prompt);
				}
			}
		}
	}
	let favCnt = favData.length;
	let perCnt = data.length;

	if (favCnt == 0) {
		$('#fav_count')[0].innerHTML = '';
	} else {
		$('#fav_count')[0].innerHTML = favCnt;
	}

	if (perCnt == 0 || perCnt == undefined) {
		$('#per_count')[0].innerHTML = '';
	} else {
		$('#per_count')[0].innerHTML = perCnt;
	}
}

function searchStringChange(e) {
	searchString = $('#search_str').val();
	console.log(searchString);
	updatePrompts(promptsData);
}

function openNewImageDlg(e) {
	$('#selectImageInput').click();
}

function updatePromptImages() {
	$('#chat_images').empty();
	if (prompt_images.length == 0) {
		$('#chat_images').addClass('hidden');
		$('.split_line').addClass('hidden');
		return;
	}
	$('#chat_images').removeClass('hidden');
	$('.split_line').removeClass('hidden');
	for (let i = 0; i < prompt_images.length; i++) {
		let new_image = document
			.querySelector('#prompt_image')
			.content.cloneNode(true);
		$(new_image.querySelector('img')).attr('src', prompt_images[i]);
		$(new_image.querySelector('.prompt_image_close')).attr('index', i);
		$(document.querySelector('#chat_images')).append(new_image);
	}
	let new_image_btn = document
		.querySelector('#prompt_image_add_btn')
		.content.cloneNode(true);
	document.querySelector('#chat_images').append(new_image_btn);
	$('.promt_image_btn').on('click', function (e) {
		e.preventDefault();
		$('#chat_add_image').click();
	});
	$('.prompt_image_close').on('click', function (e) {
		prompt_images.splice($(this).attr('index'), 1);
		updatePromptImages();
	});
}

function addImagetoChat(data) {
	console.log(data);
	if (prompt_images.filter(item => item == data).length == 0) {
		prompt_images.push(data);
		updatePromptImages();
	}
}

function initChat() {
	var mediaRecorder;
	let audioBlob;
	var chunks = [];
	var stream_;

	prompt_images = [];

	$('#scrollable_content').animate({ scrollTop: 1000 }, 200);
	// Start recording when the button is pressed
	$('#voice_record_button').click(function (e) {
		chunks = [];
		navigator.mediaDevices
			.getUserMedia({ audio: true })
			.then(function (stream) {
				stream_ = stream;
				mediaRecorder = new MediaRecorder(stream);
				$('#voice_record_button').addClass('hidden');
				$('#voice_record_stop_button').removeClass('hidden');
				isRecord = true;
				console.log(mediaRecorder);
				mediaRecorder.ondataavailable = function (e) {
					console.log(e.data);
					chunks.push(e.data);
				};
				mediaRecorder.start();
			})
			.catch(function (err) {
				console.log('The following error occurred: ' + err);
				toastr.warning('Audio is not allowed');
			});

		$('#voice_record_stop_button').click(function (e) {
			e.preventDefault();
			$('#voice_record_button').removeClass('hidden');
			$('#voice_record_stop_button').addClass('hidden');
			isRecord = false;
			mediaRecorder.onstop = function (e) {
				var blob = new Blob(chunks, { type: 'audio/mp3' });

				var formData = new FormData();
				var fileOfBlob = new File([blob], 'audio.mp3');
				formData.append('file', fileOfBlob);

				chunks = [];

				$.ajax({
					url: '/dashboard/user/openai/chat/transaudio',
					type: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					success: function (response) {
						if (response.length >= 4) {
							$('#prompt').val(response);
						}
					},
					error: function (error) {
						// Handle the error response
					},
				});
			};
			mediaRecorder.stop();
			stream_
				.getTracks() // get all tracks from the MediaStream
				.forEach(track => track.stop()); // stop each of them
		});
	});
	$('#btn_add_new_prompt').on('click', function (e) {
		prompt_title = $('#new_prompt_title').val();
		prompt = $('#new_prompt').val();

		if (prompt_title.trim() == '') {
			toastr.warning('Please input title');
			return;
		}

		if (prompt.trim() == '') {
			toastr.warning('Please input prompt');
			return;
		}

		$.ajax({
			type: 'post',
			url: '/dashboard/user/openai/chat/add-prompt',
			data: {
				title: prompt_title,
				prompt: prompt,
			},
			success: function (data) {
				promptsData = data;
				updatePrompts(data);
				$('.custom__popover__back').addClass('hidden');
				$('#custom__popover').removeClass('custom__popover__wrapper');
			},
			error: function () { },
		});
	});

	$('#add_btn').on('click', function (e) {
		$('#custom__popover').addClass('custom__popover__wrapper');
		$('.custom__popover__back').removeClass('hidden');
		e.stopPropagation();
	});

	$('.custom__popover__back').on('click', function () {
		$(this).addClass('hidden');
		$('#custom__popover').removeClass('custom__popover__wrapper');
	});

	$('#prompt_library').on('click', function (e) {
		e.preventDefault();

		$('#prompts').empty();

		$.ajax({
			type: 'post',
			url: '/dashboard/user/openai/chat/prompts',
			success: function (data) {
				filterType = 'all';
				promptsData = data.promptData;
				console.log('Update');
				favData = data.favData;
				updatePrompts(data.promptData);
				$('#modal').addClass('lqd-is-active');
				$('.modal__back').removeClass('hidden');
			},
			error: function () { },
		});
		e.stopPropagation();
	});

	$('.modal__back').on('click', function () {
		$(this).addClass('hidden');
		$('#modal').removeClass('lqd-is-active');
	});

	$(document).on('click', '.prompt', function () {
		const $promptInput = $('#prompt');
		selectedPrompt = Number($(this.querySelector('.favbtn')).attr('id'));
		$promptInput.val(
			promptsData.filter(item => item.id == selectedPrompt)[0].prompt
		);
		$('.modal__back').addClass('hidden');
		$('#modal').removeClass('lqd-is-active');
		selectedPrompt = -1;
		// updatePrompts(promptsData);
		console.log($(this.querySelector('.favbtn')).attr('id'));
		$promptInput.css('height', '5px');
		$promptInput.css('height', $promptInput[0].scrollHeight + 'px');
	});

	$(document).on('click', '.filter_btn', function () {
		$('.filter_btn').removeClass('active');
		$(this).addClass('active');
		filterType = $(this).attr('filter');
		console.log(filterType);
		updatePrompts(promptsData);
	});

	$(document).on('click', '.favbtn', function (e) {
		console.log($(this).attr('id'));
		updateFav(Number($(this).attr('id')));
		e.stopPropagation();
	});

	$('#chat_add_image').click(function () {
		openNewImageDlg();
	});

	$('#selectImageInput').change(function () {
		if (this.files && this.files[0]) {
			for (let i = 0; i < this.files.length; i++) {
				let reader = new FileReader();
				// Existing image handling code
				reader.onload = function (e) {
					var img = new Image();
					img.src = e.target.result;
					img.onload = function () {
						var canvas = document.createElement('canvas');
						var ctx = canvas.getContext('2d');
						canvas.height = (img.height * 200) / img.width;
						canvas.width = 200;
						ctx.drawImage(
							img,
							0,
							0,
							canvas.width,
							canvas.height
						);
						var base64 = canvas.toDataURL('image/png');
						addImagetoChat(base64);
					};
				};
				reader.readAsDataURL(this.files[i]);

			}
			document.getElementById('mainupscale_src') &&
				(document.getElementById('mainupscale_src').style.display =
					'none');
		}
	});

	$('#upscale_src').change(function () {
		if (this.files && this.files[0]) {
			for (let i = 0; i < this.files.length; i++) {
				let reader = new FileReader();

				reader.onload = function (e) {
					// console.log(e);
					addImagetoChat(e.target.result);
					// $( "#selectImageInput" ).val( '' );
				};
				console.log(this.files[i]);
				reader.readAsDataURL(this.files[i]);
			}
		}
		document.getElementById('mainupscale_src') &&
			(document.getElementById('mainupscale_src').style.display = 'none');
	});

	($ => {
		'use strict';
	
		function submitForm(ev) {

			ev?.preventDefault();
			document.getElementById('mainupscale_src') &&
				(document.getElementById('mainupscale_src').style.display = 'none');
			document.getElementById('sugg') &&
				(document.getElementById('sugg').style.display = 'none');
			const prompt = document.getElementById('prompt');
			const constvalue = prompt.value;
	
			prompt.value = '';
			prompt.style.height = '';
	
			let scrollLocked = false;
			let chatsContainer = $('.chats-container');
			let chatsScroller = $('.conversation-area');
	
			if (
				!constvalue ||
				constvalue.length === 0 ||
				constvalue.replace(/\s/g, '') === ''
			) {
				return toastr.error('Please fill the message field');
			}
	
			const category_id = document.getElementById('category_id');
			const chat_id = document.getElementById('chat_id');
			const submitBtn = document.getElementById('send_message_button');
	
			if (submitBtn.classList.contains('submitting')) return;
	
			submitBtn.disabled = true;
			submitBtn.classList.add('submitting');
	
			var formData = new FormData();
			formData.append('prompt', constvalue);
			formData.append('chat_id', chat_id.value);
			formData.append('category_id', category_id.value);
			if (document.getElementById("realtime").checked) {
				formData.append('realtime', 1);
			} else {
				formData.append('realtime', 0);
			}
	
			function onBeforePageUnload(e) {
				e.preventDefault();
				e.returnValue = '';
			}
	
			function onWindowScroll() {
				if (
					chatsScroller[0].scrollTop + chatsScroller[0].offsetHeight >=
					chatsScroller[0].scrollHeight
				) {
					scrollLocked = true;
				} else {
					scrollLocked = false;
				}
			}
	
			// to prevent from reloading when generating respond
			window.addEventListener('beforeunload', onBeforePageUnload);
	
			$.ajax({
				type: 'post',
				url: '/dashboard/user/openai/chat/chat-send',
				data: formData,
				contentType: false,
				processData: false,
				success: function (data) {
					chatsContainer = $('.chats-container');
					const userBubbleTemplate = document
						.querySelector('#chat_user_bubble')
						.content.cloneNode(true);
					const aiBubbleTemplate = document
						.querySelector('#chat_ai_bubble')
						.content.cloneNode(true);
					const { chat_id, message_id } = data;
	
					//Here you can append user input to the chat area
					userBubbleTemplate.querySelector('.chat-content').innerHTML =
						constvalue;
					chatsContainer.append(userBubbleTemplate);
	
					for (let i = 0; i < prompt_images.length; i++) {
						const chatImageBubbleTemplate = document
							.querySelector('#chat_user_image_bubble')
							.content.cloneNode(true);
						chatImageBubbleTemplate.querySelector('.img-content').src =
							prompt_images[i];
						chatsContainer.append(chatImageBubbleTemplate);
					}
	
					function implementChat(type, images) {
						if (images == undefined) images = '';
						const eventSource = new EventSource(
							'/dashboard/user/openai/chat/chat-send?chat_id=' +
							chat_id +
							'&message_id=' +
							message_id +
							'&type=' +
							type +
							'&images=' +
							images +
							'&pdfname=' +
							pdfName +
							'&pdfpath=' +
							pdfPath
						);
	
						//This is the div which the text will append continuously.
						let responseText = '';
	
						const aiBubbleWrapper = aiBubbleTemplate.firstElementChild;
						aiBubbleWrapper.classList.add('loading');
						aiBubbleTemplate.querySelector('.chat-content').innerHTML =
							responseText;
						chatsContainer.append(aiBubbleTemplate);
	
						aiBubbleWrapper.setAttribute('data-message-id', message_id);
	
						chatsScroller[0].scrollTo(0, chatsScroller[0].scrollHeight);
	
						chatsScroller[0].addEventListener('scroll', onWindowScroll);
	
						eventSource.onmessage = function (e) {
							aiBubbleWrapper.classList.remove('loading');
	
							if (e.data === '[DONE]') {
								//This is the area when the chat ends.
								eventSource.close();
								submitBtn.disabled = false;
								submitBtn.classList.remove('submitting');
								document.getElementById('chat_form').reset();
	
								window.removeEventListener(
									'beforeunload',
									onBeforePageUnload
								);
							}
							let txt = e.data;
							if (txt !== undefined && e.data != '[DONE]') {
								//This is the area which the text will append to the div
								responseText += txt.split('/**')[0];
								aiBubbleWrapper.querySelector(
									'.chat-content'
								).innerHTML = responseText;
	
								scrollLocked && chatsScroller[0].scrollTo(0, chatsScroller[0].scrollHeight);
							}
						};
	
						eventSource.onerror = function (e) {
							//If error from the openai.
							eventSource.close();
							submitBtn.disabled = false;
							submitBtn.classList.remove('submitting');
							aiBubbleWrapper.classList.remove('loading');
							document.getElementById('chat_form').reset();
	
							window.removeEventListener(
								'beforeunload',
								onBeforePageUnload
							);
							chatsScroller[0].removeEventListener(
								'scroll',
								onWindowScroll
							);
						};
					}
	
					if (prompt_images.length == 0) {
						implementChat('chat');
					} else {
						let temp = [...prompt_images];
						prompt_images = [];
						updatePromptImages();
						$.ajax({
							type: 'POST',
							url: '/images/upload',
							data: {
								images: temp,
							},
							success: function (result) {
								implementChat('vision', result.path);
							},
						});
					}
	
					// if (prompt_images.length == 0) {
					// 	implementChat('chat');
					// } else {
					// 	var temp = [...prompt_images];
					// 	prompt_images = [];
					// 	updatePromptImages();
					// 	$.ajax({
					// 		type: "POST",
					// 		url: '/images/upload',
					// 		data: {
					// 			images: temp
					// 		},
					// 		success: function (result) {
					// 			implementChat('vision', result.path);
					// 		}
					// 	});
					// }
				},
				error: function (data) {
					var err = data.responseJSON.errors;
					if (err) {
						$.each(err, function (index, value) {
							toastr.error(value);
						});
					} else {
						toastr.error(data.responseJSON.message);
					}
					submitBtn.disabled = false;
					submitBtn.classList.remove('submitting');
	
					window.removeEventListener('beforeunload', onBeforePageUnload);
					chatsScroller[0].removeEventListener('scroll', onWindowScroll);
				},
			});
	
			return false;
		}
	
		// $('body').off('submit', '#chat_form').on('submit', '#chat_form', submitForm);
		$("#send_message_button").off('click').on('click', submitForm);
	
		const prompt = document.getElementById('prompt');
	
		prompt?.addEventListener('keypress', ev => {
			if (ev.keyCode == 13) {
				ev.preventDefault();
				return submitForm();
			}
		});
	})(jQuery);
}

$(document).ready(initChat);
var prompt_images = [];

$(document).ready(function () {
	'use strict';

	var chatFilter = 'All';
	var serachFilter = '';

	$('.conversation-area').stop().animate({ scrollTop: $('.conversation-area')[0]?.scrollHeight }, 200);
	$('#scrollable_content')
		.stop()
		.animate({ scrollTop: $('#scrollable_content').outerHeight() }, 200);

	$('.chat-list-ul').on('click', 'a', function () {
		const parentLi = $(this).parent();
		parentLi.siblings().removeClass('active');
		parentLi.addClass('active');
	});

	$('#search_str').on('change', function () {
		serachFilter = $('#search_str').val().toLowerCase();
		updateChatsByFilter();
	});

	$('.chat_filter').on('click', function () {
		$('.chat_filter').removeClass('active');
		$(this).addClass('active');
		var filter = $(this).attr('data-filter');
		chatFilter = filter;
		updateChatsByFilter();
	});

	$('.fav_chat').on('click', function () {
		$.ajax({
			type: 'post',
			url: '/dashboard/admin/openai/chat/update-fav',
			data: {
				id: $(this).attr('id'),
			},
			success: function (data) {
				favData = data;
				updateChatsByFilter();
			},
			error: function () { },
		});
	});

	function updateChatsByFilter() {
		$('.chat_element').each(function () {
			$(this).removeClass('hidden');
			let id = $(this).attr('id');
			let isFav = favData.filter(item => item.item_id == id).length;

			if (isFav == 0) {
				$(this.querySelector('.selected')).addClass('hidden');
				$(this.querySelector('.unselected')).removeClass('hidden');
			} else {
				$(this.querySelector('.unselected')).addClass('hidden');
				$(this.querySelector('.selected')).removeClass('hidden');
			}

			let name = $(this.querySelector('.chat_name')).text().toLowerCase();
			let description = $(this.querySelector('.chat_description'))
				.text()
				.toLowerCase();
			if (
				(chatFilter == 'All' ||
					chatFilter == $(this).attr('category') ||
					(isFav != 0 && chatFilter == 'Favorite')) &&
				(name.includes(serachFilter) ||
					description.includes(serachFilter))
			) {
				$(this).removeClass('hidden');
			} else {
				$(this).addClass('hidden');
			}
		});
	}

	updateChatsByFilter();

	function saveChatNewTitle(chatId, newTitle) {
		var formData = new FormData();
		formData.append('chat_id', chatId);
		formData.append('title', newTitle);

		$.ajax({
			type: 'post',
			url: '/dashboard/user/openai/chat/rename-chat',
			data: formData,
			contentType: false,
			processData: false,
		});
		return false;
	}

	function deleteChatItem(chatId, chatTitle) {
		if (confirm(`Are you sure you want to remove ${chatTitle}?`)) {
			var formData = new FormData();
			formData.append('chat_id', chatId);

			$.ajax({
				type: 'post',
				url: '/dashboard/user/openai/chat/delete-chat',
				data: formData,
				contentType: false,
				processData: false,
				success: function (data) {
					//Remove chat li
					$('#' + chatId).hide();
					$('#chat_area_to_hide').hide();
				},
				error: function (data) {
					var err = data.responseJSON.errors;
					if (err) {
						$.each(err, function (index, value) {
							toastr.error(value);
						});
					} else {
						toastr.error(data.responseJSON.message);
					}
				},
			});
			return false;
		}
	}

	$('.chat-list-ul').on('click', '.chat-item-delete', ev => {
		const button = ev.currentTarget;
		const parent = button.closest('li');
		const chatId = parent.getAttribute('id');
		const chatTitle = parent.querySelector('.chat-item-title').innerText;
		deleteChatItem(chatId, chatTitle);
	});

	$('.chat-list-ul').on('click', '.chat-item-update-title', ev => {
		const button = ev.currentTarget;
		const parent = button.closest('.chat-list-item');
		const title = parent.querySelector('.chat-item-title');
		const chatId = parent.getAttribute('id');
		const currentText = title.innerText;

		function setEditMode(mode) {
			if (mode === 'editStart') {
				parent.classList.add('edit-mode');

				title.setAttribute('data-current-text', currentText);
				title.setAttribute('contentEditable', true);
				title.focus();
				window.getSelection().selectAllChildren(title);
			} else if (mode === 'editEnd') {
				parent.classList.remove('edit-mode');

				title.removeAttribute('contentEditable');
				title.removeAttribute('data-current-text');
			}
		}

		function keydownHandler(ev) {
			const { key } = ev;
			const escapePressed = key === 'Escape';
			const enterPressed = key === 'Enter';

			if (!escapePressed && !enterPressed) return;

			ev.preventDefault();

			if (escapePressed) {
				title.innerText = currentText;
			}

			if (enterPressed) {
				saveChatNewTitle(chatId, title.innerText);
			}

			setEditMode('editEnd');
			document.removeEventListener('keydown', keydownHandler);
		}

		// if alreay editting then turn the edit button to a save button
		if (title.hasAttribute('contentEditable')) {
			setEditMode('editEnd');
			document.removeEventListener('keydown', keydownHandler);
			return saveChatNewTitle(chatId, title.innerText);
		}

		$('.chat-list-ul .edit-mode').each((i, el) => {
			const title = el.querySelector('.chat-item-title');
			title.innerText = title.getAttribute('data-current-text');
			title.removeAttribute('data-current-text');
			title.removeAttribute('contentEditable');
			el.classList.remove('edit-mode');
		});

		setEditMode('editStart');

		document.addEventListener('keydown', keydownHandler);
	});
});

/*

DO NOT FORGET TO ADD THE CHANGES TO BOTH FUNCTION makeDocumentReadyAgain and the document ready function on the top!!!!

 */

function makeDocumentReadyAgain() {
	$(document).ready(function () {
		'use strict';

		$('.conversation-area').stop().animate({ scrollTop: $('.conversation-area')[0]?.scrollHeight }, 200);
		$('#scrollable_content').stop().animate({ scrollTop: $('#scrollable_content').outerHeight() }, 200);

		$('.chat-list-ul').on('click', 'a', function () {
			const parentLi = $(this).parent();
			parentLi.siblings().removeClass('active');
			parentLi.addClass('active');
		});

		function saveChatNewTitle(chatId, newTitle) {
			var formData = new FormData();
			formData.append('chat_id', chatId);
			formData.append('title', newTitle);

			$.ajax({
				type: 'post',
				url: '/dashboard/user/openai/chat/rename-chat',
				data: formData,
				contentType: false,
				processData: false,
			});
			return false;
		}

		function deleteChatItem(chatId, chatTitle) {
			if (confirm(`Are you sure you want to remove ${chatTitle}?`)) {
				var formData = new FormData();
				formData.append('chat_id', chatId);

				$.ajax({
					type: 'post',
					url: '/dashboard/user/openai/chat/delete-chat',
					data: formData,
					contentType: false,
					processData: false,
					success: function (data) {
						//Remove chat li
						$('#' + chatId).hide();
						$('#chat_area_to_hide').hide();
					},
					error: function (data) {
						var err = data.responseJSON.errors;
						if (err) {
							$.each(err, function (index, value) {
								toastr.error(value);
							});
						} else {
							toastr.error(data.responseJSON.message);
						}
					},
				});
				return false;
			}
		}

		$('.chat-list-ul').on('click', '.chat-item-delete', ev => {
			const button = ev.currentTarget;
			const parent = button.closest('li');
			const chatId = parent.getAttribute('id');
			const chatTitle =
				parent.querySelector('.chat-item-title').innerText;
			deleteChatItem(chatId, chatTitle);
		});

		$('.chat-list-ul').on('click', '.chat-item-update-title', ev => {
			const button = ev.currentTarget;
			const parent = button.closest('.chat-list-item');
			const title = parent.querySelector('.chat-item-title');
			const chatId = parent.getAttribute('id');
			const currentText = title.innerText;

			function setEditMode(mode) {
				if (mode === 'editStart') {
					parent.classList.add('edit-mode');

					title.setAttribute('data-current-text', currentText);
					title.setAttribute('contentEditable', true);
					title.focus();
					window.getSelection().selectAllChildren(title);
				} else if (mode === 'editEnd') {
					parent.classList.remove('edit-mode');

					title.removeAttribute('contentEditable');
					title.removeAttribute('data-current-text');
				}
			}

			function keydownHandler(ev) {
				const { key } = ev;
				const escapePressed = key === 'Escape';
				const enterPressed = key === 'Enter';

				if (!escapePressed && !enterPressed) return;

				ev.preventDefault();

				if (escapePressed) {
					title.innerText = currentText;
				}

				if (enterPressed) {
					saveChatNewTitle(chatId, title.innerText);
				}

				setEditMode('editEnd');
				document.removeEventListener('keydown', keydownHandler);
			}

			// if alreay editting then turn the edit button to a save button
			if (title.hasAttribute('contentEditable')) {
				setEditMode('editEnd');
				document.removeEventListener('keydown', keydownHandler);
				return saveChatNewTitle(chatId, title.innerText);
			}

			$('.chat-list-ul .edit-mode').each((i, el) => {
				const title = el.querySelector('.chat-item-title');
				title.innerText = title.getAttribute('data-current-text');
				title.removeAttribute('data-current-text');
				title.removeAttribute('contentEditable');
				el.classList.remove('edit-mode');
			});

			setEditMode('editStart');

			document.addEventListener('keydown', keydownHandler);
		});
	});
}

function openChatAreaContainer(chat_id) {
	'use strict';

	$(`#chat_${chat_id}`).addClass('active').siblings().removeClass('active');

	document.getElementById('mainupscale_src') &&
		(document.getElementById('mainupscale_src').style.display = 'none');
	document.getElementById('sugg') &&
		(document.getElementById('sugg').style.display = 'none');

	var formData = new FormData();
	formData.append('chat_id', chat_id);

	$.ajax({
		type: 'post',
		url: '/dashboard/user/openai/chat/open-chat-area-container',
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			$('#load_chat_area_container').html(data.html);
			initChat();
			if (data.lastThreeMessage != '') {
				document.getElementById('mainupscale_src') &&
					(document.getElementById('mainupscale_src').style.display =
						'none');
				document.getElementById('sugg') &&
					(document.getElementById('sugg').style.display = 'none');
			}
			setTimeout(function () {
				$('.conversation-area').stop().animate({ scrollTop: $('.conversation-area')[0].scrollHeight }, 200);
			}, 750);
		},
		error: function (data) {
			var err = data.responseJSON.errors;
			if (err) {
				$.each(err, function (index, value) {
					toastr.error(value);
				});
			} else {
				toastr.error(data.responseJSON.message);
			}
		},
	});
	return false;
}

function startNewChat(category_id, local) {
	'use strict';
	var formData = new FormData();
	formData.append('category_id', category_id);

	$.ajax({
		type: 'post',
		url: '/' + local + '/dashboard/user/openai/chat/start-new-chat',
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			chatid = data.chat.id;
			$('#load_chat_area_container').html(data.html);
			$('#chat_sidebar_container').html(data.html2);
			makeDocumentReadyAgain();
			initChat();

			setTimeout(function () {
				$('.conversation-area').stop().animate({ scrollTop: $('.conversation-area').outerHeight() }, 200);
			}, 750);
		},
		error: function (data) {
			var err = data.responseJSON.errors;
			if (err) {
				$.each(err, function (index, value) {
					toastr.error(value);
				});
			} else {
				toastr.error(data.responseJSON.message);
			}
		},
	});
	return false;
}

/* microphone (speech to text) */
const microphoneButton = document.querySelector('#chat-microphone');
let isTranscribing = false; // Initially not transcribing

if (microphoneButton) {
	if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
		const recognition = new (window.SpeechRecognition ||
			window.webkitSpeechRecognition)();

		recognition.continuous = true;

		recognition.addEventListener('start', () => {
			$msgSendBtn.attr('disabled', true);
			$('#chat-microphone')
				.find('i')
				.removeClass('fa-microphone')
				.addClass('fa-stop-circle');
		});

		recognition.addEventListener('result', event => {
			const transcript = event.results[0][0].transcript;
			$msgInput.val($msgInput.val() + transcript + ' ');

			microphoneButton.click();
		});

		recognition.addEventListener('end', () => {
			$msgSendBtn.attr('disabled', false);
			$('#chat-microphone')
				.find('i')
				.addClass('fa-microphone')
				.removeClass('fa-stop-circle');
			isTranscribing = false;
		});

		microphoneButton.addEventListener('click', () => {
			if (!isTranscribing) {
				// Start transcription if not transcribing
				recognition.start();
				isTranscribing = true;
			} else {
				// Stop transcription if already transcribing
				recognition.stop();
				isTranscribing = false;
			}
		});
	} else {
		console.log('Web Speech Recognition API not supported by this browser');
		$('#chat-microphone').hide();
	}
}

$(document).ready(function () {
	$('#chat_search_word').on('keyup', function () {
		return searchChatFunction();
	});

	$('#prompt').on('input', ev => {
		const el = ev.target;
		el.style.height = '5px';
		el.style.height = el.scrollHeight + 'px';
	});
});

function searchChatFunction() {
	'use strict';

	const categoryId = $('#chat_search_word').data('category-id');
	const formData = new FormData();
	formData.append(
		'_token',
		document.querySelector('input[name=_token]')?.value
	);
	formData.append(
		'search_word',
		document.getElementById('chat_search_word').value
	);
	formData.append('category_id', categoryId);

	$.ajax({
		type: 'POST',
		url: '/dashboard/user/openai/chat/search',
		data: formData,
		contentType: false,
		processData: false,
		success: function (result) {
			$('#chat_sidebar_container').html(result.html);
			$(document).trigger('ready');
		},
	});
}

function startNewDocChat(file, type) {
	'use strict';
	document.querySelector('#app-loading-indicator')?.classList?.remove('opacity-0');
	$("#btn_upload_document").attr('disabled', true);

	let category_id = $('#chat_search_word').data('category-id');

	var formData = new FormData();
	formData.append('category_id', category_id);
	formData.append('doc', pdf);
	formData.append('type', type);

	$.ajax({
		type: 'post',
		url: '/dashboard/user/openai/chat/start-new-doc-chat',
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			$("#selectDocInput").val("");
			$("#btn_upload_document").attr('disabled', false);
			document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
			chatid = data.chat.id;
			$('#load_chat_area_container').html(data.html);
			$('#chat_sidebar_container').html(data.html2);

			initChat();
			makeDocumentReadyAgain();
			setTimeout(function () {
				$('.conversation-area').stop().animate({ scrollTop: $('.conversation-area').outerHeight() }, 200);
			}, 750);
		},
		error: function (data) {
			$("#selectDocInput").val("");
			$("#btn_upload_document").attr('disabled', false);
			document.querySelector('#app-loading-indicator')?.classList?.add('opacity-0');
			var err = data.responseJSON.errors;
			if (err) {
				$.each(err, function (index, value) {
					toastr.error(value);
				});
			} else {
				toastr.error(data.responseJSON.message);
			}
		},
	});
	return false;
}

$('#selectDocInput').change(function () {
	if (this.files && this.files[0]) {
		let reader = new FileReader();
		pdf = this.files[0];
		startNewDocChat(pdf, this.files[0].type);
		document.getElementById('mainupscale_src') &&
			(document.getElementById('mainupscale_src').style.display =
				'none');
	}
});

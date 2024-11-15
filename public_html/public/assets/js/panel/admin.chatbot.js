jQuery(document).ready(function($) {
    "use strict";

    let count = $('[data-select="item"]:checked').length;


    $('#selected-count').text(count);

    let pages_total_count = $('#pages_total_count').val();

    if(pages_total_count == count) {
        $('[data-select="all"]').text('Unselect All');
    }

    $(document).on('click', '[data-edit="item-qa"]', function () {
        let id = $(this).data('id');
        let question = $(this).data('question');
        let answer = $(this).data('answer');

        $('#qa_id').val(id);

        $('#question').val(question);

        $('#answer').val(answer);

    })

    $(document).on('click', '[data-edit="item"]', function () {
        let id = $(this).data('id');
        let title = $(this).data('title');
        let text = $(this).data('text');

        $('#text_id').val(id);

        $('#title').val(title);

        $('#content_text').val(text);

    })

    $(document).on('click', '[data-submit="addtext"]', function () {

        let formId = $(this).data('form');

        let form = $(formId);

        let data = form.serializeArray();

        let url = form.attr('action');

        $('.crawler-spinner').removeClass('d-none');


        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (response) {
                if(response.status == 'error') {
                    toastr.error(response.message);
                } else {
                    toastr.success(response.message);
                }

                $('#text-list').html(response.content);

                $('.crawler-spinner').addClass('d-none');

                $('#text-list').removeClass('d-none');

                $('#text_id').val('');

                $('#title').val('');

                $('#content_text').val('');

                if (response.count) {
                    $('#text-list-alert').removeClass('d-none');
                }
            },
            error: function (response) {
                $('.crawler-spinner').addClass('d-none');
                toastr.error(response.responseJSON.message);
            }
        });

        console.log(data, url);
    });

    $(document).on('click', '[data-submit="addqa"]', function () {

        let formId = $(this).data('form');

        let form = $(formId);

        let data = form.serializeArray();

        let url = form.attr('action');

        $('.crawler-spinner').removeClass('d-none');

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (response) {
                if(response.status == 'error') {
                    toastr.error(response.message);
                } else {
                    toastr.success(response.message);
                }

                $('#qa-list').html(response.content);

                $('.crawler-spinner').addClass('d-none');

                $('#qa-list').removeClass('d-none');

                $('#qa_id').val('');

                $('#question').val('');

                $('#answer').val('');

                if (response.count) {
                    $('#text-list-alert').removeClass('d-none');
                }
            },
            error: function (response) {
                $('.crawler-spinner').addClass('d-none');
                toastr.error(response.responseJSON.message);
            }
        });

        console.log(data, url);
    });

    $(document).on('click','#web-site-form-submit', function () {
        let action = $(this).data('action');
        let url = $('[name="url"]').val();
        let typeRadio = $('[name="type"]:checked').val();

        if (!url) {
            toastr.error('Please enter the URL');
            return;
        }

        $('.crawler-spinner').removeClass('d-none');
        $('#pages').addClass('d-none');

        $.ajax({
            type: "POST",
            url: action,
            data: {url: url, type: typeRadio},
            success: function (response) {

                if(response.status == 'error') {
                    toastr.error(response.message);
                } else {
                    toastr.success(response.message);
                }

                $('#pages').html(response.content);

                $('.crawler-spinner').addClass('d-none');
                $('#pages').removeClass('d-none');

                $('#selected-count').text(0);

            },
            error: function (response) {
                toastr.error(response.responseJSON.message);
            }
        });
    })

    $(document).on('click', '[data-item="delete"]', function () {
        let parent = $(this).data('parent');

        let url = $(this).data('url');

        if(confirm('Are you sure? This is item and will delete.')) {
            $.ajax({
                type: "DELETE",
                url: url,
                success: function(result) {
                    if(result.status == 'error') {
                        toastr.error(result.message);
                    } else {
                        toastr.success(result.message);
                    }

                    $(parent).remove();

                    let count = $('[data-select="item"]:checked').length;

                    $('#selected-count').text(count);
                }
            });
        }
    });

    $(document).on('click','[data-submit="train"]' , function () {

        let formId = $(this).data('form');

        let list = $(this).data('list');

        let form = $(formId);

        let data = form.serializeArray();

        let url = form.attr('action');

        $('.crawler-spinner').removeClass('d-none');

        let alertTrain = $('.text-list-alert');

        if (alertTrain) {
            alertTrain.addClass('d-none');
        }

        let pages = $(list);

        if (pages) {
            pages.addClass('d-none');
        }

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(result) {
                if (pages) {
                    pages.html(result.content);
                    pages.removeClass('d-none');
                }

                if (alertTrain) {
                    alertTrain.removeClass('d-none');
                }

                $('.crawler-spinner').addClass('d-none');

                if(result.status == 'error') {
                    toastr.error(result.message);
                } else {
                    toastr.success(result.message);
                }

            },
            error: function (response) {
                $('.crawler-spinner').addClass('d-none');

                if (pages) {
                    pages.html(result.content);
                    pages.removeClass('d-none');
                }

                toastr.error(response.responseJSON.message);
            }
        });

    });

    $(document).on('change', '[data-select="item"]', function () {

        let count = $('[data-select="item"]:checked').length;

        $('#selected-count').text(count);
    });

    $(document).on('click', '[data-select="all"]', function () {

        let count = $('[data-select="item"]:checked').length;

        $('#selected-count').text(count);

       let pages_total_count = $('#pages_total_count').val();

        console.log(pages_total_count, count);

       if(pages_total_count == count) {
            $('[data-select="item"]').prop('checked', false);
            $('[data-select="all"]').text('Select All');
            $('#selected-count').text($('[data-select="item"]:checked').length);
       } else {
           $('[data-select="item"]').prop('checked', true);
           $('[data-select="all"]').text('Unselect All');
           $('#selected-count').text($('[data-select="item"]:checked').length);
       }

    });

    $(document).on('change', '[data-select="item"]', function () {
        let count = $('[data-select="item"]:checked').length;

        $('#selected-count').text(count);

        let pages_total_count = $('#pages_total_count').val();

        if(pages_total_count == count) {
            $('[data-select="all"]').text('Unselect All');
        }else{
            $('[data-select="all"]').text('Select All');
        }
    });
})


function dropHandler(ev, id) {
    // Prevent default behavior (Prevent file from being opened)

    ev.preventDefault();
    $('#' + id)[0].files = ev.dataTransfer.files;
    $('#' + id).prev().find(".file-name").text(ev.dataTransfer.files[0].name);

    for (let i = 0; i < ev.dataTransfer.files.length; i++) {

        let reader = new FileReader();
        // Existing image handling code
        reader.onload = function(e) {
            var img = new Image();
            img.src = e.target.result;
            img.onload = function() {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                canvas.height = img.height * 200 / img.width;
                canvas.width = 200;
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                var base64 = canvas.toDataURL('image/png');
                addImagetoChat(base64);
            }
        };
        reader.readAsDataURL(ev.dataTransfer.files[id]);
    }
    document.getElementById('mainupscale_src').style.display = 'none';
}

function dragOverHandler(ev) {
    // Prevent default behavior (Prevent file from being opened)
    ev.preventDefault();
}

function handleFileSelect(id) {
    $('#' + id)
        .prev()
        .find(".file-name")
        .text($('#' + id)[0].files[0].name);

    let input = $('#' + id);


    let action = input.data('action');


    var formData = new FormData();

    $('.pdf-list').addClass('d-none');

    // add assoc key values, this will be posts values
    formData.append("file", input[0].files[0]);
    $('.crawler-spinner').removeClass('d-none');
    $.ajax({
        url: action,
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            $('#' + id).prev().find(".file-name").text('Upload a PDF File (Max: 25Mb)');
            $('.pdf-list').html(data.content);
            $('.crawler-spinner').addClass('d-none');
            $('.pdf-list').removeClass('d-none');

            if(data.status == 'error') {
                toastr.error(data.message);
            } else {
                toastr.success(data.message);
            }

        },
        error: function (data) {
            console.log(data);
        }
    });
}
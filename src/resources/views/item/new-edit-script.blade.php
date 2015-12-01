$(function()
{
    $('.sell [name=type]').on('change', function()
    {
        var type_val = $(this).val();

        $('.sell .options').each(function()
        {
            if ($(this).data('type') == type_val) {
                $(this).show().find('input, select').prop('disabled', false);
            } else {
                $(this).hide().find('input, select').prop('disabled', true);
            }
        });
    }).trigger('change');

    $('.sell .delivery-option, .sell .collection, .sell .returns').hide();

    $('.sell input[name=collection]').on('click', function()
    {
        $('.sell .collection').toggle($(this).prop('checked'));

        $('.sell input[name=payment_other]')
            .prop('disabled', !$(this).prop('checked'));
    }).triggerHandler('click');

    $('.sell #add-delivery-option').on('click', function(e)
    {
        e.preventDefault();

        var delivery_option = $('.sell .delivery-option').last();

        if (delivery_option.is(':visible')) {
            var id = parseInt(delivery_option.data('id')) + 1;

            var new_delivery_option = delivery_option.clone(true);

            new_delivery_option.data('id', id).find('input').val('').each(function()
            {
                $(this).attr('name', $(this).attr('name').replace(/[0-9]+/, id));
            });

            delivery_option.after(new_delivery_option);
        } else {
            delivery_option.data('id', 1);

            delivery_option.show();
        }
    });

    $('.sell #remove-delivery-option').on('click', function()
    {
        var delivery_option = $(this).parents('.delivery-option');

        if (delivery_option.data('id') > 1) {
            delivery_option.remove();
        } else {
            delivery_option.hide();
        }
    });

    $('.sell input[name=returns]').on('click', function()
    {
        $('.sell .returns').toggle($(this).prop('checked'));
    }).triggerHandler('click');

    Dropzone.autoDiscover = false;

    $('.sell .fallback').hide();

    if ($('.dropzone-previews').length) {
        $('.sell form.photos').dropzone({
            paramName: 'photos',
            clickable: '.dropzone-target',
            addRemoveLinks: true,
            previewsContainer: '.dropzone-previews',
            uploadMultiple: true,
            parallelUploads: 100,
            maxFiles: 100,
            fallback: function()
            {
                $('.sell .fallback').show();
            },
            init: function()
            {
                dropzone = this;

                $('.dropzone-existing div').each(function()
                {
                    var mock = { name: $(this).data('filename'), size: $(this).data('filesize') };

                    dropzone.options.addedfile.call(dropzone, mock);

                    dropzone.options.thumbnail.call(dropzone, mock, $(this).data('filepath'));
                });
            }
        });

        if ($('.sell form.photos').length) {
            Dropzone.forElement('.sell form.photos').on('sendingmultiple', function()
            {
                $('#submit').prop('disabled', true);

                $('#upload-progress').show();
            }).on('totaluploadprogress', function(progress)
            {
                $('#upload-progress').find('.meter').css('width', progress + '%');
            }).on('queuecomplete', function(progress)
            {
                $('#submit').prop('disabled', false);

                $('#upload-progress').hide();
            }).on('removedfile', function(file)
            {
                $.post('/item/delete-photo', { file: file.name, _token: "{{ csrf_token() }}" });
            });
        }
    }

    $('#upload-progress').hide();

    $('#new-item').submit(function()
    {
        return !$('#submit').is(':disabled');
    });
});

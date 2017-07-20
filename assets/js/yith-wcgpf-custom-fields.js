jQuery( function ( $ ) {
    var wrapper  = $( '#yith-wcgpf-custom-fields-tab-wrapper' ),
        save_btn = $( '#yith-wcgpf-custom-fields-tab-actions-save' ),
        ajax_request;

    wrapper.on( 'click', '.yith-wcgpf-add-row', function ( e ) {
            var current_target = $( e.target ),
                parent         = current_target.closest( '.yith-wcgpf-custom-field-wrap' ),
                parent_clone   = parent.clone();

            parent_clone.find( 'input:text' ).val( '' );
            parent.after( parent_clone );
        } )

        .on( 'click', '.yith-wcgpf-delete-row', function ( e ) {
            var number_of_custom_fields = wrapper.find( '.yith-wcgpf-custom-field-wrap' ).length,
                current_target          = $( e.target ),
                parent                  = current_target.closest( '.yith-wcgpf-custom-field-wrap' );

            if ( number_of_custom_fields > 1 ) {
                parent.remove();
            }else{
                parent.find( 'input:text' ).val( '' );
            }
        } );

    save_btn.on('click',this, function( e ) {
        e.preventDefault(),
        $('#ywcgpf-form').block({message:null, overlayCSS:{background:"#fff",opacity:.6}});
        var custom_fields = $("input[name='yith-wcgpf-custom-field[]']").map(function(){return $(this).val();}).get();
        var post_data = {
            'custom_fields': custom_fields ,
            action: 'yith_wcgpf_save_custom_fields'
        };

        $.ajax({
            type    : "POST",
            data    : post_data,
            url     : yith_wcgpf_custom_fields_tab_js.ajaxurl,
            success : function ( response ) {
                $('#ywcgpf-form').unblock();
            },
            complete: function () {
            }
        });
    });


} );
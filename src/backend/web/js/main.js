$(function(){
    //表格选中
    $(document).delegate('.kv-grid-container table tr', 'click', function(e){
        $(this).each(function(){
            if ( $(this).hasClass('grid-column-focus-color') ) {
                $(this).removeClass('grid-column-focus-color');
            } else {
                $(this).addClass('grid-column-focus-color');
            }
        });
    });
});
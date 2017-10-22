;function ChannelMultiSelect(args)
{
    this.channel_id = args.channel_id;
    this.pos_id = args.pos_id;
    this.selected_values_id = args.selected_values_id;
}

ChannelMultiSelect.prototype = {
    channelChangeEvent: function(){
        var pos_id = this.pos_id;
        $(this.channel_id).on('change', function(){
            var channelArr = $(this).val();
            var channels = '';
            if (channelArr && channelArr.length > 0) {
                channels = channelArr.join(',');
            }
            $.getJSON('/api/get-pos-by-channel', {channels: channels}, function(data){
                $(pos_id).empty();
                var rData = data.data;
                var options = '';
                if (rData) {
                    var i = 0;
                    for (i; i < rData.length; i ++) {
                        options += '<option value="'+rData[i]['id']+'">' + rData[i]['name'] + '</option>';
                    }
                    $(pos_id).html(options);
                    $(pos_id).multiselect('rebuild');
                }
            });
        });
    },
    common: function(){
        var channelArr = $(this.channel_id).val();
        var channels = '';
        if (channelArr && channelArr.length > 0) {
            channels = channelArr.join(',');
        }
        var selectedPos = $(this.selected_values_id).val();
        var selectedArr  = [];
        if (selectedPos) {
            selectedArr = selectedPos.split(',')
        }
        var pos_id = this.pos_id;
        $.getJSON('/api/get-pos-by-channel', {channels: channels}, function(data){
            $(pos_id).empty();
            var rData = data.data;
            var options = '';
            if (rData) {
                var i = 0;
                for (i; i < rData.length; i ++) {
                    //判断选中
                    var ch = '';
                    if (selectedArr.length > 0) {
                        if ($.inArray(rData[i]['id'], selectedArr) > -1) {
                            ch = 'selected';
                        }
                    }
                    options += '<option value="'+rData[i]['id']+'" '+ch+'>' + rData[i]['name'] + '</option>';
                }
                $(pos_id).html(options);
                $(pos_id).multiselect('rebuild');
            }
        });
    },
    start: function(){
        this.common();
        this.channelChangeEvent();
        $(this.pos_id).multiselect(
            {   includeSelectAllOption: true,
                selectAllText: '全选',
                filterPlaceholder: '请选择...',
                nonSelectedText: '未选择',
                enableFiltering: true,
                numberDisplayed: 0
            }
        );
    }
};


//
//
// $('#possearch-channel_id').change(function(){
//     //选中操作
//     var channelArr = $(this).val();
//     var channels = '';
//     if (channelArr && channelArr.length > 0) {
//         channels = channelArr.join(',');
//     }
//     $.getJSON('/api/get-pos-by-channel', {channels: channels}, function(data){
//         $("#pos_select_multi").empty();
//         var rData = data.data;
//         var options = '';
//         if (rData) {
//             var i = 0;
//             for (i; i < rData.length; i ++) {
//                 options += '<option value="'+rData[i]['id']+'">' + rData[i]['name'] + '</option>';
//             }
//             $("#pos_select_multi").html(options);
//             $("#pos_select_multi").multiselect('rebuild');
//         }
//     });
//
// });
//
//
// function init () {
//     var channelArr = $('#possearch-channel_id').val();
//     var channels = '';
//     if (channelArr && channelArr.length > 0) {
//         channels = channelArr.join(',');
//     }
//     var selectedPos = $('#selected_pos_id').val();
//     var selectedArr  = [];
//     if (selectedPos) {
//         selectedArr = selectedPos.split(',')
//     }
//     $.getJSON('/api/get-pos-by-channel', {channels: channels}, function(data){
//         $("#pos_select_multi").empty();
//         var rData = data.data;
//         var options = '';
//         if (rData) {
//             var i = 0;
//             for (i; i < rData.length; i ++) {
//                 //判断选中
//                 var ch = '';
//                 if ($.inArray(rData[i]['id'], selectedArr) > -1) {
//                     ch = 'selected';
//                 }
//                 options += '<option value="'+rData[i]['id']+'" '+ch+'>' + rData[i]['name'] + '</option>';
//             }
//             $("#pos_select_multi").html(options);
//             $("#pos_select_multi").multiselect('rebuild');
//         }
//     });
// }
//
// init();
//
// $("#pos_select_multi").multiselect(
//     {   includeSelectAllOption: true,
//         selectAllText: '全选',
//         filterPlaceholder: '请选择...',
//         nonSelectedText: '未选择',
//         enableFiltering: true,
//         numberDisplayed: 0,
//         onChange: function(element, checked) {
//
//         }
//     }
// );
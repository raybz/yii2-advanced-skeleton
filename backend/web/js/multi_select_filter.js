;
var  localStore = {
    get: function(key){
        return localStorage.getItem(key);
    },
    set: function(key, item){
        return localStorage.setItem(key, item);
    },
    remove: function(key){
        return localStorage.removeItem(key);
    },
    clear: function(){
        return localStorage.clear();
    }
};

function MultiSelectFilter (args) {
    this.select_id = args.select_id;
    this.tableSelector = $(args.tableSelector);
    this.show_columns = args.show_columns;
    this.name = args.name;
    this.prefix = 'mdata_2144_cn_' + this.name + '_multi';
    this.columns = [];
}

MultiSelectFilter.prototype = {
    getAllColumns: function(){
        var columns = [];
        this.tableSelector.find('tr').find('th').each(function(k, e){
            var h_tmp = $(e).html();
            h_tmp = h_tmp.replace(/<[^>]+>/g, "");
            columns.push({title:h_tmp,show:true,index:k});
        });
        this.columns = columns;
        $('#' + this.show_columns).data('all', columns);
    },
    renderSelect: function(){
        $('#' + this.select_id).html('');
        if ( localStore.get(this.prefix) != null) {
            $('#' + this.show_columns).val(localStore.get(this.prefix));
        }
        var show_columns = $('#' + this.show_columns).val().split(',');
        for (var key in this.columns) {
            var i = this.columns[key];
            if($.inArray(i.index+"", show_columns) == -1){
                $("#" + this.select_id).append("<option value='" + i.index + "'  >" + i.title + "</option>");
                i.show = false;
            }else{
                $("#" + this.select_id).append("<option value='" + i.index + "' selected='selected'>" + i.title + "</option>");
                i.show = true;
            }
        }
        var obj = this;
        $('#' + this.select_id).multiselect(
            {   includeSelectAllOption: false,
                filterPlaceholder: '请选择...',
                nonSelectedText: '未选择',
                enableFiltering: false,
                enableCaseInsensitiveFiltering: true,

            onChange: function(element, checked) {
                    var selectColumns = $('#' + obj.select_id).val();
                    localStore.set(obj.prefix, selectColumns.join(','));
                    $('#' + obj.show_columns).val(selectColumns.join(','));
                    for (var i in obj.columns) {
                        var item = obj.columns[i];
                        if ($.inArray(item.index, selectColumns) != -1) {
                            obj.columns[i]['show'] = true;
                        } else {
                            obj.columns[i]['show'] = false;
                        }
                    }
                    obj.showTableColumn();
                }
            }
        );
        //显示正常的列
        localStore.set(this.prefix, $('#' + this.show_columns).val());
    },
    showTableColumn: function(){
        var columns =  this.columns;
        var shows =  localStore.get(this.prefix).split(",");
        for (var key in columns) {
            var k_tmp = columns[key];
            if($.inArray(k_tmp.index+"", shows) == -1){
                this.tableSelector.find("tr").each(function (i, k) {
                    $(k).find("td").eq(k_tmp.index).hide();
                    $(k).find("th").eq(k_tmp.index).hide();
                });
            }else{
                this.tableSelector.find('tr').each(function (i, k) {
                    $(k).find("td").eq(k_tmp.index).show();
                    $(k).find("th").eq(k_tmp.index).show();
                });
            }
        }
    },
    start: function(){
        this.getAllColumns();
        this.renderSelect();
        this.showTableColumn();
    }
};

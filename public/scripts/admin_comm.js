


//控制退格键使网页退回
$(document).keyup(function (event) {
            let AltPressed = false;
            let f5Pressed = false;
            if (event.altKey || event.metaKey) {
                AltPressed = true;
            }
            if (event.key === 'F5' || event.key === 'f5') {
                f5Pressed = true;
            }
            if(AltPressed && f5Pressed ){
                Debug('parent', parent);
                console.log("Alt+F5 pressed");
                if(parent && parent.RefrushSub){
                    parent.RefrushSub();
                }

                console.log('点击完成。');
            }
}); 

//根据表格头 填充 列
function FillField(name, arr) { 
    let FieldIndex = -1;
    $('.layui-table-header table thead tr').each(function(){
        $(this).find('th').each(function(index, element){
            var title = $(this).attr('title');
            // Debug('title:' + title + ', name:' + name);
            // Debug('index:' + index + ', element:' , element);
            if(title == name){
                FieldIndex = index;
                Debug('FillField FieldIndex FIND :' + FieldIndex);
            }
        });
    });
    if(FieldIndex == -1){
        return;
    }
    $('.layui-table-body table tbody tr').each(function(index, element){
        $(this).find('td').each(function(tdindex){
            if(tdindex == FieldIndex){
                var val = $(this).text();
                if(null == val || val == ''){
                    val =  $(this).find('input[type=hidden].typeid').val();
                    Debug('hidden val:' + val);
                    if(null == val  || '' == val){
                        return true;
                    }
                }
                for (var i = 0; i < arr.length; i++) {
                    var row = arr[i];
                    if (row.TypeId == val) {
                        $(this).children('div.layui-table-cell').text(row.TypeName);
                    }
                }
            }
        });
    });

}


function GetName4TypeId(arr, id, defname = '') {
    if(0 == arr.length){
        return '<input type="hidden" class="typeid" value="'+id+'">';
    }
    for (var i = 0; i < arr.length; i++) {
        var row = arr[i];
        if (row.TypeId == id) {
            return row.TypeName;
        }
    }
    return defname;
}
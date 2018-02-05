$('form.ajax').on('submit', function() {
     var that = $(this),
         url = that.attr('action'),
         type = that.attr('method'),
         data = {};

     that.find(['name']).each(function(index, value) {
         var that = $(this),
             name = that.attr('name'),
             value = that.val();

         data[name] = value;
     });

     $.ajax({
         url:"<?php echo $this->baseUrl(); ?>/port/report/ajaxdata",
         type: type,
         data: data,
         success: function(response) {
             console.log(response);
         }
     });

     return false;
 });

 function chk()
        {
            var year=document.getElementByName('year').value;
            var month=document.getElementByName('month').value;
            var dataString='year='+ year + 'month='+ month;
            $.ajax({
                type:"post",
                url: "/port/report/ajaxdata",
                data:dataString,
                cache:false,
                success: function(html) {
                    $('#msg').html(html);
                }
            });
            return false;
        }

// $(document).ready(function(){
//     alert('Hello');
// });
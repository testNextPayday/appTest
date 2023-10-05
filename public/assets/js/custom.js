$(document).ready(function (){
   var table = $('#myTable').DataTable({
       select: {
            style: 'multi'
        }
   });

   var lots_of_stuff_already_done = false;

   // Handle form submission event
   $('#repayment_form').on('submit', function(e){
      if (lots_of_stuff_already_done) {
        lots_of_stuff_already_done = false; // reset flag
        return; // let the event bubble away
      }
       e.preventDefault();
      var form = this;
      let rows = table.rows( { selected: true } ).data().toArray();
   
      var rowData = table.columns(0).data();
      var data = [];
      var amount = 0;
      console.log(rows);
      var i;
      for (i = 0; i < rows.length; i++) { 
        data.push(rows[i][0]);
        var newAmount= parseInt(rows[i][6].replace(/\D/g,''));
        amount += newAmount;
      }
      console.log(amount);
      
      $('#repayments').val(data);
      $('#metadata').val(data);
      $('#repayment_amount').val(amount);
      lots_of_stuff_already_done = true; // set flag
      $(this).trigger('submit');

      // Iterate over all selected checkboxes
    
   });
   
   $('#payment_method').on('change',function(){
      var value = this.value;
      if( value == "deposit" || value ==  "transfer" || value == "check"){
         $('#payment_proof').css("display", "block");
      }
      else{
         $('#payment_proof').css("display", "none");
      }
   })
});
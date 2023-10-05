$(document).ready(function() {
    var table = $('#repayments-table').DataTable({

        select: {
            style: 'multi'
        }
    });

    $('#approve').click(function() {
        console.log('fish');
        var data = [];
        var rows = table.rows({ selected: true }).data().toArray();

        var i;
        for (i = 0; i < rows.length; i++) {

            data.push(rows[i][0]);
        }

        var input = $('#repayments-input').val(data);

        $('#repayment-form').submit();
    });


    $('#delete').click(function() {

        var data = [];
        var rows = table.rows({ selected: true }).data().toArray();

        var i;
        for (i = 0; i < rows.length; i++) {

            data.push(rows[i][0]);
        }

        var input = $('#repayments-delete-input').val(data);

        $('#repayment-deletion-form').submit();
    });


});
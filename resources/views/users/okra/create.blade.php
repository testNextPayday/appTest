
<button onclick="connectViaOptions()" type="button"> Click Here </button>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.okra.ng/v2/bundle.js"></script>







<script>
function connectViaOptions(){
       
  Okra.buildWithOptions({
      name: 'nextpayday ',
      env: 'production-sandbox',
      key: '6904f7d5-6534-5e90-accd-44c3afd12b73',
      token: '60c1f0e143df4a3004d96b7c', 
      source: 'integration',
      color:  '#3AB795',
      limit: '3',
      corporate: null,
      connectMessage: 'Select an account to continue',
      products: ["auth","identity","balance","transactions","income"],
        
        //debitLater: true,
        
        debitType:'one-time',
         //optional recurring interval    
        
        callback_url: 'null',
        redirect_url: '',
        logo: 'https://dash.okra.ng/static/media/okra-logo.514fd943.png',
        institutions: ["first-bank-of-nigeria","united-bank-for-africa","guaranty-trust-bank","access-bank","zenith-bank","kuda-bank","stanbic-ibtc-bank","first-city-monument-bank"],
        widget_success: 'Your account was successfully linked to nextpayday ',
        widget_failed: 'An unknown error occurred, please try again.',
        currency: 'NGN', 
        mode: 'primary', 
        continue_cta: 'Continue', 
        multi_account: 'true',
        exp: null, 
        
        success_title:'successfully linked to nextpayday !',
        success_message: 'With your bank account linked, you would be able to access the best financial services & products.',
        auth: {"manual":false,"debitLater":false,"debitType":"one-time"},
        balance: {"showBalance":false,"enableAutoConnect":true},
        onSuccess: function(data){
            //alert(data['balance']['clientId']);
            console.log('success', data)
        },
        onClose: function(){
            console.log('closed')
        }
    })
    }

</script>


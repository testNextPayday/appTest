<footer class="app-footer">
    <init :user="{{Auth::guard('web')->user()}}"></init>
    <span><a href="#">Nextpayday</a> © {{date('Y')}}</span>
    <!--<span class="ml-auto">Powered by <a href="http://olotusquare.co" target="_blank">Olotusquare</a></span>-->
</footer>
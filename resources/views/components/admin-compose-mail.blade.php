<div class="row justify-content-center">
    <div class="col-sm-8">
        <form action="{{route('admin.mails.send')}}" method="post">
            @csrf
            <div class="form-group">
                <label class="form-control-label">Mail Subject</label>
                <input type="text" name="mail_subject" class="form-control" placeholder=" Enter Mail Subject">
            </div>

            <div class="form-group">
                <label class="form-control-label">Senders Email</label>
                <input type="text" name="senders_email" class="form-control" placeholder=" Enter Senders Email">
            </div>

            <div class="form-group">
                <label class="form-control-label">Mail Content</label>
                <textarea name="mail_content" class="form-control" rows="20"></textarea>
            </div>
               {{$userType}}
                {{$slot}}
            <div class="row">
                <div class="form-group col-md-6">
                    <input type="submit" class="btn btn-primary" value=" Send Mail">
                </div>
            </div>
        </form>
    </div>
</div>
<div >
    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#replyForm" aria-expanded="false" aria-controls="replyForm" style="width:100%;">
        Reply
    </button>

    <div class="collapse p-2" id="replyForm">
        <form action="{{$url}}" method="POST" enctype="multipart/form-data">
            {{csrf_field()}}

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name" class="form-control-label" class="text-bold">Name : </label>
                    <input type="text" class="form-control" disabled value="{{$user->name}}">
                </div>

                <div class="form-group col-md-6">
                    <label for="name" class="form-control-label" class="text-bold">Email : </label>
                    <input type="text"  class="form-control" disabled value="{{$user->email}}">
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    <label for="message" class="form-control-label" class="text-bold">Message</label>
                    <textarea name="message" class="form-control" rows="10"></textarea>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-6">
                    <label for="attachment">Upload an Attachment</label>
                    <input type="file" class="form-control" name="file">
                </div>
            </div>

            <div class="row">

                <div class="col-md-12 text-center">
                    
                    <button class="btn btn-outline-primary" type="button" data-toggle="collapse" data-target="#replyForm" aria-expanded="false" aria-controls="replyForm">
                        Cancel
                    </button>
                    <button class="btn btn-primary" type="submit">
                        Submit
                    </button>
                    
                </div>
            </div>

            
        </form>
    </div>
</div>
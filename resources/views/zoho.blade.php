<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="_token" content="{!! csrf_token() !!}" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <title>Zoho Crm</title>
    </head>
    <body>
        <div class="container">
            @if(session()->has('auth_success'))                                
                <div class="container mt-5">
                    <h1 class="title mt-5 text-center">Choose Category</h1>

                    <div class="alert alert-success">
                        {{ session()->get('auth_success') }}
                    </div>
                    
                    <h4 class="title mt-5 text-left"><a href="{{route('deals')}}">Deals</a></h4>
                </div>
            @else
                <div class="container mt-5"> 
                    <h1 class="title mt-5 text-center">Auth</h1>
                    @if(session()->has('auth_error'))
                        <div class="alert alert-danger">
                            {{ session()->get('auth_error') }}
                        </div>
                    @endif
                    <form action="/zohoauth" method="post">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="client_id">Client Id</label>
                            <input type="text" class="form-control" id="client_id" value="{{ \Session::get('client_id') }}" name="client_id" placeholder="Enter Client Id" required="required">
                        </div>
                        <div class="form-group">
                            <label for="client_secret">Client Secret</label>
                            <input type="text" class="form-control" value="{{ \Session::get('client_secret') }}" id="client_secret" name="client_secret" placeholder="Enter Client Secret" required="required">
                        </div>
                        <div class="form-group">
                            <label for="redirect_uri">Redirect Uri</label>
                            <input type="text" value="{{ \Session::get('redirect_uri') }}" class="form-control" id="redirect_uri" name="redirect_uri" placeholder="Enter Redirect URI" required="required">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            @endif
        </div>
        
    </body>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script>
     $("document").ready(function(){
       setTimeout(function(){
         $("div.alert").remove();
       }, 5000);

     });
    </script>
</html>


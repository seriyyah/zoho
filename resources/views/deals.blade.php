<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <title>Deals</title>
    </head>
    <body>
        <div class="container">
            <div class="mt-5 text-right">
                <a href="{{route('/')}}" class="btn btn-primary">Back</a>
            </div>
            <h1 class="title mt-1 text-center">Deals</h1>

            <h3 class="title mt-5">Add a deal</h3>
            <div class="mt-5">
                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif
                <form action="adddeal" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="deal_name">Deal Name</label>
                        <input type="text" class="form-control" id="deal_name" name="deal_name" placeholder="Enter Deal Name">
                    </div>
                    <div class="form-group">
                        <label for="account_name">Account</label>
                        <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Enter Account Name">
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Amount">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>

            <h3 class="title mt-5 mb-5">Deals</h3>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Deal Name</th>
                        <th scope="col">Account Name</th>
                        <th scope="col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deals as $deal)
                        <tr>
                            <td>{{ $deal['Deal_Name'] }}</td>
                            <td>{{ $deal['Account_Name']['name'] }}</td>
                            <td>{{ $deal['Amount'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </body>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="text/javascript">
     $("document").ready(function(){
       setTimeout(function(){
         $("div.alert").remove();
       }, 5000);

     });
    </script>
</html>

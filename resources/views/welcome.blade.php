<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
<div class="container mt-5">
    <form action="result" method="POST">
        {{ csrf_field() }}
        <div class="form-group">
          <label for="Deal_Name">Deal Name</label>
          <input type="text" class="form-control" id="Deal_Name" name="Deal_Name" aria-describedby="emailHelp" placeholder="Enter Deal Name">

        </div>
        <div class="form-group">
          <label for="Account_Name">Account Name</label>
          <input type="text" class="form-control" id="Account_Name" name="Account_Name" placeholder="Account Name">
        </div>
        <div class="form-group">
            <label for="Amount">Amount</label>
            <input type="number" class="form-control" id="Amount" name="Amount" placeholder="Amount">
          </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>

</div>

</body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</html>

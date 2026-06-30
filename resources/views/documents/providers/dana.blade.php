<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Insurance Policy</title>
    <style>
        body{font-family: DejaVu Sans}
        .container{width:100%}
        .header{font-size:24px;margin-bottom:20px}
        .row{margin-bottom:10px}
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        Insurance Policy
    </div>

    <div class="row">
        Policy Number: {{ $policy->policy_number }}
    </div>

    <div class="row">
        Status: {{ $policy->status }}
    </div>

    <div class="row">
        Issued At: {{ $policy->issued_at }}
    </div>

    <div class="row">
        Customer ID: {{ $policy->customer_id }}
    </div>

    <div class="row">
        Premium: {{ $policy->premium }}
    </div>

</div>

</body>
</html>

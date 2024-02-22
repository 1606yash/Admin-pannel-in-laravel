<!-- resources/views/salary.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Details</title>
</head>
<style>
    /* Add this to your CSS file or within a style tag in your HTML file */

    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
    }

    #salary-block {
        width: 80%;
        margin: auto;
    }

    .card {
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 20px;
    }

    .border {
        border: 1px solid #dee2e6;
    }

    .rounded-3 {
        border-radius: 0.3rem;
    }

    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }

    .float-none {
        float: none !important;
    }

    legend {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    h5 {
        margin-bottom: 1rem;
    }

    /* Add more styles as needed */
</style>

<body>
    <div id="salary-block" class="card mt-3">
        <div class="card-body">
            <fieldset class="border rounded-3 p-3 card  mt-3 ">
                <legend class="float-none w-auto px-3">
                    <h5>Earnings</h5>
                </legend>
                <div class="card-body">
                    <div class="form-row">
                        <table class="table table-borderless">
                            <thead>
                                <th></th>
                                <th>Amount in Rs</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Basic</td>
                                    <td><span>{{ number_format($data['basic_salary'], 2) ?? '' }}</span></td>
                                </tr>
                                <tr>
                                    <td>HRA</td>
                                    <td><span>{{ number_format($data['house_rent_allowance'], 2) ?? '' }}</span></td>
                                </tr>
                                <tr>
                                    <td>Conveyance</td>
                                    <td><span>{{ number_format($data['conveyance_allowance'], 2) ?? '' }}</span></td>
                                </tr>
                                <tr>
                                    <td>Special Allowance</td>
                                    <td><span>{{ number_format($data['special_allowances'], 2) ?? '' }}</span></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <hr>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><strong>{{ number_format($data['gross_salary'], 2) ?? '' }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </fieldset>
            <fieldset class="border rounded-3 p-3 card  mt-3 ">
                <legend class="float-none w-auto px-3">
                    <h5>Deductions</h5>
                </legend>
                <div class="card-body">
                    <div class="form-row">
                        <table class="table table-borderless">
                            <thead>
                                <th></th>
                                <th>Amount in Rs</th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>PF</td>
                                    <td><span>{{ number_format($data['pf_contribution'], 2) ?? '' }}</span></td>
                                </tr>
                                <tr>
                                    <td>Professional Tax</td>
                                    <td><span>{{ number_format($data['professional_tax'], 2) ?? '' }}</span></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <hr>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td><span><strong>{{ number_format(($data['pf_contribution'] + $data['professional_tax']), 2) ?? '' }}</strong></span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </fieldset>
            @php
            $formattedDate = \Carbon\Carbon::createFromDate($data['year'], $data['month'], 1)->format('M, Y');
            @endphp
            <fieldset class="border rounded-3 p-3 card  mt-3 ">
                <legend class="float-none w-auto px-3">
                    <h5>Net payment for {{ $formattedDate ?? ''}}</h5>
                </legend>
                <div class="card-body">
                    <div class="form-row">
                        <table class="table table-borderless">
                            <tr>
                                <td>{{ number_format($data['net_payable_amount'], 2) ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>{{Helpers::convertFiguresIntoWords($data['net_payable_amount']) ?? ''}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</body>

</html>
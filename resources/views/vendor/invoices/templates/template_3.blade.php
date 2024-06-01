<!DOCTYPE html>
<html lang="zxx">
<head>
    <title>DISEE - Invoice HTML5 Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="fonts/font-awesome/css/font-awesome.min.css">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" >

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="css/style.css">

</head>
<body>

<!-- Invoice 3 start -->
<div class="invoice-3 invoice-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner">
                    <div class="invoice-info" id="invoice_wrapper">
                        <div class="invoice-headar">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="invoice-name">
                                        <!-- logo started -->
                                        <div class="logo">
                                            <img src="img/logos/logo.png" alt="logo">
                                        </div>
                                        <!-- logo ended -->
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="invoice">
                                        <h1 class="text-end inv-header-1 mb-0">Invoice No: #45613</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-top">
                            <div class="row">
                                <div class="col-sm-6 mb-30">
                                    <div class="invoice-number">
                                        <h4 class="inv-title-1">Invoice To</h4>
                                        <p class="invo-addr-1 mb-0">
                                            Theme Vessel <br/>
                                            info@themevessel.com <br/>
                                            21-12 Green Street, Meherpur, Bangladesh <br/>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-6 mb-30">
                                    <div class="invoice-number text-end">
                                        <h4 class="inv-title-1">Bill To</h4>
                                        <p class="invo-addr-1 mb-0">
                                            Apexo Inc  <br/>
                                            billing@apexo.com <br/>
                                            169 Teroghoria, Bangladesh <br/>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 mb-30">
                                    <h4 class="inv-title-1">Date</h4>
                                    <p class="inv-from-1 mb-0">Due Date: 21/09/2021</p>
                                </div>
                                <div class="col-sm-6 text-end mb-30">
                                    <h4 class="inv-title-1">Payment Method</h4>
                                    <p class="inv-from-1 mb-0">Credit Card</p>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-center">
                            <div class="order-summary">
                                <h4>Order summary</h4>
                                <div class="table-outer">
                                    <table class="default-table invoice-table">
                                        <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th>Price</th>
                                            <th>VAT (20%)</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Standard Plan</td>
                                            <td>$443.00 </td>
                                            <td>$921.80</td>
                                            <td>$9243</td>
                                        </tr>
                                        <tr>
                                            <td>Extra Plan</td>
                                            <td>$413.00 </td>
                                            <td>$912.80 </td>
                                            <td>$5943</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Due</strong></td>
                                            <td></td>
                                            <td></td>
                                            <td><strong>$9,750</strong></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-bottom">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="important-note mb-30">
                                        <h3 class="inv-title-1">Important Note</h3>
                                        <ul class="important-notes-list-1">
                                            <li>Once order done, money can't refund</li>
                                            <li>Delivery might delay due to some external dependency</li>
                                            <li>This is computer generated invoice and physical signature does not require.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-offsite">
                                    <div class="text-end payment-info mb-30">
                                        <h3 class="inv-title-1">Payment Info</h3>
                                        <p class="mb-0 text-13">This payment made by BRAC BANK master card without any problem</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-btn-section clearfix d-print-none">
                        <a href="javascript:window.print()" class="btn btn-lg btn-print">
                            <i class="fa fa-print"></i> Print Invoice
                        </a>
                        <a id="invoice_download_btn" class="btn btn-lg btn-download btn-theme">
                            <i class="fa fa-download"></i> Download Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Invoice 3 end -->

<script src="js/jquery.min.js"></script>
<script src="js/jspdf.min.js"></script>
<script src="js/html2canvas.js"></script>
<script src="js/app.js"></script>
</body>
</html>
